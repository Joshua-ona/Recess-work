package com.edudiscuss.controllers;

import com.edudiscuss.utils.Navigator;
import com.edudiscuss.utils.QuizLockService;
import com.edudiscuss.utils.Session;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import com.edudiscuss.utils.Session;

public class SidebarController {

    @FXML private Button dashboardBtn;
    @FXML private Button groupsBtn;
    @FXML private Button discussionsBtn;
    @FXML private Button quizzesBtn;
    @FXML private Button notificationsBtn;
    @FXML private Button messagesBtn;
    @FXML private Button logoutBtn;
    @FXML private Label lockBanner;

    @FXML
    public void goToDashboard() {
        navigateIfUnlocked(rolePath("dashboard.fxml"));
    }

    @FXML
    public void goToGroups() {
        navigateIfUnlocked("/views/student/groups.fxml");
    }

    @FXML
    public void goToDiscussions() {
        navigateIfUnlocked("/views/student/discussions.fxml");
    }

    @FXML
    public void goToQuizzes() {
        boolean isLecturer = Session.getUser() != null && "lecturer".equals(Session.getUser().getRole());

        if (isLecturer) {
            // Lecturers have no active-quiz lock concept — go straight there.
            Navigator.goTo(quizzesBtn, "/views/lecturer/quiz-list.fxml");
            return;
        }

        // Quizzes is always reachable for students — if locked, the lock
        // check inside enforceLock just bounces to the attempt screen
        // anyway, which is the correct outcome either way.
        Navigator.goTo(quizzesBtn, "/views/student/quiz-list.fxml");
    }

    @FXML
    public void goToNotifications() {
        navigateIfUnlocked("/views/student/notifications.fxml");
    }

    @FXML
    public void goToMessages() {
        navigateIfUnlocked("/views/student/messages.fxml");
    }

    private void setupSidebarNavigation() {
        // Set up navigation buttons
        homeBtn.setOnAction(e -> loadView("home"));
        quizzesBtn.setOnAction(e -> loadView("quizzes"));
        discussionsBtn.setOnAction(e -> loadView("discussions"));
        savedBtn.setOnAction(e -> loadView("saved"));
        messagesBtn.setOnAction(e -> loadView("messages"));
        groupsBtn.setOnAction(e -> loadView("groups"));
        logoutBtn.setOnAction(e -> handleLogout());
    }

    private void loadView(String view) {
    try {

        FXMLLoader loader = new FXMLLoader(
                getClass().getResource("/views/" + view + ".fxml")
        );

        Parent root = loader.load();

        contentArea.getChildren().clear();
        contentArea.getChildren().add(root);

    } catch (Exception e) {
        e.printStackTrace();
    }
}
    private void loadUserInfo() {

    if (Session.getUser() != null) {

        userName.setText(
                Session.getUser().getFullName()
        );

        userEmail.setText(
                Session.getUser().getEmail()
        );

        String name =
                Session.getUser().getFullName();

        String initials = "";

        for (String part : name.split(" ")) {
            if (!part.isEmpty()) {
                initials +=
                        part.substring(0, 1)
                                .toUpperCase();
            }
        }

        userInitials.setText(initials);
    }
}
private void loadDefaultView() {
    loadView("home");
}
private void handleLogout() {

    try {

        Session.clear();

        FXMLLoader loader =
                new FXMLLoader(
                        getClass().getResource(
                                "/views/login.fxml"
                        )
                );

        Parent root = loader.load();

        logoutBtn.getScene().setRoot(root);

    } catch (Exception e) {
        e.printStackTrace();
    }
}
}
