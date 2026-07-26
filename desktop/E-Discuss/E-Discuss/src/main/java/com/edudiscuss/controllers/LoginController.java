package com.edudiscuss.controllers;

import java.util.Objects;

import com.edudiscuss.api.ApiClient;
import com.edudiscuss.models.LoginResponse;
import com.google.gson.Gson;
import javafx.application.Platform;
import javafx.fxml.FXML;
import com.edudiscuss.api.ApiResult;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.stage.Stage;
import javafx.scene.control.*;
import javafx.scene.layout.VBox;
import com.edudiscuss.utils.Session;

public class LoginController {

    @FXML
    private TextField emailField;

    @FXML private Button        loginButton;

     @FXML private Label         errorLabel;
      @FXML
    private Button logoutButton;
    @FXML
    private PasswordField passwordField;

    @FXML
    private void login() {
        String email = emailField.getText().trim();
        String password = passwordField.getText().trim();

        // Validate input
        if (email.isEmpty() || password.isEmpty()) {
            showAlert("Error", "Please enter both email and password.");
            return;
        }

        System.out.println("📧 Email: " + email);

        // Run in background thread to prevent UI freeze
        new Thread(() -> {
            try {
                String json = String.format("""
                        {
                          "email":"%s",
                          "password":"%s"
                        }
                        """, email, password);

                System.out.println("📤 Sending login request...");
                String response = ApiClient.post("login", json);
                System.out.println("📥 Login Response: " + response);

                // Parse response on background thread
                Gson gson = new Gson();
                LoginResponse login = gson.fromJson(response, LoginResponse.class);

                // Check if login was successful
                if (login != null && login.getToken() != null) {
                    // Save session on UI thread
                    Platform.runLater(() -> {
                        Session.setUser(login.getUser());
                        Session.setToken(login.getToken());

                        System.out.println("✅ Login Successful!");
                        System.out.println("👤 User: " + login.getUser().getFullName());
                        System.out.println("🎭 Role: " + login.getUser().getRole());
                        System.out.println("🔑 Token saved: " + login.getToken().substring(0, Math.min(30, login.getToken().length())) + "...");

                        redirectByRole(login.getUser().getRole());
                    });
                } else {
                    Platform.runLater(() -> {
                        showAlert("Login Failed", "Invalid email or password. Please try again.");
                    });
                }

            } catch (Exception e) {
                e.printStackTrace();
                Platform.runLater(() -> {
                    showAlert("Connection Error", "Failed to connect to server.\n" + e.getMessage());
                });
            }
        }).start();
    }

    private void redirectByRole(String role) {
        System.out.println("🔀 Redirecting role: " + role);

        try {
            String page;

            switch (role.toLowerCase()) {
                case "admin":
                    page = "/views/admin/dashboard.fxml";
                    break;
                case "lecturer":
                    page = "/views/lecturer/dashboard.fxml";
                    break;
                case "student":
                    page = "/views/student/dashboard.fxml";
                    break;
                default:
                    throw new RuntimeException("Unknown role: " + role);
            }

            System.out.println("📂 Loading FXML: " + page);

            FXMLLoader loader = new FXMLLoader(getClass().getResource(page));

            if (loader.getLocation() == null) {
                System.out.println("❌ FXML file not found: " + page);
                showAlert("Error", "Dashboard not found for role: " + role);
                return;
            }

           Scene scene = new Scene(loader.load());

String css;

switch (role.toLowerCase()) {
    case "admin":
        css = "/css/style.css";
        break;

    case "lecturer":
    case "student":
        css = "/css/dashboard.css";
        break;

    default:
        css = "/css/style.css";
}

scene.getStylesheets().add(
    Objects.requireNonNull(
        getClass().getResource(css)
    ).toExternalForm()
);


            Stage stage = (Stage) emailField.getScene().getWindow();
            stage.setScene(scene);
            stage.show();
            System.out.println("✅ Dashboard loaded successfully!");

        } catch (Exception e) {
            e.printStackTrace();
            showAlert("Error", "Failed to load dashboard: " + e.getMessage());
        }
    }

    private void showAlert(String title, String message) {
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }

        private void setLoading(boolean loading) {
        loginButton.setDisable(loading);
        loginButton.setText(loading ? "Signing in…" : "Sign in");
        errorLabel.setVisible(false);
    }
}

