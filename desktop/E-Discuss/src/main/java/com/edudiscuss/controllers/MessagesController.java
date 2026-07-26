package com.edudiscuss.controllers;

import com.edudiscuss.dao.MessageDAO;
import com.edudiscuss.dao.SyncQueueDAO;
import com.edudiscuss.models.Message;
import com.edudiscuss.models.User;
import com.edudiscuss.services.ApiService;
import com.edudiscuss.sync.MessageSynchronizer;
import com.edudiscuss.utils.NetworkUtils;
import com.edudiscuss.utils.Session;

import javafx.animation.Animation;
import javafx.animation.KeyFrame;
import javafx.animation.Timeline;
import javafx.application.Platform;
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

import java.time.LocalDateTime;
import java.util.List;

/**
 * Private chat, WhatsApp-style:
 *  - Conversations render instantly from the local SQLite cache
 *    (MessageDAO), so switching users or reopening the app never
 *    shows a blank screen, online or offline.
 *  - Sending a message writes it locally first (optimistic bubble,
 *    "Sending..." status) and queues it (SyncQueueDAO); it only
 *    touches the network in the background.
 *  - A background timer periodically runs MessageSynchronizer
 *    (uploads anything queued, downloads anything new) whenever
 *    we're online, then re-renders from local storage — so messages
 *    sent while offline go out automatically once connectivity
 *    returns, without the user doing anything.
 */
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
    private final MessageDAO messageDAO = new MessageDAO();
    private final SyncQueueDAO syncQueueDAO = new SyncQueueDAO();
    private final MessageSynchronizer messageSynchronizer = new MessageSynchronizer();

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
                        updateStatusLabel();

                        // Show whatever we already have locally, instantly.
                        renderConversationFromDb();

                        // Then reconcile with the server in the background.
                        syncThenRender();
                    }
                });

        startAutoRefresh();
    }

    /* ==========================================================
       USERS LIST
       ========================================================== */

    private void loadUsers() {

        new Thread(() -> {

            try {
                List<User> users = apiService.getChatUsers();

                Platform.runLater(() ->
                        usersList.setItems(FXCollections.observableArrayList(users))
                );

            } catch (Exception e) {

                e.printStackTrace();

                Platform.runLater(() -> {
                    // Offline / server unreachable: don't crash, just tell the user.
                    // Whoever is already in usersList (from a previous successful
                    // load) stays visible; on first-ever offline launch it'll be
                    // empty until they're back online.
                    statusLabel.setText("Can't load contacts \u2014 check connection");
                });
            }

        }).start();
    }

    /* ==========================================================
       CONVERSATION LOADING
       ========================================================== */

    /** Renders straight from local SQLite. Instant, works fully offline. */
    private void renderConversationFromDb() {

        if (selectedUser == null) {
            return;
        }

        new Thread(() -> {

            List<Message> messages =
                    messageDAO.findConversation(
                            Session.getUserId(),
                            selectedUser.getId()
                    );

            Platform.runLater(() -> {

                // selection may have changed while this was loading
                if (selectedUser == null) {
                    return;
                }

                messagesBox.getChildren().clear();

                for (Message message : messages) {
                    messagesBox.getChildren().add(createMessageBubble(message));
                }
            });

        }).start();
    }

    /** Uploads anything pending + downloads anything new, then re-renders. */
    private void syncThenRender() {

        new Thread(() -> {

            if (NetworkUtils.isOnline()) {
                messageSynchronizer.sync();
            }

            Platform.runLater(() -> {
                updateStatusLabel();
                renderConversationFromDb();
            });

        }).start();
    }

    private void updateStatusLabel() {
        if (selectedUser == null) {
            return;
        }
        statusLabel.setText(NetworkUtils.isOnline() ? "Online" : "Offline \u2014 messages will send later");
    }

    /* ==========================================================
       MESSAGE BUBBLES
       ========================================================== */

    private HBox createMessageBubble(Message message) {

        Label text = new Label(message.getContent());
        text.setWrapText(true);
        text.setMaxWidth(320);

        VBox bubble = new VBox(2, text);
        bubble.setPadding(new Insets(10));

        HBox row = new HBox();
        row.setPadding(new Insets(5));

        boolean isMine = message.getSenderId() == Session.getUserId();

        if (isMine) {

            row.setAlignment(Pos.CENTER_RIGHT);

            bubble.setStyle("""
                -fx-background-color:#D8FBCF;
                -fx-background-radius:18;
                """);

            // WhatsApp-style status hint under my own messages.
            Label status = new Label(message.isSynced() ? "Sent \u2713" : "Sending\u2026");
            status.setStyle("-fx-font-size:10px; -fx-text-fill:#6b7280;");
            bubble.getChildren().add(status);

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

    /* ==========================================================
       SENDING
       ========================================================== */

    @FXML
    private void sendMessage() {

        if (selectedUser == null) {
            return;
        }

        String text = messageField.getText() == null ? "" : messageField.getText().trim();

        if (text.isEmpty()) {
            return;
        }

        messageField.clear();

        // 1. Write locally first (optimistic) so the bubble appears instantly,
        //    online or offline — exactly like WhatsApp.
        Message pending = new Message();
        pending.setSenderId(Session.getUserId());
        pending.setReceiverId(selectedUser.getId());
        pending.setContent(text);
        pending.setCreatedAt(LocalDateTime.now().toString());
        pending.setUpdatedAt(LocalDateTime.now().toString());
        pending.setIsRead(0);
        pending.setStatus("PENDING");
        pending.setSynced(false);
        pending.setDeleted(false);

        new Thread(() -> {

            int localId = messageDAO.insert(pending);

            if (localId != -1) {
                syncQueueDAO.add("message", "CREATE", localId);
            }

            Platform.runLater(this::renderConversationFromDb);

            // 2. If we're online, push it out right away instead of waiting
            //    for the next auto-refresh tick.
            if (NetworkUtils.isOnline()) {
                messageSynchronizer.sync();
                Platform.runLater(this::renderConversationFromDb);
            }

        }).start();
    }

    /* ==========================================================
       BACKGROUND AUTO-SYNC
       ========================================================== */

    private void startAutoRefresh() {

        Timeline timeline = new Timeline(

                new KeyFrame(
                        Duration.seconds(5),
                        e -> new Thread(() -> {

                            if (NetworkUtils.isOnline()) {
                                messageSynchronizer.sync();
                            }

                            Platform.runLater(() -> {
                                if (selectedUser != null) {
                                    updateStatusLabel();
                                    renderConversationFromDb();
                                }
                            });

                        }).start()
                )
        );

        timeline.setCycleCount(Animation.INDEFINITE);
        timeline.play();
    }
}
