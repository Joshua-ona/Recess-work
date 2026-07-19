package com.edudiscuss.models;

public class Message {

    private int id;
    private int sender_id;
    private int receiver_id;
    private String content;
    private String created_at;
    private int is_read;

    private User sender;

    public int getId() {
        return id;
    }

    public int getSender_id() {
        return sender_id;
    }

    public int getReceiver_id() {
        return receiver_id;
    }

    public String getContent() {
        return content;
    }

    public String getCreated_at() {
        return created_at;
    }

    public int getIs_read() {
        return is_read;
    }

    public User getSender() {
        return sender;
    }
}