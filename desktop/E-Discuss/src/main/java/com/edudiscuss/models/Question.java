package com.edudiscuss.models;

import java.util.LinkedHashMap;
import java.util.Map;

public class Question {

    private int question_id;
    private String question;
    private String option_a;
    private String option_b;
    private String option_c;
    private String option_d;
    private String correct_answer; // never actually sent by the API, but kept for shape safety

    public int getQuestionId() { return question_id; }
    public String getQuestionText() { return question; }

    /** Letter -> option text, in a/b/c/d order, ready for a ToggleGroup of radio buttons. */
    public Map<String, String> getOptions() {
        Map<String, String> options = new LinkedHashMap<>();
        options.put("a", option_a);
        options.put("b", option_b);
        options.put("c", option_c);
        options.put("d", option_d);
        return options;
    }
}
