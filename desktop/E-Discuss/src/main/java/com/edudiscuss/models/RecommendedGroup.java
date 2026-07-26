package com.edudiscuss.models;


public class RecommendedGroup {

    private int id;

    private String name;

    private String description;

    private double score;

    private String reason;



    public int getId() {

        return id;
    }



    public String getName() {

        return name;
    }



    public String getDescription() {

        return description;
    }



    public double getScore() {

        return score;
    }



    public String getReason() {

        return reason;
    }



    @Override
    public String toString() {

        return name + " (" + score + "% match)";
    }

}
