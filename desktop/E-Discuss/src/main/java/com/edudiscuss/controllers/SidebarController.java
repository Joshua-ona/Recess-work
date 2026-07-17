package com.edudiscuss.controllers;

import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.scene.layout.*;
import javafx.scene.text.Text;

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
            String fxmlFile = "/fxml/" + view + ".fxml";
            // Load the appropriate FXML into contentArea
            // ...
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
