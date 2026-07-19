package com.edudiscuss.database;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class DatabaseManager {

    private static final String URL =
            "jdbc:sqlite:edudiscuss.db";

    public static Connection getConnection()
            throws SQLException {

        return DriverManager.getConnection(URL);
    }
}