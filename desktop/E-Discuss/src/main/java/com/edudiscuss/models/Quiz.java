package com.edudiscuss.models;

import javafx.beans.property.SimpleStringProperty;
import javafx.beans.property.StringProperty;

import java.util.List;

public class Quiz {

    private int quiz_id;
    private int created_by;
    private int group_id;

    private String title;
    private String start_time;
    private int duration_mins;

    private String target_category;
    private boolean is_published;

    private List<Question> questions;


    public Quiz() {
    }


    public List<Question> getQuestions() {
        return questions;
    }


    public int getQuizId() {
        return quiz_id;
    }


    public int getCreatedBy() {
        return created_by;
    }


    public int getGroupId() {
        return group_id;
    }


    public String getTitle() {
        return title;
    }


    public String getStartTime() {
        return start_time;
    }


    public int getDurationMins() {
        return duration_mins;
    }


    public String getTargetCategory() {
        return target_category;
    }


    public boolean isPublished() {
        return is_published;
    }



    // ==========================
    // JavaFX TableView support
    // ==========================


    public StringProperty titleProperty() {

        return new SimpleStringProperty(
                title
        );

    }



    public StringProperty formattedStartTimeProperty() {

        if (start_time == null) {
            return new SimpleStringProperty("");
        }
        try {
            java.time.format.DateTimeFormatter displayFormat =
                    java.time.format.DateTimeFormatter.ofPattern("MMM d, yyyy h:mm a");
            String formatted = java.time.Instant.parse(start_time)
                    .atZone(java.time.ZoneId.of("Africa/Kampala"))
                    .format(displayFormat);
            return new SimpleStringProperty(formatted);
        } catch (Exception e) {
            return new SimpleStringProperty(start_time);
        }

    }



    public StringProperty durationProperty() {

        return new SimpleStringProperty(
                duration_mins + " mins"
        );

    }

}