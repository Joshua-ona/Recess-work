import pandas as pd
from datetime import datetime
from sqlalchemy import text
from sklearn.metrics.pairwise import cosine_similarity

from database import get_connection
from content_model import create_content_similarity
from sklearn.feature_extraction.text import TfidfVectorizer
import numpy as np

def get_user_groups(user_id):
    engine = get_connection()
    query = text("SELECT group_id FROM group_members WHERE user_id = :user_id")
    with engine.connect() as conn:
        df = pd.read_sql(query, conn, params={"user_id": user_id})
    engine.dispose()
    return df["group_id"].tolist() if not df.empty else []

def recommend_for_user(user_id, limit=5):
    engine = get_connection()
    user_groups = get_user_groups(user_id)

    if not user_groups:
        engine.dispose()
        return []

    placeholders = ",".join([f":g{i}" for i in range(len(user_groups))])
    params = {"user_id": user_id}
    params.update({f"g{i}": gid for i, gid in enumerate(user_groups)})

    query = text(f"""
        SELECT d.id, d.title, d.body, d.group_id, g.name AS group_name, d.created_at,
               COUNT(r.id) AS replies, COALESCE(us.score, 0) AS author_score
        FROM discussions d
        JOIN groups g ON g.id = d.group_id
        LEFT JOIN replies r ON r.discussion_id = d.id
        LEFT JOIN user_scores us ON us.user_id = d.user_id
        WHERE d.group_id IN ({placeholders})
          AND d.user_id <> :user_id
          AND d.id NOT IN (SELECT discussion_id FROM replies WHERE user_id = :user_id)
        GROUP BY d.id, g.name, d.title, d.body, d.group_id, d.created_at
    """)

    with engine.connect() as conn:
        discussions = pd.read_sql(query, conn, params=params)
    discussions = discussions.drop_duplicates(subset=['id'])
    engine.dispose()

    if discussions.empty:
        return []

    similarity = create_content_similarity(discussions)
    recommendations = []
    now = datetime.now()

    for index, row in discussions.iterrows():
        score = 40
        score += similarity[index].mean() * 25
        score += min(int(row["replies"]) * 3, 15)
        
        # FIX HERE: handle NULL dates
        if pd.isna(row["created_at"]):
            days_old = 999
        else:
            days_old = (now - row["created_at"]).days
        score += 10 if days_old <= 1 else 5 if days_old <= 7 else 0
        
        score += (float(row["author_score"]) / 100) * 10

        recommendations.append({
            "id": int(row["id"]), "title": row["title"], "body": row["body"],
            "group_id": int(row["group_id"]), "group_name": row["group_name"],
            "score": round(score, 2)
        })

    recommendations.sort(key=lambda x: x["score"], reverse=True)
    return recommendations[:limit]


def get_user_groups(user_id):
    engine = get_connection()
    query = text("SELECT group_id FROM group_members WHERE user_id = :user_id")
    with engine.connect() as conn:
        df = pd.read_sql(query, conn, params={"user_id": user_id})
    engine.dispose()
    return df["group_id"].tolist() if not df.empty else []

