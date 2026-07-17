package com.edudiscuss.controllers;

import com.edudiscuss.models.StudentDashboard;
import com.edudiscuss.models.User;
import com.edudiscuss.services.ApiService;
import com.edudiscuss.utils.Session;
import javafx.fxml.FXML;
import javafx.scene.control.Label;

import java.time.LocalTime;

public class StudentDashboardController {

    @FXML
    private Label greetingLabel;

    @FXML
    private Label postsLabel;

    @FXML
    private Label quizLabel;

    @FXML
    private Label groupsLabel;

    @FXML
    private Label scoreLabel;

    private final ApiService apiService = new ApiService();

    @FXML
    public void initialize() {
        loadGreeting();
        loadDashboard();
    }

    private void loadGreeting() {

        User user = Session.getUser();

        if (user == null) {
            greetingLabel.setText("Welcome");
            return;
        }

        int hour = LocalTime.now().getHour();

        String greeting;

        if (hour < 12) {
            greeting = "Good morning";
        } else if (hour < 17) {
            greeting = "Good afternoon";
        } else {
            greeting = "Good evening";
        }

        greetingLabel.setText(greeting + ", " + user.getFirst_name());
    }

    private void loadDashboard() {

        try {

            StudentDashboard dashboard = apiService.getStudentDashboard();

            updateDashboard(dashboard);

        } catch (Exception e) {

            e.printStackTrace();

            postsLabel.setText("0");
            quizLabel.setText("0");
            groupsLabel.setText("0");
            scoreLabel.setText("0.0");
        }
    }

    private void updateDashboard(StudentDashboard dashboard) {

        postsLabel.setText(String.valueOf(dashboard.getPosts()));
        quizLabel.setText(String.valueOf(dashboard.getQuizzes()));
        groupsLabel.setText(String.valueOf(dashboard.getGroups()));
        scoreLabel.setText(String.format("%.1f", dashboard.getParticipation()));

    }
}