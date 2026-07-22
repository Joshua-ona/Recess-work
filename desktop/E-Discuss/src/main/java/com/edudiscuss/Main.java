package com.edudiscuss;

import com.edudiscuss.utils.SyncManager;
import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

import java.io.IOException;
import java.util.Objects;

import com.edudiscuss.database.DatabaseInitializer;

public class Main extends Application {

      private static Stage primaryStage;

    @Override
public void start(Stage stage) throws Exception {

    Main.primaryStage = stage;

    DatabaseInitializer.initialize();

    SyncManager manager = new SyncManager();
    manager.syncReplies();

    FXMLLoader loader =
            new FXMLLoader(getClass().getResource("/views/login.fxml"));
            System.out.println(
        getClass().getResource("/css/style.css")
);
    

    Scene scene = new Scene(loader.load());
     scene.getStylesheets().add(
        Objects.requireNonNull(
                getClass().getResource("/css/style.css")
        ).toExternalForm()
);
    stage.setTitle("EduDiscuss");
    stage.setScene(scene);
    stage.show();
}
    public static void showAdminDashboard() {
        loadScene("/views/admin/dashboard.fxml", 1100, 700);
        primaryStage.setResizable(true);
    }

    public static void showManageUsers() {
        System.out.println(
        Main.class.getResource("/views/admin/manage-users.fxml")
);
         loadScene("/views/admin/manage-users.fxml", 1100, 700);
        primaryStage.setResizable(true);
    }

    public static void showAddLecturer() {
        loadScene("/views/admin/add-lecturer.fxml", 1100, 700);
    }

private static void loadScene(String fxmlPath,
                              double width,
                              double height) {
    try {
        FXMLLoader loader =
                new FXMLLoader(
                        Main.class.getResource(fxmlPath));

        Parent root = loader.load();

        Scene scene = new Scene(root, width, height);

        scene.getStylesheets().add(
                Objects.requireNonNull(
                        Main.class.getResource("/css/style.css")
                ).toExternalForm()
        );
        System.out.println(scene.getStylesheets());
        primaryStage.setScene(scene);
        primaryStage.centerOnScreen();

    } catch (IOException e) {
        e.printStackTrace();
    }
}
    public static Stage getPrimaryStage() {
        return primaryStage;
    }



    public static void main(String[] args) {
        launch(args);
    }
}