def recommend_groups_for_user(user_id, top_n=3, min_match=15):
    engine = get_connection()

    try:
        with engine.connect() as conn:
            # 1. COLD START: No activity = No recommendations
            my_discussions = pd.read_sql("SELECT title, body FROM discussions WHERE user_id = %s", conn, params=(user_id,))
            my_replies = pd.read_sql("SELECT d.title, d.body FROM replies r JOIN discussions d ON d.id = r.discussion_id WHERE r.user_id = %s", conn, params=(user_id,))
            all_my_text = pd.concat([my_discussions, my_replies], ignore_index=True)
            
            if all_my_text.empty:
                return [] # No fwaaaa trending
            
            my_profile = " ".join(all_my_text['title'].fillna('') + "" + all_my_text['body'].fillna(''))

            user_groups = get_user_groups(user_id)

            # 2. BUILD "GROUP-GROUP SIMILARITY MATRIX" FROM ALL DISCUSSIONS
            # This is the magic. It learns that "React" and "JS" are similar automatically
            all_groups = pd.read_sql("SELECT id, name, description FROM groups WHERE status = 'approved'", conn)
            
            group_texts = []
            group_ids = []
            for _, group in all_groups.iterrows():
                discussions = pd.read_sql("SELECT title, body FROM discussions WHERE group_id = %s", conn, params=(group['id'],))
                if not discussions.empty:
                    text = " ".join(discussions['title'].fillna('') + "" + discussions['body'].fillna(''))
                else:
                    text = str(group['name']) + "" + str(group['description'])
                group_texts.append(text)
                group_ids.append(group['id'])
            
            # Vectorize ALL groups at once
            vectorizer = TfidfVectorizer(stop_words='english', max_features=3000, ngram_range=(1,2))
            group_tfidf = vectorizer.fit_transform(group_texts)
            
            # This matrix says: "Group 1 is 80% similar to Group 5"
            group_similarity_matrix = cosine_similarity(group_tfidf, group_tfidf)

            # 3. GET GROUPS USER HAS ACTIVITY IN
            if not user_groups:
                # If no groups joined, find groups where user posted
                user_activity_group_ids = pd.read_sql("SELECT DISTINCT group_id FROM discussions WHERE user_id = %s", conn, params=(user_id,))['group_id'].tolist()
            else:
                user_activity_group_ids = user_groups

            if not user_activity_group_ids:
                return []

            # 4. SCORE OTHER GROUPS BASED ON SIMILARITY TO MY GROUPS
            candidate_scores = {}
            for my_gid in user_activity_group_ids:
                if my_gid not in group_ids: continue
                my_idx = group_ids.index(my_gid)
                
                # Get similarity scores from my group to all other groups
                sim_scores = list(enumerate(group_similarity_matrix[my_idx]))
                
                for idx, score in sim_scores:
                    other_gid = group_ids[idx]
                    if other_gid in user_groups: continue # skip joined
                    if other_gid not in candidate_scores:
                        candidate_scores[other_gid] = 0
                    candidate_scores[other_gid] += score # Sum similarity from all my groups

            if not candidate_scores:
                return []

            # 5. CONVERT TO FINAL RESULTS
            df_groups = pd.DataFrame(all_groups)
            df_groups['score'] = df_groups['id'].map(lambda x: candidate_scores.get(x, 0) * 100)
            df_groups['discussion_count'] = df_groups['id'].map(lambda x: pd.read_sql("SELECT COUNT(*) as c FROM discussions WHERE group_id=%s", conn, params=(x,)).iloc[0]['c'])
            
            df_groups = df_groups[df_groups['score'] >= min_match]
            df_groups = df_groups.sort_values('score', ascending=False).head(top_n)
            
            results = []
            for _, row in df_groups.iterrows():
                results.append({
                    'id': int(row['id']), 
                    'name': row['name'], 
                    'description': row['description'],
                    'score': int(row['score']), 
                    'reason': 'Similar to groups you are active in', # Auto reason
                    'discussion_count': int(row['discussion_count'])
                })
            return results

    except Exception as e:
        print(f"Error: {e}")
        return []
    finally:
        engine.dispose()

def _get_trending_discussions(user_id, engine, limit):
    query = text("""
        SELECT d.id, d.title, d.body, d.group_id, g.name AS group_name, d.created_at,
               COUNT(r.id) AS replies, COALESCE(us.score, 0) AS author_score
        FROM discussions d
        JOIN groups g ON g.id = d.group_id
        LEFT JOIN replies r ON r.discussion_id = d.id
        LEFT JOIN user_scores us ON us.user_id = d.user_id
        WHERE d.user_id <> :user_id
        GROUP BY d.id, g.name, d.title, d.body, d.group_id, d.created_at
        ORDER BY replies DESC, d.created_at DESC
        LIMIT :limit
    """)
    with engine.connect() as conn:
        df = pd.read_sql(query, conn, params={"user_id": user_id, "limit": limit})
    df = df.drop_duplicates(subset=['id'])
    return [{"id": int(r.id), "title": r.title, "body": r.body, "group_id": int(r.group_id),
             "group_name": r.group_name, "score": round(50 + int(r.replies) * 2, 2)} for _, r in df.iterrows()]

def _get_trending_groups(engine, limit):
    query = text("""
        SELECT g.id, g.name, g.description, COUNT(d.id) as discussion_count
        FROM groups g
        LEFT JOIN discussions d ON d.group_id = g.id
        WHERE g.status = 'approved'
        GROUP BY g.id
        ORDER BY discussion_count DESC
        LIMIT :limit
    """)
    with engine.connect() as conn:
        df = pd.read_sql(query, conn, params={"limit": limit})
    return [{"id": int(r.id), "name": r.name, "description": r.description,
             "discussion_count": int(r.discussion_count), "score": 50, "reason": "Trending group"}
            for _, r in df.iterrows()]