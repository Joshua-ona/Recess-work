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

            System.out.println(
                    "SQLite initialized successfully."
            );

        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}