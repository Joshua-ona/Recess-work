package com.edudiscuss.models;

public class ActiveQuizResponse {

    private boolean active;
    private int quiz_id;
    private String deadline;
    private double remaining_seconds;

    public boolean isActive() { return active; }
    public int getQuizId() { return quiz_id; }
    public String getDeadline() { return deadline; }
    public double getRemainingSeconds() { return remaining_seconds; }
}
