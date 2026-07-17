package com.edudiscuss.controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.control.Label;
import javafx.stage.Stage;
import com.edudiscuss.utils.Session;

public class TopBarController {

    @FXML
    private Label nameLabel;

    @FXML
    private Label roleLabel;

    @FXML
    private Label pageTitle;

    @FXML
    public void initialize() {
        try {
            if (Session.getUser() != null) {
                nameLabel.setText(Session.getUser().getFullName());

                String role = Session.getUser().getRole();
                if ("admin".equals(role)) {
                    roleLabel.setText("Administrator");
                } else if ("lecturer".equals(role)) {
                    roleLabel.setText("Lecturer");
                } else {
                    roleLabel.setText("Student");
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void logout() {
        try {
            // Clear session
            Session.setUser(null);
            Session.setToken(null);

            // Load login screen
            FXMLLoader loader = new FXMLLoader(
                    getClass().getResource("/views/login.fxml")
            );

            Stage stage = (Stage) nameLabel.getScene().getWindow();
            stage.setScene(new Scene(loader.load()));
            stage.show();

        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}