package com.edudiscuss.utils;

import com.edudiscuss.models.User;

public class Session {

    private static User user;
    private static String token;
    private static int userId;

    public static int getUserId() {
        return userId;
    }

    public static void setUserId(int id) {
        userId = id;
    }

    public static void setUser(User user) {
        Session.user = user;
    }


    public static User getUser() {
        return user;
    }


    public static void setToken(String token) {
        Session.token = token;
    }


    public static String getToken() {
        return token;
    }


    public static void clear() {
        user = null;
        token = null;
    }
}