package com.edudiscuss.utils;

import javafx.fxml.FXMLLoader;
import javafx.scene.Node;
import javafx.scene.Scene;
import javafx.stage.Stage;

public class Navigator {

    /**
     * Loads an FXML view and swaps it into the current window, keeping the
     * shared dashboard stylesheet applied.
     */
    public static void goTo(Node fromNode, String fxmlPath) {
        try {
            FXMLLoader loader = new FXMLLoader(Navigator.class.getResource(fxmlPath));
            Scene scene = new Scene(loader.load());
            scene.getStylesheets().add(
                    Navigator.class.getResource("/css/dashboard.css").toExternalForm());

            Stage stage = (Stage) fromNode.getScene().getWindow();
            stage.setScene(scene);
            stage.show();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    /** Returns the loaded controller so callers can pass data into the new screen. */
    public static <T> T goToWithController(Node fromNode, String fxmlPath) {
        try {
            FXMLLoader loader = new FXMLLoader(Navigator.class.getResource(fxmlPath));
            Scene scene = new Scene(loader.load());
            scene.getStylesheets().add(
                    Navigator.class.getResource("/css/dashboard.css").toExternalForm());

            Stage stage = (Stage) fromNode.getScene().getWindow();
            stage.setScene(scene);
            stage.show();

            return loader.getController();
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
    }
}
