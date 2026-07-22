package com.edudiscuss.database;

import java.sql.*;
import java.io.File;

public class DatabaseHelper {
    private static DatabaseHelper instance;
    private Connection connection;
    private final String DB_PATH = System.getProperty("user.dir") + "/edudiscuss.db";

    private DatabaseHelper() {
        try {
            System.out.println("🔵 Initializing Database...");

            try {
                Class.forName("org.sqlite.JDBC");
                System.out.println("✅ SQLite driver found");
            } catch (ClassNotFoundException e) {
                System.out.println("⚠️ Driver not in classpath, trying alternative...");
                ClassLoader cl = Thread.currentThread().getContextClassLoader();
                Class.forName("org.sqlite.JDBC", true, cl);
                System.out.println("✅ SQLite driver loaded");
            }

            connection = DriverManager.getConnection("jdbc:sqlite:" + DB_PATH);
            System.out.println("✅ Database connected at: " + new File(DB_PATH).getAbsolutePath());
            createTables();

        } catch (Exception e) {
            System.err.println("❌ Database initialization failed!");
            e.printStackTrace();
        }
    }

    public static DatabaseHelper getInstance() {
        if (instance == null) {
            instance = new DatabaseHelper();
        }
        return instance;
    }

    private void createTables() {
        if (connection == null) {
            System.out.println("⚠️ No database connection");
            return;
        }

        String[] sqls = {
            "CREATE TABLE IF NOT EXISTS groups (" +
                "id INTEGER PRIMARY KEY, " +
                "name TEXT, " +
                "description TEXT, " +
                "is_joined INTEGER DEFAULT 0)",

            "CREATE TABLE IF NOT EXISTS replies (" +
                "id INTEGER PRIMARY KEY AUTOINCREMENT, " +
                "discussion_id INTEGER, " +
                "user_id INTEGER, " +
                "user_name TEXT, " +
                "content TEXT, " +
                "created_at TEXT, " +
                "is_synced INTEGER DEFAULT 0)"
        };

        for (String sql : sqls) {
            try (Statement stmt = connection.createStatement()) {
                stmt.execute(sql);
            } catch (SQLException e) {
                System.out.println("⚠️ Could not create table: " + e.getMessage());
            }
        }
        System.out.println("✅ Tables ready");
    }

    public void saveGroup(int id, String name, String description, boolean isJoined) {
        if (connection == null) {
            System.out.println("⚠️ Skipping save (no database): " + name);
            return;
        }

        String sql = "INSERT OR REPLACE INTO groups (id, name, description, is_joined) VALUES (?, ?, ?, ?)";
        try (PreparedStatement pstmt = connection.prepareStatement(sql)) {
            pstmt.setInt(1, id);
            pstmt.setString(2, name);
            pstmt.setString(3, description);
            pstmt.setInt(4, isJoined ? 1 : 0);
            pstmt.executeUpdate();
            System.out.println("✅ Saved group: " + name);
        } catch (SQLException e) {
            System.out.println("⚠️ Could not save group: " + e.getMessage());
        }
    }

    public ResultSet getGroups(boolean joined) {
        if (connection == null) {
            System.out.println("⚠️ No database connection");
            return null;
        }
        try {
            String sql = "SELECT * FROM groups WHERE is_joined = ?";
            PreparedStatement pstmt = connection.prepareStatement(sql);
            pstmt.setInt(1, joined ? 1 : 0);
            return pstmt.executeQuery();
        } catch (SQLException e) {
            System.out.println("⚠️ Could not get groups: " + e.getMessage());
            return null;
        }
    }

    public void saveReply(int discussionId, int userId, String userName, String content) {
        if (connection == null) {
            System.out.println("⚠️ No database connection");
            return;
        }
        String sql = "INSERT INTO replies (discussion_id, user_id, user_name, content, is_synced) " +
            "VALUES (?, ?, ?, ?, 0)";
        try (PreparedStatement pstmt = connection.prepareStatement(sql)) {
            pstmt.setInt(1, discussionId);
            pstmt.setInt(2, userId);
            pstmt.setString(3, userName);
            pstmt.setString(4, content);
            pstmt.executeUpdate();
            System.out.println("✅ Reply saved locally");
        } catch (SQLException e) {
            System.out.println("⚠️ Could not save reply: " + e.getMessage());
        }
    }

    public ResultSet getUnsyncedReplies() {
        if (connection == null) {
            System.out.println("⚠️ No database connection");
            return null;
        }
        try {
            String sql = "SELECT * FROM replies WHERE is_synced = 0";
            PreparedStatement pstmt = connection.prepareStatement(sql);
            return pstmt.executeQuery();
        } catch (SQLException e) {
            System.out.println("⚠️ Could not get unsynced replies: " + e.getMessage());
            return null;
        }
    }

    public void markReplySynced(int replyId) {
        if (connection == null) {
            System.out.println("⚠️ No database connection");
            return;
        }
        String sql = "UPDATE replies SET is_synced = 1 WHERE id = ?";
        try (PreparedStatement pstmt = connection.prepareStatement(sql)) {
            pstmt.setInt(1, replyId);
            pstmt.executeUpdate();
            System.out.println("✅ Reply synced: " + replyId);
        } catch (SQLException e) {
            System.out.println("⚠️ Could not mark reply synced: " + e.getMessage());
        }
    }

    public void clearAll() {
        if (connection == null) return;
        try (Statement stmt = connection.createStatement()) {
            stmt.execute("DELETE FROM groups");
            stmt.execute("DELETE FROM replies");
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    public Connection getConnection() {
        return connection;
    }

    public void close() {
        try {
            if (connection != null && !connection.isClosed()) {
                connection.close();
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }
}
