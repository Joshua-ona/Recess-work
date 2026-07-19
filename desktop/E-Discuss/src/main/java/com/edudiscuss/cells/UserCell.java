package com.edudiscuss.cells;

import com.edudiscuss.models.User;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.control.Label;
import javafx.scene.control.ListCell;
import javafx.scene.layout.*;
import javafx.scene.paint.Color;
import javafx.scene.shape.Circle;

public class UserCell extends ListCell<User> {

    @Override
    protected void updateItem(User user, boolean empty) {

        super.updateItem(user, empty);

        if (empty || user == null) {
            setGraphic(null);
            return;
        }

        Circle circle = new Circle(20);
        circle.setFill(Color.web("#4B3F96"));

        Label initial = new Label(
                user.getFirst_name()
                        .substring(0, 1)
                        .toUpperCase()
        );

        initial.setStyle(
                "-fx-text-fill:white;" +
                "-fx-font-size:14;" +
                "-fx-font-weight:bold;"
        );

        StackPane avatar = new StackPane(circle, initial);

        Label name = new Label(user.getFullName());
        name.setStyle(
                "-fx-font-weight:bold;" +
                "-fx-font-size:14;"
        );

        Label email = new Label(user.getEmail());
        email.setStyle(
                "-fx-text-fill:#777777;" +
                "-fx-font-size:12;"
        );

        VBox details = new VBox(3, name, email);

        // Optional unread badge
        if (user.getUnreadCount() > 0) {

            Label badge = new Label(
                    String.valueOf(user.getUnreadCount())
            );

            badge.setStyle("""
                    -fx-background-color:#EF4444;
                    -fx-text-fill:white;
                    -fx-background-radius:12;
                    -fx-padding:2 8 2 8;
                    -fx-font-size:11;
                    """);

            Region spacer = new Region();
            HBox.setHgrow(spacer, Priority.ALWAYS);

            HBox root = new HBox(
                    12,
                    avatar,
                    details,
                    spacer,
                    badge
            );

            root.setAlignment(Pos.CENTER_LEFT);
            root.setPadding(new Insets(10));

            setGraphic(root);

        } else {

            HBox root = new HBox(
                    12,
                    avatar,
                    details
            );

            root.setAlignment(Pos.CENTER_LEFT);
            root.setPadding(new Insets(10));

            setGraphic(root);
        }
    }
}