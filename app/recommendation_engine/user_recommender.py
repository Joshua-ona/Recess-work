import logging
from datetime import datetime

import pandas as pd
from sqlalchemy import text
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.feature_extraction.text import TfidfVectorizer

from database import get_connection
from content_model import create_content_similarity


logging.basicConfig(level=logging.INFO)


def get_user_groups(user_id):
    """
    Get groups a student belongs to.
    """

    engine = get_connection()

    try:
        query = text("""
            SELECT group_id
            FROM group_members
            WHERE user_id = :user_id
        """)

        with engine.connect() as conn:
            df = pd.read_sql(
                query,
                conn,
                params={"user_id": user_id}
            )

        return df["group_id"].tolist() if not df.empty else []

    finally:
        engine.dispose()



def recommend_for_user(user_id, limit=5):
    """
    Recommend discussions based on:
    - content similarity
    - engagement
    - freshness
    - author reputation
    """

    engine = get_connection()

    try:

        user_groups = get_user_groups(user_id)


        # Student has no interests yet
        if not user_groups:
            return {
                "status": "cold_start",
                "message": "Join groups and participate in discussions to receive recommendations.",
                "recommendations": []
            }


        placeholders = ",".join(
            [f":g{i}" for i in range(len(user_groups))]
        )


        params = {
            "user_id": user_id
        }

        params.update(
            {
                f"g{i}": gid
                for i, gid in enumerate(user_groups)
            }
        )


        query = text(f"""
            SELECT
                d.id,
                d.title,
                d.body,
                d.group_id,
                g.name AS group_name,
                d.created_at,
                COUNT(r.id) AS replies,
                COALESCE(us.score,0) AS author_score

            FROM discussions d

            JOIN groups g
            ON g.id = d.group_id

            LEFT JOIN replies r
            ON r.discussion_id = d.id

            LEFT JOIN user_scores us
            ON us.user_id = d.user_id

            WHERE d.group_id IN ({placeholders})

            AND d.user_id <> :user_id

            AND d.id NOT IN
            (
                SELECT discussion_id
                FROM replies
                WHERE user_id = :user_id
            )

            GROUP BY
                d.id,
                g.name,
                d.title,
                d.body,
                d.group_id,
                d.created_at

        """)


        with engine.connect() as conn:

            discussions = pd.read_sql(
                query,
                conn,
                params=params
            )


        if discussions.empty:

            return {
                "status": "success",
                "recommendations": []
            }


        similarity = create_content_similarity(discussions)

        results = []

        now = datetime.now()


        for index,row in discussions.iterrows():

            score = 40


            # Topic similarity
            score += similarity[index].mean() * 25


            # Engagement
            score += min(
                int(row["replies"]) * 3,
                15
            )


            # Freshness
            if pd.isna(row["created_at"]):

                days_old = 999

            else:

                days_old = (
                    now - row["created_at"]
                ).days


            if days_old <= 1:
                score += 10

            elif days_old <= 7:
                score += 5



            # Author reputation

            score += (
                float(row["author_score"])
                / 100
            ) * 10



            results.append({

                "id": int(row["id"]),

                "title": row["title"],

                "body": row["body"],

                "group_id": int(row["group_id"]),

                "group_name": row["group_name"],

                "score": round(score,2)

            })


        results.sort(
            key=lambda x:x["score"],
            reverse=True
        )


        return {

            "status":"success",

            "recommendations":
                results[:limit]

        }



    except Exception as e:

        logging.error(
            f"Recommendation error: {e}"
        )

        return {

            "status":"error",

            "message":
            "Unable to generate recommendations",

            "recommendations":[]

        }


    finally:

        engine.dispose()



def recommend_groups_for_user(
        user_id,
        top_n=3,
        min_match=15
):

    """
    Recommend learning groups using
    TF-IDF similarity between groups.
    """

    engine = get_connection()


    try:

        with engine.connect() as conn:


            discussions = pd.read_sql(
                """
                SELECT title, body
                FROM discussions
                WHERE user_id=%s
                """,
                conn,
                params=(user_id,)
            )


            replies = pd.read_sql(
                """
                SELECT
                    d.title,
                    d.body

                FROM replies r

                JOIN discussions d
                ON d.id=r.discussion_id

                WHERE r.user_id=%s

                """,
                conn,
                params=(user_id,)
            )


            profile = pd.concat(
                [
                    discussions,
                    replies
                ],
                ignore_index=True
            )


            # Cold start
            if profile.empty:

                return {

                    "status":"cold_start",

                    "message":
                    "Participate in discussions to unlock personalized group recommendations.",

                    "recommendations":[]

                }



            groups = pd.read_sql(
                """
                SELECT id,name,description
                FROM groups
                WHERE status='approved'
                """,
                conn
            )



            texts=[]

            ids=[]


            for _,group in groups.iterrows():

                texts.append(
                    f"{group['name']} {group['description']}"
                )

                ids.append(
                    group["id"]
                )



            vectorizer=TfidfVectorizer(
                stop_words="english",
                ngram_range=(1,2),
                max_features=3000
            )


            matrix = vectorizer.fit_transform(texts)


            similarity = cosine_similarity(
                matrix
            )



            user_groups=get_user_groups(
                user_id
            )



            candidates={}


            for gid in user_groups:

                if gid not in ids:
                    continue


                index=ids.index(gid)


                for i,score in enumerate(
                    similarity[index]
                ):

                    other=ids[i]


                    if other in user_groups:
                        continue


                    candidates[other]=(
                        candidates.get(other,0)
                        +
                        score
                    )



            results=[]


            for gid,score in candidates.items():

                if score*100 < min_match:
                    continue


                group=groups[
                    groups["id"]==gid
                ].iloc[0]


                results.append({

                    "id":int(gid),

                    "name":group["name"],

                    "description":
                    group["description"],

                    "score":
                    int(score*100),

                    "reason":
                    "Similar to groups you participate in"

                })



            results.sort(
                key=lambda x:x["score"],
                reverse=True
            )


            return {

                "status":"success",

                "recommendations":
                results[:top_n]

            }


    except Exception as e:

        logging.error(e)

        return {

            "status":"error",

            "recommendations":[]

        }


    finally:

        engine.dispose()
