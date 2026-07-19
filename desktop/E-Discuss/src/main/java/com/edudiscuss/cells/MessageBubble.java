package com.edudiscuss.cells;

import com.edudiscuss.models.Message;
import com.edudiscuss.utils.Session;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.control.Label;
import javafx.scene.layout.*;

public class MessageBubble extends HBox {

    public MessageBubble(Message message) {

        Label text = new Label(message.getContent());

        text.setWrapText(true);
        text.setMaxWidth(320);

        VBox bubble = new VBox(text);

        bubble.setPadding(new Insets(10));

        boolean mine =
                message.getSender_id() == Session.getUserId();

        if (mine) {

            setAlignment(Pos.CENTER_RIGHT);

            bubble.setStyle("""
                -fx-background-color:#D8FBCF;
                -fx-background-radius:18;
                """);

        } else {

            setAlignment(Pos.CENTER_LEFT);

            bubble.setStyle("""
                -fx-background-color:white;
                -fx-background-radius:18;
                """);
        }

        getChildren().add(bubble);

        setPadding(new Insets(4));
    }
}