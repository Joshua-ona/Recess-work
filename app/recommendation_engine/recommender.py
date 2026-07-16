import pandas as pd

from database import get_connection
from content_model import create_model


def load_discussions():

    engine = get_connection()

    query = """
    SELECT
        d.id,
        d.title,
        d.body,
        d.group_id,
        COUNT(r.id) AS replies

    FROM discussions d

    LEFT JOIN replies r
        ON r.discussion_id = d.id

    GROUP BY d.id
    """

    df = pd.read_sql(
        query,
        engine
    )

    engine.dispose()

    return df


def recommend_discussions(
        discussion_id,
        limit=5
):

    df = load_discussions()

    # If no discussions exist
    if df.empty:
        return []


    similarity = create_model(df)


    # Check if discussion exists
    matching = df.index[
        df["id"] == discussion_id
    ]

    if len(matching) == 0:
        return []


    index = matching[0]


    scores = list(
        enumerate(similarity[index])
    )


    scores = sorted(
        scores,
        key=lambda x: x[1],
        reverse=True
    )


    recommendations = []


    for i, score in scores[1:limit+1]:

        recommendations.append({

            "id": int(df.iloc[i]["id"]),

            "title": df.iloc[i]["title"],

            "score": round(
                float(score),
                3
            )
        })


    return recommendations