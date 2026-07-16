import pandas as pd
from datetime import datetime
from sqlalchemy import text

from database import get_connection
from content_model import create_content_similarity

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
        return _get_trending_discussions(user_id, engine, limit)

    placeholders = ",".join([f":g{i}" for i in range(len(user_groups))])
    params = {"user_id": user_id}
    params.update({f"g{i}": gid for i, gid in enumerate(user_groups)})

    query = text(f"""
        SELECT
            d.id, d.title, d.body, d.group_id, g.name AS group_name, d.created_at,
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
        return _get_trending_discussions(user_id, get_connection(), limit)

    similarity = create_content_similarity(discussions)
    recommendations = []
    now = datetime.now()

    for index, row in discussions.iterrows():
        score = 40
        score += similarity[index].mean() * 25
        score += min(int(row["replies"]) * 3, 15)
        days_old = (now - row["created_at"]).days
        score += 10 if days_old <= 1 else 5 if days_old <= 7 else 0
        score += (float(row["author_score"]) / 100) * 10

        recommendations.append({
            "id": int(row["id"]),
            "title": row["title"],
            "body": row["body"],
            "group_id": int(row["group_id"]),
            "group_name": row["group_name"],
            "score": round(score, 2)
        })

    recommendations.sort(key=lambda x: x["score"], reverse=True)
    return recommendations[:limit]
def recommend_groups_for_user(user_id, top_n=3, min_match=75):
    engine = get_connection()

    try:
        with engine.connect() as conn:
            # 1. Get user's discussions + replies - USE %s
            my_discussions = pd.read_sql("SELECT title, body FROM discussions WHERE user_id = %s", conn, params=(user_id,))
            my_replies = pd.read_sql("SELECT d.title, d.body FROM replies r JOIN discussions d ON d.id = r.discussion_id WHERE r.user_id = %s", conn, params=(user_id,))
            
            all_my_text = pd.concat([my_discussions, my_replies], ignore_index=True)
            
            if all_my_text.empty:
                return [] # No activity = no recommendations
            
            my_profile = " ".join(all_my_text['title'].fillna('') + "" + all_my_text['body'].fillna(''))

            # 2. Get all approved groups NOT joined by user
            user_groups = get_user_groups(user_id)
            if user_groups:
                placeholders = ",".join(["%s"] * len(user_groups)) # USE %s
                query = f"SELECT g.id, g.name, g.description FROM groups g WHERE g.status = 'approved' AND g.id NOT IN ({placeholders})"
                groups = pd.read_sql(query, conn, params=tuple(user_groups))
            else:
                query = "SELECT g.id, g.name, g.description FROM groups g WHERE g.status = 'approved'"
                groups = pd.read_sql(query, conn)

            if groups.empty: return []

            # 3. Build group profile
            group_profiles = []
            for _, group in groups.iterrows():
                group_discussions = pd.read_sql("SELECT title, body FROM discussions WHERE group_id = %s", conn, params=(group['id'],))
                
                if not group_discussions.empty:
                    group_text = " ".join(group_discussions['title'].fillna('') + " " + group_discussions['body'].fillna(''))
                else:
                    group_text = str(group['name']) + " " + str(group['description'])
                
                group_profiles.append({
                    'id': group['id'],
                    'name': group['name'],
                    'description': group['description'],
                    'text': group_text,
                    'discussion_count': len(group_discussions)
                })

        df_groups = pd.DataFrame(group_profiles)
        
        # 4. TFIDF Similarity
        from sklearn.feature_extraction.text import TfidfVectorizer
        from sklearn.metrics.pairwise import cosine_similarity
        
        corpus = [my_profile] + df_groups['text'].tolist()
        vectorizer = TfidfVectorizer(stop_words='english')
        tfidf_matrix = vectorizer.fit_transform(corpus)
        
        cosine_sim = cosine_similarity(tfidf_matrix[0:1], tfidf_matrix[1:]).flatten()
        df_groups['similarity'] = cosine_sim
        df_groups['score'] = (df_groups['similarity'] * 100).round(0).astype(int)

        # 5. FILTER: ONLY KEEP >= 75% MATCH
        df_groups = df_groups[df_groups['score'] >= min_match]
        df_groups = df_groups.sort_values('score', ascending=False).head(top_n)
        
        if df_groups.empty:
            return []

        results = []
        for _, row in df_groups.iterrows():
            results.append({
                'id': int(row['id']),
                'name': row['name'],
                'description': row['description'],
                'score': int(row['score']),
                'reason': 'High match based on your activity',
                'discussion_count': int(row['discussion_count'])
            })
        
        return results

    except Exception as e:
        print(f"Error in recommend_groups_for_user: {e}")
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