package com.edudiscuss;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.stage.Stage;

public class Main extends Application {

    @Override
    public void start(Stage stage) throws Exception {

        FXMLLoader loader =
                new FXMLLoader(
                        getClass().getResource(
                                "/views/login.fxml"
                        )
                );

        Scene scene = new Scene(loader.load());

        stage.setTitle("EduDiscuss");
        stage.setScene(scene);
        stage.setWidth(500);
        stage.setHeight(400);
        stage.show();
    }

    public static void main(String[] args) {
        launch(args);
    }
}