package com.edudiscuss;

import com.edudiscuss.utils.SyncManager;
import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.stage.Stage;
import com.edudiscuss.database.DatabaseInitializer;

public class Main extends Application {

    @Override
    public void start(Stage primaryStage) throws Exception {

        DatabaseInitializer.initialize();

        // Test sync
        SyncManager manager = new SyncManager();
        manager.syncReplies();

        FXMLLoader loader = new FXMLLoader(
            getClass().getResource("/views/login.fxml")
        );
        Scene scene = new Scene(loader.load());
        primaryStage.setTitle("EduDiscuss");
        primaryStage.setScene(scene);
        primaryStage.show();
    }

    public static void main(String[] args) {
        launch(args);
    }
}
