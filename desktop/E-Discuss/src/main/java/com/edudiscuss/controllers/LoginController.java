package com.edudiscuss.controllers;

import com.edudiscuss.api.ApiClient;
import com.edudiscuss.models.LoginResponse;
import com.google.gson.Gson;
import javafx.fxml.FXML;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.stage.Stage;
import com.edudiscuss.utils.Session;

public class LoginController {

    @FXML
    private TextField emailField;

    @FXML
    private PasswordField passwordField;

    @FXML
    public void login() {

        try {

            String json =
                    String.format("""
                    {
                      "email":"%s",
                      "password":"%s"
                    }
                    """,
                            emailField.getText(),
                            passwordField.getText());

            String response =
                    ApiClient.post("login", json);
            System.out.println(response);

            Gson gson = new Gson();

            LoginResponse login =
                    gson.fromJson(
                            response,
                            LoginResponse.class
                    );
            Session.setUser(login.getUser());
            Session.setToken(login.getToken());

            redirectByRole(login.getUser().getRole());

            System.out.println(
                    login.getUser().getFullName()
            );

            System.out.println(
                    login.getToken()
            );
            System.out.println(
                    "ROLE = " + login.getUser().getRole()
            );

        }

        catch (Exception e) {
            e.printStackTrace();
        }
    }
    private void redirectByRole(String role) {

        System.out.println("Redirecting role: " + role);

        try {

            String page;

            switch(role) {

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
                    throw new RuntimeException(
                            "Unknown role: " + role
                    );
            }

            System.out.println("Loading FXML: " + page);

            var resource = getClass().getResource(page);

            System.out.println("FXML FOUND: " + resource);

            FXMLLoader loader =
                    new FXMLLoader(
                            getClass().getResource(page)
                    );

            Scene scene = new Scene(loader.load());
            scene.getStylesheets().add(
                    getClass()
                            .getResource("/css/dashboard.css")
                            .toExternalForm());

            Stage stage =
                    (Stage) emailField
                            .getScene()
                            .getWindow();

            stage.setScene(scene);
            stage.show();


        } catch(Exception e) {

            e.printStackTrace();

        }
    }
}
