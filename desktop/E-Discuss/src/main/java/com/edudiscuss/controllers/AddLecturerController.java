package com.edudiscuss.controllers;

import java.io.IOException;
import java.util.Objects;

import com.edudiscuss.Main;
import com.edudiscuss.api.ApiClient;
import com.edudiscuss.api.ApiResult;
import com.edudiscuss.utils.Session;
import javafx.application.Platform;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.stage.Stage;

public class AddLecturerController {

    @FXML private TextField firstNameField;
    @FXML private TextField lastNameField;
    @FXML private TextField emailField;
    @FXML private Label     statusLabel;
    @FXML private Button    sendButton;
    @FXML
    private Button logoutButton;

    @FXML private Label     userNameLabel;

    @FXML
    public void initialize() {
        userNameLabel.setText(Session.getInstance().getCurrentUser().getFullName());
        statusLabel.setVisible(false);
    }

    @FXML
    private void handleSend() {
        String firstName = firstNameField.getText().trim();
        String lastName  = lastNameField.getText().trim();
        String email     = emailField.getText().trim();

        if (firstName.isEmpty() || lastName.isEmpty() || email.isEmpty()) {
            showStatus("All fields are required.", false);
            return;
        }

        setLoading(true);

        new Thread(() -> {
            ApiResult<String> result = ApiClient.inviteLecturer(firstName, lastName, email);
            Platform.runLater(() -> {
                setLoading(false);
                if (result.isOk()) {
                    showStatus("✓ " + result.getValue(), true);
                    clearForm();
                } else {
                    showStatus("✗ " + result.getError(), false);
                }
            });
        }).start();
    }

    private void clearForm() {
        firstNameField.clear();
        lastNameField.clear();
        emailField.clear();
        firstNameField.requestFocus();
    }

    private void showStatus(String message, boolean success) {
        statusLabel.setText(message);
        statusLabel.setStyle(success
            ? "-fx-text-fill: #27ae60; -fx-font-weight: bold;"
            : "-fx-text-fill: #e74c3c; -fx-font-weight: bold;");
        statusLabel.setVisible(true);
    }

    private void setLoading(boolean loading) {
        sendButton.setDisable(loading);
        sendButton.setText(loading ? "Sending…" : "Send invitation");
    }

    @FXML private void goDashboard()   { Main.showAdminDashboard(); }
    @FXML private void goManageUsers() { Main.showManageUsers(); }

   @FXML
private void handleLogout(javafx.event.ActionEvent event) {
    try {
        Session.clear();

        FXMLLoader loader =
                new FXMLLoader(getClass().getResource("/views/login.fxml"));

        Scene scene = new Scene(loader.load());

        scene.getStylesheets().add(
                Objects.requireNonNull(
                        getClass().getResource("/css/style.css")
                ).toExternalForm()
        );

        Stage stage =
                (Stage) ((Button) event.getSource())
                        .getScene()
                        .getWindow();

        stage.setScene(scene);
        stage.show();

    } catch (IOException e) {
        e.printStackTrace();
    }
}
    
}
