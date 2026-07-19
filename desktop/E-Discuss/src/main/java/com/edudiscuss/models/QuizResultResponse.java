package com.edudiscuss.models;

public class QuizResultResponse {
    private Quiz quiz;
    private int score;
    private int total;
    private boolean auto_submitted;
    private String message; // populated on 404 (no submission yet)

    public Quiz getQuiz() { return quiz; }
    public int getScore() { return score; }
    public int getTotal() { return total; }
    public boolean isAutoSubmitted() { return auto_submitted; }
    public String getMessage() { return message; }
}
