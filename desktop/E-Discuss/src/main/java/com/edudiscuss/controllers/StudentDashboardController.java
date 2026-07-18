package com.edudiscuss.controllers;

import com.edudiscuss.utils.QuizLockService;
import javafx.fxml.FXML;
import javafx.scene.layout.VBox;

public class StudentDashboardController {

    @FXML private VBox rootBox;

    @FXML
    public void initialize() {
        // Requirement #3: landing on the dashboard while a quiz is active
        // should redirect straight to it, same as the web app's middleware
        // does for every student.* route.
        QuizLockService.enforceLock(rootBox);
    }
}
