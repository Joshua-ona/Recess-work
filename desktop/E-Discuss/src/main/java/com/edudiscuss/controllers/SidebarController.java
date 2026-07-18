package com.edudiscuss.controllers;

import com.edudiscuss.utils.Navigator;
import com.edudiscuss.utils.QuizLockService;
import com.edudiscuss.utils.Session;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.Label;

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

    @FXML
    public void logout() {
        Session.clear();
        Navigator.goTo(logoutBtn, "/views/login.fxml");
    }

    /**
     * Requirement #3: before leaving to any non-quiz screen, check the
     * server for an active quiz. If locked, QuizLockService redirects to
     * the attempt screen itself and we don't proceed with the original
     * navigation.
     */
    private void navigateIfUnlocked(String fxmlPath) {
        boolean isStudent = Session.getUser() != null && "student".equals(Session.getUser().getRole());

        if (isStudent && QuizLockService.enforceLock(dashboardBtn)) {
            return;
        }
        Navigator.goTo(dashboardBtn, fxmlPath);
    }

    private String rolePath(String file) {
        String role = Session.getUser() != null ? Session.getUser().getRole() : "student";
        return "/views/" + role + "/" + file;
    }
}
