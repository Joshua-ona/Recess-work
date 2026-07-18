package com.edudiscuss.controllers;

import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.scene.layout.*;
import javafx.scene.text.Text;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import com.edudiscuss.utils.Session;

public class SidebarController {
    @FXML private VBox sidebar;
    @FXML private Button homeBtn, discussionsBtn, savedBtn, quizzesBtn;
    @FXML private Button messagesBtn, groupsBtn, coursesBtn;
    @FXML private Button notificationsBtn, logoutBtn;
    @FXML private StackPane contentArea;
    @FXML private Label userName, userEmail;
    @FXML private Text userInitials;

    private String currentUserRole = "student"; // From login

    @FXML
    public void initialize() {
        setupSidebarNavigation();
        loadUserInfo();
        loadDefaultView();
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
