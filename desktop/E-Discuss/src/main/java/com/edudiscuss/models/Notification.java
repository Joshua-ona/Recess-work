package com.edudiscuss.models;

public class Notification {

    private int id;
    private String type;
    private String title;
    private String body;
    private int reference_id;
    private String created_at;
    private boolean read;

    public Notification() {
    }

    public int getId() {
        return id;
    }

    public String getType() {
        return type;
    }

    public String getTitle() {
        return title;
    }

    public String getBody() {
        return body;
    }

    public int getReference_id() {
        return reference_id;
    }

    public String getCreated_at() {
        return created_at;
    }

    public boolean isRead() {
        return read;
    }
}