package com.edudiscuss.models;

public class User {

    private int id;
    private String first_name;
    private String last_name;
    private String email;
    private String role;
    private int unread_count;

    public User() {
    }

    public int getId() {
        return id;
    }

    public int getUnreadCount() {
        return unread_count;
    }

    public String getFirst_name() {
        return first_name;
    }

    public String getLast_name() {
        return last_name;
    }

    public String getEmail() {
        return email;
    }

    public String getRole() {
        return role;
    }

    public String getFullName() {
        return first_name + " " + last_name;
    }
    @Override
    public String toString() {

        if (unread_count > 0) {
            return getFullName() + " (" + unread_count + ")";
        }

        return getFullName();
    }
}