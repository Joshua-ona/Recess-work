
import mysql.connector
from datetime import datetime
import math


DB_CONFIG = {
    'host': '127.0.0.1',
    'user': 'root',
    'password': '',
    'database': 'fave'
}


WEIGHTS = {
    'discussion': 5,
    'reply': 3,
    'group_join': 3,
    'group_message': 2
}

DECAY_RATE = 0.02


def main():

    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)

        cursor.execute("""
            SELECT 
                u.id,

                COALESCE(d.discussions, 0) AS discussions,
                COALESCE(r.replies, 0) AS replies,
                COALESCE(gm.groups_joined, 0) AS groups_joined,
                COALESCE(gmsg.group_messages, 0) AS group_messages,

                GREATEST(
                    COALESCE(d.last_activity, '2000-01-01'),
                    COALESCE(r.last_activity, '2000-01-01'),
                    COALESCE(gm.last_activity, '2000-01-01'),
                    COALESCE(gmsg.last_activity, '2000-01'),
                    u.updated_at
                ) AS last_active

            FROM users u

            LEFT JOIN (
                SELECT 
                    user_id,
                    COUNT(*) AS discussions,
                    MAX(created_at) AS last_activity
                FROM discussions
                GROUP BY user_id
            ) d ON d.user_id = u.id


            LEFT JOIN (
                SELECT
                    user_id,
                    COUNT(*) AS replies,
                    MAX(created_at) AS last_activity
                FROM replies
                GROUP BY user_id
            ) r ON r.user_id = u.id


            LEFT JOIN (
                SELECT
                    user_id,
                    COUNT(*) AS groups_joined,
                    MAX(created_at) AS last_activity
                FROM group_members
                GROUP BY user_id
            ) gm ON gm.user_id = u.id


            LEFT JOIN (
                SELECT
                    user_id,
                    COUNT(*) AS group_messages,
                    MAX(created_at) AS last_activity
                FROM group_messages
                GROUP BY user_id
            ) gmsg ON gmsg.user_id = u.id

        """)


        users = cursor.fetchall()


        for user in users:

            base_score = (
                user['discussions'] * WEIGHTS['discussion']
                +
                user['replies'] * WEIGHTS['reply']
                +
                user['groups_joined'] * WEIGHTS['group_join']
                +
                user['group_messages'] * WEIGHTS['group_message']
            )


            last_active = user['last_active']

            if isinstance(last_active, str):
                last_active = datetime.strptime(
                    last_active,
                    "%Y-%m-%d %H:%M:%S"
                )


            days_inactive = (
                datetime.now() - last_active
            ).days


            score = round(
                base_score *
                math.exp(
                    -DECAY_RATE * max(0, days_inactive)
                ),
                2
            )


            cursor.execute("""
                INSERT INTO user_scores
                (
                    user_id,
                    score,
                    created_at,
                    updated_at
                )

                VALUES
                (
                    %s,
                    %s,
                    NOW(),
                    NOW()
                )

                ON DUPLICATE KEY UPDATE
                    score = VALUES(score),
                    updated_at = NOW()

            """, (
                user['id'],
                score
            ))


            print(
                f"User {user['id']} => Score: {score}"
            )


        conn.commit()

        print("\nScore calculation completed successfully")


    except mysql.connector.Error as error:

        print(
            "Database error:",
            error
        )


    finally:

        if 'cursor' in locals():
            cursor.close()

        if 'conn' in locals() and conn.is_connected():
            conn.close()



if __name__ == "__main__":
    main()
