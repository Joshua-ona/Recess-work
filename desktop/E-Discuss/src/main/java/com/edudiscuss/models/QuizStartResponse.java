package com.edudiscuss.models;

import com.google.gson.annotations.SerializedName;
import java.util.List;
import java.util.Map;

public class QuizStartResponse {
    private Quiz quiz;
    private List<Question> questions;

    @SerializedName("saved_answers")
    private Map<String, String> savedAnswers;

    @SerializedName("remaining_seconds")
    private double remainingSeconds;

    public QuizStartResponse() {}

    public QuizStartResponse(Quiz quiz, List<Question> questions, Map<String, String> savedAnswers, double remainingSeconds) {
        this.quiz = quiz;
        this.questions = questions;
        this.savedAnswers = savedAnswers;
        this.remainingSeconds = remainingSeconds;
    }

    public Quiz getQuiz() { return quiz; }
    public void setQuiz(Quiz quiz) { this.quiz = quiz; }

    public List<Question> getQuestions() { return questions; }
    public void setQuestions(List<Question> questions) { this.questions = questions; }

    public Map<String, String> getSavedAnswers() { return savedAnswers; }
    public void setSavedAnswers(Map<String, String> savedAnswers) { this.savedAnswers = savedAnswers; }

    public double getRemainingSeconds() { return remainingSeconds; }
    public void setRemainingSeconds(double remainingSeconds) { this.remainingSeconds = remainingSeconds; }
}