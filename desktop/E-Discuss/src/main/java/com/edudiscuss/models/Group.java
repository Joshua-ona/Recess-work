package com.edudiscuss.models;

public class Group {
    private int id;
    private String name;

    public int getId() { return id; }
    public String getName() { return name; }

    @Override
    public String toString() { return name; } // used directly in ComboBox display
}
