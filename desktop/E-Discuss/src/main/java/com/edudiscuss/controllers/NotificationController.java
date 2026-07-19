package com.edudiscuss.controllers;

import com.edudiscuss.api.NotificationService;
import com.edudiscuss.models.Notification;
import com.edudiscuss.models.NotificationResponse;
import javafx.fxml.FXML;
import javafx.geometry.Insets;
import javafx.scene.control.Label;
import javafx.scene.layout.HBox;
import javafx.scene.layout.Region;
import javafx.scene.layout.VBox;

public class NotificationController {

    @FXML
    private VBox notificationBox;

    @FXML
    public void initialize() {
        loadNotifications();
    }

    private void loadNotifications() {

        NotificationResponse response =
                NotificationService.getNotifications();

        if (response == null || response.getNotifications() == null) {
            notificationBox.getChildren().add(
                    new Label("No notifications found.")
            );
            return;
        }

        notificationBox.getChildren().clear();

        for (Notification notification : response.getNotifications()) {

            notificationBox.getChildren().add(
                    createNotificationCard(notification)
            );

        }
    }

    private VBox createNotificationCard(Notification notification) {

        Region dot = new Region();
        dot.setPrefSize(12, 12);
        dot.setMinSize(12, 12);
        dot.setMaxSize(12, 12);

        String color;

        switch (notification.getType()) {

            case "warning":
                color = "#EF4444";
                break;

            case "discussion":
                color = "#22C55E";
                break;

            case "quiz":
                color = "#3B82F6";
                break;

            default:
                color = "#3B82F6";
        }

        dot.setStyle(
                "-fx-background-color:" + color + ";" +
                "-fx-background-radius:50%;"
        );

        Label message = new Label(notification.getBody());
        message.setWrapText(true);
        message.setStyle("-fx-font-size:14px;");

        Label badge = new Label(notification.getType().toUpperCase());

        badge.setStyle("""
            -fx-background-color:#E5E7EB;
            -fx-background-radius:12;
            -fx-padding:3 10;
            -fx-font-size:11px;
            -fx-font-weight:bold;
            """);

        Label time = new Label(notification.getCreated_at());
        time.setStyle("-fx-text-fill:#6B7280;");

        HBox footer = new HBox(8, badge, time);

        VBox content = new VBox(6, message, footer);

        HBox row = new HBox(12, dot, content);
        row.setPadding(new Insets(12));

        if (notification.isRead()) {

    row.setStyle("""
        -fx-background-color:#F9FAFB;
        -fx-background-radius:12;
        -fx-border-color:#E5E7EB;
        -fx-border-radius:12;
        """);

} else {

    row.setStyle("""
        -fx-background-color:#EAF4FF;
        -fx-background-radius:12;
        -fx-border-color:#3B82F6;
        -fx-border-radius:12;
        """);

}
        // Mark notification as read when clicked
     row.setOnMouseClicked(e -> {

    if (!notification.isRead()) {

        boolean success =
                NotificationService.markAsRead(notification.getId());

        if (success) {

            loadNotifications();

        }

    }

});

        return new VBox(row);
    }
}