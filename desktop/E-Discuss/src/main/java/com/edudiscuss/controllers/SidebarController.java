package com.edudiscuss.controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.stage.Stage;

import java.io.IOException;

public class SidebarController {

    @FXML
    private Button dashboardButton;

    @FXML
    private Button groupsButton;

    @FXML
    private Button discussionsButton;

    @FXML
    private Button quizzesButton;

    @FXML
    private Button notificationsButton;

    @FXML
    private Button messagesButton;

    @FXML
    private Button logoutButton;

    @FXML
    public void initialize() {
        System.out.println("✅ Sidebar loaded successfully!");
    }

    @FXML
    private void handleDashboard() {
        try {
            FXMLLoader loader = new FXMLLoader(
                getClass().getResource("/views/student/dashboard.fxml")
            );
            Scene scene = new Scene(loader.load());
            Stage stage = (Stage) dashboardButton.getScene().getWindow();
            stage.setScene(scene);
            stage.show();
        } catch (IOException e) {
            e.printStackTrace();
        }
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
        try {
            FXMLLoader loader = new FXMLLoader(
                getClass().getResource("/views/student/my-discussions.fxml")
            );
            Scene scene = new Scene(loader.load());
            Stage stage = (Stage) discussionsButton.getScene().getWindow();
            stage.setScene(scene);
            stage.show();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleQuizzes() {
        System.out.println("Quizzes button clicked");
    }

    @FXML
    private void handleNotifications() {
        System.out.println("Notifications button clicked");
    }

    @FXML
    private void handleMessages() {
        System.out.println("Messages button clicked");
    }

    @FXML
    private void handleLogout() {
        try {
            com.edudiscuss.utils.Session.clear();

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

