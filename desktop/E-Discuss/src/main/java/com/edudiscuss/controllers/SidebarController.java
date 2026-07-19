package com.edudiscuss.controllers;

import com.edudiscuss.utils.Navigator;
import com.edudiscuss.utils.Session;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import com.edudiscuss.api.NotificationService;
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
    public void initialize() {

        // Hide groups for lecturers
        if (isLecturer() && groupsBtn != null) {
            groupsBtn.setVisible(false);
            groupsBtn.setManaged(false);
        }
        updateNotificationBadge();
    }


    @FXML
    public void goToDashboard() {

        Navigator.goTo(
                dashboardBtn,
                "/views/" + roleFolder() + "/dashboard.fxml"
        );
    }


    @FXML
    public void goToGroups() {

        // Lecturers don't access groups
        if (isLecturer()) {
            return;
        }

        Navigator.goTo(
                groupsBtn,
                "/views/student/groups.fxml"
        );
    }


    @FXML
    public void goToDiscussions() {

        Navigator.goTo(
                discussionsBtn,
                "/views/" + roleFolder() + "/discussions.fxml"
        );
    }


@FXML
public void goToQuizzes() {

    String path;

    if (isLecturer()) {
        path = "/views/lecturer/quiz-list.fxml";
    } else {
        path = "/views/student/quizzes.fxml"; // your student fxml
    }

    Navigator.goTo(quizzesBtn, path);
}

    @FXML
    public void goToNotifications() {

        Navigator.goTo(
                notificationsBtn,
                "/views/" + roleFolder() + "/notifications.fxml"
        );
    }

    
    @FXML
    public void goToMessages() {

        Navigator.goTo(
                messagesBtn,
                "/views/messages.fxml"
        );
    }


    @FXML
    public void logout() {

        Session.clear();

        Navigator.goTo(
                logoutBtn,
                "/views/login.fxml"
        );
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
            notificationsBtn.setText("Notifications (" + unread + ")");
        } else {
            notificationsBtn.setText("Notifications");
        }

    } catch (Exception e) {

        e.printStackTrace();
        notificationsBtn.setText("Notifications");

    }
}
}