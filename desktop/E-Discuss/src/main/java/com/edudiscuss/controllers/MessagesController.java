package com.edudiscuss.controllers;

import com.edudiscuss.models.Message;
import com.edudiscuss.models.User;
import com.edudiscuss.services.ApiService;
import com.edudiscuss.utils.Session;

import javafx.animation.Animation;
import javafx.animation.KeyFrame;
import javafx.animation.Timeline;
import javafx.collections.FXCollections;
import javafx.fxml.FXML;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.control.Label;
import javafx.scene.control.ListView;
import com.edudiscuss.cells.UserCell;
import javafx.scene.layout.HBox;
import javafx.scene.layout.VBox;
import javafx.util.Duration;

import java.util.List;

public class MessagesController {

    @FXML
    private ListView<User> usersList;

   @FXML
private VBox messagesBox;

@FXML
private javafx.scene.control.TextField messageField;

@FXML
private Label chatName;

@FXML
private Label statusLabel;

    private final ApiService apiService = new ApiService();

    private User selectedUser;

    @FXML
    public void initialize() {

        System.out.println("MessagesController initialized");

        
        usersList.setCellFactory(list -> new UserCell());
        loadUsers();

        usersList.getSelectionModel()
                .selectedItemProperty()
                .addListener((obs, oldUser, newUser) -> {

                    if (newUser != null) {

                       selectedUser = newUser;

chatName.setText(newUser.getFullName());
statusLabel.setText("Online");

loadConversation();
                    }
                });

        startAutoRefresh();
    }

    private HBox createMessageBubble(Message message) {

    Label text = new Label(message.getContent());
    text.setWrapText(true);
    text.setMaxWidth(320);

    VBox bubble = new VBox(text);
    bubble.setPadding(new Insets(10));

    HBox row = new HBox();
    row.setPadding(new Insets(5));

    if (message.getSender_id() == Session.getUserId()) {

        row.setAlignment(Pos.CENTER_RIGHT);

        bubble.setStyle("""
            -fx-background-color:#D8FBCF;
            -fx-background-radius:18;
            """);

    } else {

        row.setAlignment(Pos.CENTER_LEFT);

        bubble.setStyle("""
            -fx-background-color:white;
            -fx-background-radius:18;
            """);
    }

    row.getChildren().add(bubble);

    return row;
}
  
   private void loadUsers() {

    try {

        List<User> users = apiService.getChatUsers();

        System.out.println("TOTAL USERS: " + users.size());

        for(User user : users){

            System.out.println(
                user.getFullName()
                + " | "
                + user.getEmail()
            );
        }


        usersList.setItems(
            FXCollections.observableArrayList(users)
        );


    } catch(Exception e){

        e.printStackTrace();

    }

}

    private void loadConversation() {

        if (selectedUser == null)
            return;

        try {

            List<Message> messages =
                    apiService.getConversation(
                            selectedUser.getId()
                    );
messagesBox.getChildren().clear();

for (Message message : messages) {
    messagesBox.getChildren().add(
            createMessageBubble(message)
    );
}

        } catch (Exception e) {

            e.printStackTrace();

        }
    }

    @FXML
    private void sendMessage() {

        if (selectedUser == null)
            return;

        String text =
                messageField.getText().trim();

        if (text.isEmpty())
            return;

        try {

            apiService.sendMessage(
                    selectedUser.getId(),
                    text
            );

            messageField.clear();

            loadConversation();

        } catch (Exception e) {

            e.printStackTrace();

        }
    }

    private void startAutoRefresh() {

        Timeline timeline =
                new Timeline(

                        new KeyFrame(
                                Duration.seconds(3),
                                e -> {

                                    if (selectedUser != null) {

                                        loadConversation();
                                    }
                                }
                        )
                );

        timeline.setCycleCount(
                Animation.INDEFINITE
        );

        timeline.play();
    }
}