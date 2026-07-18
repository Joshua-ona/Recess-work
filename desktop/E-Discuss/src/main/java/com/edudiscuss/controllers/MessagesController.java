package com.edudiscuss.controllers;

import com.edudiscuss.models.Message;
import com.edudiscuss.models.User;
import com.edudiscuss.services.ApiService;
import javafx.collections.FXCollections;
import javafx.fxml.FXML;
import javafx.scene.control.ListView;
import javafx.scene.control.TextArea;
import com.edudiscuss.utils.Session;

import javafx.geometry.Pos;
import javafx.scene.control.Label;
import javafx.scene.control.ListCell;
import javafx.scene.layout.HBox;
import javafx.scene.layout.VBox;

import java.util.List;

public class MessagesController {

    @FXML
    private ListView<User> usersList;

    @FXML
    private ListView<Message> messagesList;

    @FXML
    private TextArea messageField;

    private final ApiService apiService = new ApiService();

    private User selectedUser;


    @FXML
    public void initialize() {
        System.out.println("MessagesController initialized");

        messagesList.setCellFactory(list -> new ListCell<>() {

            @Override
            protected void updateItem(
                    Message message,
                    boolean empty) {

                super.updateItem(message, empty);

                if (empty || message == null) {
                    setGraphic(null);
                    return;
                }

                Label text =
                        new Label(message.getContent());

                text.setWrapText(true);
                text.setMaxWidth(300);

                VBox bubble =
                        new VBox(text);

                HBox row =
                        new HBox(bubble);

                if (message.getSender_id()
                        == Session.getUserId()) {

                    row.setAlignment(
                            Pos.CENTER_RIGHT
                    );

                    bubble.setStyle("""
                        -fx-background-color:#2563EB;
                        -fx-background-radius:15;
                        -fx-padding:10;
                        """);

                } else {

                    row.setAlignment(
                            Pos.CENTER_LEFT
                    );

                    bubble.setStyle("""
                        -fx-background-color:#E5E7EB;
                        -fx-background-radius:15;
                        -fx-padding:10;
                        """);
                }

                setGraphic(row);
            }
        });

        loadUsers();

        usersList.getSelectionModel()
                .selectedItemProperty()
                .addListener(
                        (obs, oldUser, newUser) -> {

                            if (newUser != null) {

                                selectedUser = newUser;

                                loadConversation();
                            }
                        });
    }

    private void loadUsers() {

        try {

            List<User> users = apiService.getChatUsers();

            usersList.setItems(
                    FXCollections.observableArrayList(users)
            );

        } catch (Exception e) {

            e.printStackTrace();

        }

    }

    private void loadConversation() {
        System.out.println("Loading conversation...");

        System.out.println(
                "Selected user: "
                        + selectedUser.getId()
        );
        try {

            List<Message> messages =
                    apiService.getConversation(
                            selectedUser.getId()
                    );

            messagesList.setItems(
                    FXCollections.observableArrayList(messages)
            );

            if (!messages.isEmpty()) {

                messagesList.scrollTo(
                        messages.size() - 1
                );
            }

        } catch (Exception e) {

            e.printStackTrace();

        }
    }
    @FXML
    private void sendMessage() {

        if (selectedUser == null) {
            return;
        }

        if (messageField.getText().isBlank()) {
            return;
        }

        try {

            apiService.sendMessage(
                    selectedUser.getId(),
                    messageField.getText()
            );

            messageField.clear();

            loadConversation();

        } catch (Exception e) {

            e.printStackTrace();

        }

    }

}