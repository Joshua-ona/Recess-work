package com.edudiscuss.models;

public class Quiz {

    private int quiz_id;
    private int created_by;
    private int group_id;
    private String title;
    private String start_time;
    private int duration_mins;
    private String target_category;
    private boolean is_published;
    private java.util.List<Question> questions; // only present when eager-loaded (lecturer show/upload)

    public java.util.List<Question> getQuestions() { return questions; }

    public int getQuizId() { return quiz_id; }
    public int getCreatedBy() { return created_by; }
    public int getGroupId() { return group_id; }
    public String getTitle() { return title; }
    public String getStartTime() { return start_time; }
    public int getDurationMins() { return duration_mins; }
    public String getTargetCategory() { return target_category; }
    public boolean isPublished() { return is_published; }
}
