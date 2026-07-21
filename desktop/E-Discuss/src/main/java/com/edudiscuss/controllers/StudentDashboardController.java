package com.edudiscuss.controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.stage.Stage;

import java.io.IOException;

public class StudentDashboardController {

    @FXML
    private Button groupsButton;

    @FXML
    private Button discussionsButton;

    @FXML
    private Button quizzesButton;

    @FXML
    private Button notificationsButton;

    @FXML
    private Button logoutButton;

    @FXML
    public void initialize() {
        System.out.println("Student Dashboard Loaded!");
    }

    @FXML
    private void handleGroups() {
        try {
            FXMLLoader loader = new FXMLLoader(
                getClass().getResource("/views/student/groups.fxml")
            );
            Scene scene = new Scene(loader.load());
            Stage stage = (Stage) groupsButton.getScene().getWindow();
            stage.setScene(scene);
            stage.show();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleDiscussions() {
        System.out.println("Discussions button clicked");
        // TODO: Load discussions view
    }

    @FXML
    private void handleQuizzes() {
        System.out.println("Quizzes button clicked");
        // TODO: Load quizzes view
    }

    @FXML
    private void handleNotifications() {
        System.out.println("Notifications button clicked");
        // TODO: Load notifications view
    }

    @FXML
    private void handleLogout() {
        try {
            // Clear session
            com.edudiscuss.utils.Session.clear();

            // Load login screen
            FXMLLoader loader = new FXMLLoader(
                getClass().getResource("/views/login.fxml")
            );
            Scene scene = new Scene(loader.load());
            Stage stage = (Stage) logoutButton.getScene().getWindow();
            stage.setScene(scene);
            stage.show();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
