package com.edudiscuss.models;

public class User {

    private int id;
    private String first_name;
    private String last_name;
    private String email;
    private String role;
    private int unread_count;
    private String status;
    private int warning_count;


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
    
    /** Display-friendly role label */
    public String getRoleLabel() {
        return switch (getRole()) {
            case "admin"    -> "Admin";
            case "lecturer" -> "Lecturer";
            case "student"  -> "Student";
            default         -> getRole();
        };
    }
    public boolean isBlacklisted() { return "blacklisted".equals(status); }
    public int    getWarningCount(){ return warning_count; }
     public String getStatus()      { return status == null ? "active" : status; }
    public boolean isPending()     { return "pending".equals(status); }
}