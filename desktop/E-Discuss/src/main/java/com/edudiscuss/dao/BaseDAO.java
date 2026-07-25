package com.edudiscuss.dao;

import com.edudiscuss.database.DatabaseManager;

import java.sql.Connection;
import java.sql.SQLException;

public abstract class BaseDAO {

    protected Connection getConnection() throws SQLException {
        return DatabaseManager.getConnection();
    }

}