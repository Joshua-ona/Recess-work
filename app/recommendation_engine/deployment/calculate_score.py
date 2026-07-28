from datetime import datetime
import math
import os

from dotenv import load_dotenv
from sqlalchemy import text

from database import get_connection


load_dotenv()


WEIGHTS = {
    'discussion': 1,
    'reply': 1,
    'group_join': 2,
    'group_message': 2
}

DECAY_RATE = 0.02


def to_datetime(val):

    if isinstance(val, datetime):
        return val

    if isinstance(val, str):
        return datetime.strptime(
            val,
            '%Y-%m-%d %H:%M:%S'
        )

    return datetime(2000, 1, 1)



def run_batch_scoring():

    engine = get_connection()

    conn = engine.connect()

    print("Running score calculation...")


    query = text("""
        SELECT 
            u.id,

            COUNT(DISTINCT d.id) AS discussions,
            COUNT(DISTINCT r.id) AS replies,
            COUNT(DISTINCT gm.group_id) AS groups_joined,
            COUNT(DISTINCT gms.id) AS group_messages,

            GREATEST(
                COALESCE(MAX(d.created_at), '2000-01-01 00:00:00'),
                COALESCE(MAX(r.created_at), '2000-01-01 00:00:00'),
                COALESCE(MAX(gm.created_at), '2000-01-01 00:00:00'),
                COALESCE(MAX(gms.created_at), '2000-01-01 00:00:00'),
                COALESCE(MAX(u.updated_at), '2000-01-01 00:00:00')
            ) AS last_active

        FROM users u

        LEFT JOIN discussions d 
            ON d.user_id = u.id

        LEFT JOIN replies r 
            ON r.user_id = u.id

        LEFT JOIN group_members gm 
            ON gm.user_id = u.id

        LEFT JOIN group_messages gms 
            ON gms.user_id = u.id

        GROUP BY u.id
    """)


    users = conn.execute(query).mappings().all()


    for user in users:


        base = (
            user['discussions'] * WEIGHTS['discussion']
            +
            user['replies'] * WEIGHTS['reply']
            +
            user['groups_joined'] * WEIGHTS['group_join']
            +
            user['group_messages'] * WEIGHTS['group_message']
        )


        last_active = to_datetime(
            user['last_active']
        )


        days_inactive = (
            datetime.now() - last_active
        ).days


        score = round(
            base * math.exp(
                -DECAY_RATE * max(0, days_inactive)
            ),
            2
        )


        update_query = text("""
            INSERT INTO user_scores
            (
                user_id,
                score,
                updated_at
            )

            VALUES
            (
                :user_id,
                :score,
                NOW()
            )

            ON CONFLICT (user_id)

            DO UPDATE SET

                score = EXCLUDED.score,

                updated_at = NOW()
        """)


        conn.execute(
            update_query,
            {
                "user_id": user['id'],
                "score": score
            }
        )


        print(f"User: {user['id']}")
        print(f"Score: {score}")
        print("-" * 30)


    conn.commit()

    conn.close()

    print("Score calculation done")



if __name__ == "__main__":

    run_batch_scoring()