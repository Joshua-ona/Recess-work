import mysql.connector
from datetime import datetime
import math

DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',  
    'database': 'discussion_forum'
}

WEIGHTS = {
    'discussion': 5,      # starting a discussion
    'reply': 3,           # replying
    'group_join': 10,     # joining a group
    'group_message': 2    # messaging in group
}
DECAY_RATE = 0.02 

def main():
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor(dictionary=True)
    
    cursor.execute("""
        SELECT u.id, 
               COUNT(DISTINCT d.id) as discussions,
               COUNT(DISTINCT r.id) as replies,
               COUNT(DISTINCT gm.group_id) as groups_joined,
               COUNT(DISTINCT gms.id) as group_messages,
               GREATEST(
                   IFNULL(MAX(d.created_at), '2000-01-01'), 
                   IFNULL(MAX(r.created_at), '2000-01-01'), 
                   IFNULL(MAX(gm.created_at), '2000-01-01'),
                   IFNULL(MAX(gms.created_at), '2000-01-01'),
                   IFNULL(MAX(u.updated_at), '2000-01-01')
               ) as last_active
        FROM users u
        LEFT JOIN discussions d ON d.user_id = u.id
        LEFT JOIN replies r ON r.user_id = u.id  
        LEFT JOIN group_members gm ON gm.user_id = u.id
        LEFT JOIN group_messages gms ON gms.user_id = u.id
        GROUP BY u.id
    """)
    
    for user in cursor.fetchall():
        base = (user['discussions'] * WEIGHTS['discussion'] + 
                user['replies'] * WEIGHTS['reply'] +
                user['groups_joined'] * WEIGHTS['group_join'] +
                user['group_messages'] * WEIGHTS['group_message'])
        
        last_active = user['last_active']
        days_inactive = (datetime.now() - last_active).days
        score = round(base * math.exp(-DECAY_RATE * max(0, days_inactive)), 2)
        
        # Upsert into user_scores table
        cursor.execute("""
            INSERT INTO user_scores (user_id, score, updated_at) 
            VALUES (%s, %s, NOW()) 
            ON DUPLICATE KEY UPDATE score = %s, updated_at = NOW()
        """, (user['id'], score, score))
        
        print(f"User {user['id']}: Score = {score}")
    
    conn.commit()
    cursor.close()
    conn.close()
    print("Score calculation done")