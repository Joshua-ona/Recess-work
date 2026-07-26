package com.edudiscuss.utils;

import com.edudiscuss.models.User;

public class Session {

    private static User user;
    private static String token;
     private static Session instance;

    public static int getUserId() {
    return user != null ? user.getId() : 0;
}

  
    public static Session getInstance() {
        if (instance == null) {
            instance = new Session();
        }
        return instance;
    }

    public static void setUser(User user) {
        Session.user = user;
    }

     public User   getCurrentUser() { return user; }

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
        public boolean isLoggedIn()  { return token != null && user != null; }
}