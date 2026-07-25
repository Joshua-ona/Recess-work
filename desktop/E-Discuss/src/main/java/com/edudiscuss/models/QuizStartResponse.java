package com.edudiscuss.models;

import java.util.List;
import java.util.Map;

public class QuizStartResponse {

    private Quiz quiz;
    private List<Question> questions;
    private String deadline;
    private int remaining_seconds;
    private Map<String, String> saved_answers;
    private String message; // populated on error responses (403/409/410)

    public Quiz getQuiz() { return quiz; }
    public List<Question> getQuestions() { return questions; }
    public String getDeadline() { return deadline; }
    public int getRemainingSeconds() { return remaining_seconds; }
    public Map<String, String> getSavedAnswers() { return saved_answers; }
    public String getMessage() { return message; }
}
