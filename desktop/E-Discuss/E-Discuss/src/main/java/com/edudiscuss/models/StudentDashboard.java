package com.edudiscuss.models;

import java.util.List;

public class StudentDashboard {


    private int posts;

    private int quizzes;

    private int groups;


    private double participation;


    private String participationLevel;


    private List<RecommendedGroup> recommendedGroups;

    private List<String> myGroups;

    private List<String> browseGroups;



    public int getPosts() {
        return posts;
    }


    public int getQuizzes() {
        return quizzes;
    }


    public int getGroups() {
        return groups;
    }


    public double getParticipation() {
        return participation;
    }


    public String getParticipationLevel() {
        return participationLevel;
    }


    public List<RecommendedGroup> getRecommendedGroups() {
        return recommendedGroups;
    }

    public List<String> getMyGroups() {
        return myGroups;
    }

    public List<String> getBrowseGroups() {
        return browseGroups;
    }

}
