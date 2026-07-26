package com.edudiscuss.database;

import java.sql.Connection;
import java.sql.Statement;

public class DatabaseInitializer {

    public static void initialize() {

        try (
                Connection conn =
                        DatabaseManager.getConnection();

                Statement stmt =
                        conn.createStatement()
        ) {

            stmt.execute("""
                CREATE TABLE IF NOT EXISTS discussions (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT,
                    content TEXT,
                    synced INTEGER DEFAULT 0,
                    updated_at TEXT
                )
            """);

            stmt.execute("""
                CREATE TABLE IF NOT EXISTS messages (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    server_id INTEGER,
                    sender_id INTEGER,
                    receiver_id INTEGER,
                    content TEXT,
                    created_at TEXT,
                    updated_at TEXT,
                    is_read INTEGER DEFAULT 0,
                    synced INTEGER DEFAULT 0,
                    status TEXT,
                    deleted INTEGER DEFAULT 0
                )
            """);

            stmt.execute("""
                CREATE TABLE IF NOT EXISTS notifications (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    server_id INTEGER,
                    user_id INTEGER,
                    type TEXT,
                    title TEXT,
                    body TEXT,
                    reference_id INTEGER,
                    created_at TEXT,
                    is_read INTEGER DEFAULT 0,
                    synced INTEGER DEFAULT 0,
                    deleted INTEGER DEFAULT 0
                )
            """);

            stmt.execute("""
                CREATE TABLE IF NOT EXISTS sync_queue (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    entity TEXT,
                    action TEXT,
                    local_id INTEGER,
                    status TEXT DEFAULT 'PENDING',
                    attempts INTEGER DEFAULT 0,
                    error_message TEXT,
                    created_at TEXT,
                    synced_at TEXT
                )
            """);

            System.out.println(
                    "SQLite initialized successfully."
            );

        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}