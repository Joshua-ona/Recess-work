package com.edudiscuss.controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.stage.Stage;
import com.edudiscuss.api.NotificationService;
import com.edudiscuss.utils.Navigator;
import com.edudiscuss.utils.Session;
import javafx.scene.control.Label;

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

     @FXML private Label lockBanner;

    @FXML
    public void initialize() {
        System.out.println("✅ Sidebar loaded successfully!");
        // Hide groups for lecturers
        if (isLecturer() && groupsButton != null) {
            groupsButton.setVisible(false);
            groupsButton.setManaged(false);
        }
        updateNotificationBadge();
    }
    

       @FXML
    public void goToDashboard() {

        Navigator.goTo(
                dashboardButton,
                "/views/" + roleFolder() + "/dashboard.fxml"
        );
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
        String path;

    if (isLecturer()) {
        path = "/views/lecturer/quiz-list.fxml";
    } else {
        path = "/views/student/quizzes.fxml"; // your student fxml
    }

    Navigator.goTo(quizzesButton, path);
    }

    @FXML
    private void handleNotifications() {
         Navigator.goTo(
                notificationsButton,
                "/views/" + roleFolder() + "/notifications.fxml"
        );
    }

    @FXML
    private void handleMessages() {
       Navigator.goTo(
                messagesButton,
                "/views/messages.fxml"
        );
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
      private boolean isLecturer() {

        return Session.getUser() != null
                && "lecturer".equalsIgnoreCase(
                        Session.getUser().getRole()
                );
    }


    private String roleFolder() {

        return isLecturer()
                ? "lecturer"
                : "student";
    }
    private void updateNotificationBadge() {

    try {

        int unread = NotificationService.getUnreadCount();

        if (unread > 0) {
            notificationsButton.setText("Notifications (" + unread + ")");
        } else {
            notificationsButton.setText("Notifications");
        }

    } catch (Exception e) {

        e.printStackTrace();
        notificationsButton.setText("Notifications");

    }
}
}
