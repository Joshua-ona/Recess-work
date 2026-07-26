package com.edudiscuss.controllers;

import java.io.IOException;
import java.util.Objects;

import javafx.event.ActionEvent;
import com.edudiscuss.Main;
import com.edudiscuss.api.ApiClient;
import com.edudiscuss.api.ApiResult;
import com.edudiscuss.utils.Session;
import javafx.application.Platform;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.stage.Stage;

public class AdminDashboardController {

    @FXML private Label userNameLabel;
    @FXML private Label totalMembersLabel;
    @FXML private Label activeTodayLabel;
    @FXML private Label pendingLabel;
    @FXML private Label blacklistedLabel;
    @FXML private Label statusLabel;
      @FXML
    private Button logoutButton;

    @FXML
    public void initialize() {
        userNameLabel.setText("Welcome, " + Session.getInstance().getCurrentUser().getFullName());
        statusLabel.setText("Loading…");
        loadStats();
        System.out.println(userNameLabel.getScene());
    }

    private void loadStats() {
        new Thread(() -> {
            ApiResult<ApiClient.DashboardStats> result = ApiClient.adminDashboard();
            Platform.runLater(() -> {
                if (!result.isOk()) {
                    statusLabel.setText("Could not load stats: " + result.getError());
                    return;
                }
                statusLabel.setText("");
                ApiClient.DashboardStats s = result.getValue();
                totalMembersLabel.setText(String.valueOf(s.total_members));
                activeTodayLabel.setText(String.valueOf(s.active_today));
                pendingLabel.setText(String.valueOf(s.pending_count));
                blacklistedLabel.setText(String.valueOf(s.blacklisted_count));
            });
        }).start();
    }

    @FXML private void goToManageUsers()  { Main.showManageUsers(); }
    @FXML private void goToAddLecturer()  { Main.showAddLecturer(); }
    @FXML private void refreshDashboard() { loadStats(); }

    
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
