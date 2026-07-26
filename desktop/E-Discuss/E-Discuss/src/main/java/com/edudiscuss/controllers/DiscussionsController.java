package com.edudiscuss.controllers;

import com.edudiscuss.api.ApiClient;
import com.edudiscuss.database.DatabaseHelper;
import com.edudiscuss.utils.NetworkUtils;
import com.edudiscuss.utils.Session;
import com.google.gson.JsonArray;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.scene.layout.VBox;
import javafx.geometry.Insets;

public class DiscussionsController {

    @FXML private Label groupTitle;
    @FXML private ListView<String> discussionsList;

    private ObservableList<String> discussions = FXCollections.observableArrayList();
    private DatabaseHelper db;
    private int groupId;
    private String groupName;

    public void setGroup(int groupId, String groupName) {
        this.groupId = groupId;
        this.groupName = groupName;
        this.db = DatabaseHelper.getInstance();
        groupTitle.setText("📚 " + groupName + " - Discussions");
        loadDiscussions();

        discussionsList.setOnMouseClicked(event -> {
            if (event.getClickCount() == 2) {
                String selected = discussionsList.getSelectionModel().getSelectedItem();
                if (selected != null && !selected.contains("No discussions") && !selected.contains("Failed")) {
                    openDiscussion(selected);
                }
            }
        });
    }

    private void loadDiscussions() {
        new Thread(() -> {
            if (NetworkUtils.isOnline()) {
                System.out.println("📡 Online - Loading discussions from API");
                loadDiscussionsFromApi();
            } else {
                System.out.println("📡 Offline - Loading discussions from database");
                javafx.application.Platform.runLater(() -> loadDiscussionsFromDatabase());
            }
        }).start();
    }

    private void loadDiscussionsFromApi() {
        try {
            String response = ApiClient.get("groups/" + groupId + "/discussions");
            System.out.println("📥 Discussions Response: " + response);

            JsonObject json = JsonParser.parseString(response).getAsJsonObject();
            JsonArray data = json.has("discussions") ? json.getAsJsonArray("discussions") :
                (json.has("data") ? json.getAsJsonArray("data") : new JsonArray());

            final JsonArray finalData = data;

            javafx.application.Platform.runLater(() -> {
                discussions.clear();

                if (finalData.size() == 0) {
                    discussions.add("📭 No discussions yet. Start one!");
                    discussionsList.setItems(discussions);
                    return;
                }

                for (int i = 0; i < finalData.size(); i++) {
                    JsonObject disc = finalData.get(i).getAsJsonObject();
                    String title = disc.has("title") ? disc.get("title").getAsString() : "Untitled";
                    String author = "Unknown";

                    if (disc.has("user")) {
                        JsonObject user = disc.get("user").getAsJsonObject();
                        if (user.has("first_name")) {
                            author = user.get("first_name").getAsString();
                            if (user.has("last_name")) {
                                author += " " + user.get("last_name").getAsString();
                            }
                        }
                    }

                    discussions.add("💬 " + title + " (by " + author + ")");
                }

                discussionsList.setItems(discussions);
                System.out.println("✅ Loaded " + discussions.size() + " discussions from API");
            });

        } catch (Exception e) {
            e.printStackTrace();
            javafx.application.Platform.runLater(() -> {
                discussions.clear();
                discussions.add("⚠️ Failed to load discussions");
                discussionsList.setItems(discussions);
            });
        }
    }

    private void loadDiscussionsFromDatabase() {
        try {
            discussions.clear();
            discussions.add("📡 Offline - Connect to internet to see discussions");
            discussionsList.setItems(discussions);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleRefresh() {
        System.out.println("🔄 Refreshing discussions...");
        loadDiscussions();
    }

    private void openDiscussion(String selected) {
        try {
            String title = selected.replaceAll("💬 ", "").split(" \\(by ")[0];

            new Thread(() -> {
                try {
                    if (NetworkUtils.isOnline()) {
                        // Online - load from API
                        loadDiscussionFromApi(title);
                    } else {
                        // Offline - load from database
                        loadDiscussionFromDatabase(title);
                    }
                } catch (Exception e) {
                    e.printStackTrace();
                }
            }).start();

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void loadDiscussionFromApi(String title) {
        try {
            String response = ApiClient.get("groups/" + groupId + "/discussions");
            JsonObject json = JsonParser.parseString(response).getAsJsonObject();
            JsonArray data = json.has("discussions") ? json.getAsJsonArray("discussions") :
                (json.has("data") ? json.getAsJsonArray("data") : new JsonArray());

            for (int i = 0; i < data.size(); i++) {
                JsonObject disc = data.get(i).getAsJsonObject();
                String discTitle = disc.get("title").getAsString();

                if (discTitle.equals(title)) {
                    int discussionId = disc.get("id").getAsInt();
                    String content = disc.has("body") ? disc.get("body").getAsString() :
                        (disc.has("content") ? disc.get("content").getAsString() : "No content");

                    String author = "Unknown";
                    if (disc.has("user")) {
                        JsonObject user = disc.get("user").getAsJsonObject();
                        if (user.has("first_name")) {
                            author = user.get("first_name").getAsString();
                            if (user.has("last_name")) {
                                author += " " + user.get("last_name").getAsString();
                            }
                        }
                    }

                    // Load replies from API
                    String repliesResponse = ApiClient.get("discussions/" + discussionId + "/replies");
                    JsonObject repliesJson = JsonParser.parseString(repliesResponse).getAsJsonObject();
                    JsonArray replies = repliesJson.has("data") ? repliesJson.getAsJsonArray("data") :
                        (repliesJson.has("replies") ? repliesJson.getAsJsonArray("replies") : new JsonArray());

                    String repliesText = formatReplies(replies);

                    final String finalAuthor = author;
                    final String finalContent = content;
                    final String finalRepliesText = repliesText;

                    javafx.application.Platform.runLater(() -> {
                        showDiscussionDialog(finalContent, finalAuthor, finalRepliesText);
                    });
                    break;
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
            javafx.application.Platform.runLater(() -> {
                showAlert("Error", "Failed to load discussion: " + e.getMessage());
            });
        }
    }

    private void loadDiscussionFromDatabase(String title) {
        try {
            // First, get the discussion from database
            // For now, show a message
            javafx.application.Platform.runLater(() -> {
                showDiscussionDialog(
                    "📡 Offline Mode\n\nYou are currently offline. " +
                        "Please connect to the internet to view full discussions and replies.\n\n" +
                        "Your saved replies will sync automatically when you go online.",
                    "Offline",
                    "💡 Connect to internet to see replies"
                );
            });
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private String formatReplies(JsonArray replies) {
        StringBuilder repliesText = new StringBuilder();
        if (replies.size() == 0) {
            repliesText.append("📭 No replies yet. Be the first to reply!");
        } else {
            repliesText.append("--- Replies (").append(replies.size()).append(") ---\n");
            for (int j = 0; j < replies.size(); j++) {
                JsonObject reply = replies.get(j).getAsJsonObject();
                String replyAuthor = "Unknown";
                if (reply.has("user")) {
                    JsonObject user = reply.get("user").getAsJsonObject();
                    if (user.has("first_name")) {
                        replyAuthor = user.get("first_name").getAsString();
                        if (user.has("last_name")) {
                            replyAuthor += " " + user.get("last_name").getAsString();
                        }
                    }
                }
                String body = reply.has("body") ? reply.get("body").getAsString() : "";
                repliesText.append("  • ").append(replyAuthor).append(": ").append(body).append("\n");
            }
        }
        return repliesText.toString();
    }

    private void showDiscussionDialog(String content, String author, String repliesText) {
        Dialog<Void> dialog = new Dialog<>();
        dialog.setTitle("Discussion Content");
        dialog.setHeaderText("By: " + author);

        ButtonType closeButton = new ButtonType("Close", ButtonType.OK.getButtonData());
        dialog.getDialogPane().getButtonTypes().addAll(closeButton);

        VBox vbox = new VBox(10);
        vbox.setPadding(new Insets(20));

        // Content area
        Label contentLabel = new Label("📝 Content:");
        contentLabel.setStyle("-fx-font-weight: bold;");
        TextArea contentArea = new TextArea(content);
        contentArea.setEditable(false);
        contentArea.setWrapText(true);
        contentArea.setPrefHeight(150);
        contentArea.setPrefWidth(500);

        // Replies area
        Label repliesLabel = new Label("💬 Replies:");
        repliesLabel.setStyle("-fx-font-weight: bold;");
        TextArea repliesArea = new TextArea(repliesText);
        repliesArea.setEditable(false);
        repliesArea.setWrapText(true);
        repliesArea.setPrefHeight(150);
        repliesArea.setPrefWidth(500);

        vbox.getChildren().addAll(contentLabel, contentArea, repliesLabel, repliesArea);
        dialog.getDialogPane().setContent(vbox);

        dialog.showAndWait();
    }

    private void loadRepliesForDiscussion(int discussionId) {
        new Thread(() -> {
            try {
                String response = ApiClient.get("discussions/" + discussionId + "/replies");
                System.out.println("📥 Replies Response: " + response);

                JsonObject json = JsonParser.parseString(response).getAsJsonObject();
                JsonArray replies = json.has("data") ? json.getAsJsonArray("data") :
                    (json.has("replies") ? json.getAsJsonArray("replies") : new JsonArray());

                javafx.application.Platform.runLater(() -> {
                    if (replies.size() == 0) {
                        System.out.println("📭 No replies yet");
                    } else {
                        System.out.println("📝 Found " + replies.size() + " replies");
                        for (int i = 0; i < replies.size(); i++) {
                            JsonObject reply = replies.get(i).getAsJsonObject();
                            String author = "Unknown";
                            if (reply.has("user")) {
                                JsonObject user = reply.get("user").getAsJsonObject();
                                if (user.has("first_name")) {
                                    author = user.get("first_name").getAsString();
                                    if (user.has("last_name")) {
                                        author += " " + user.get("last_name").getAsString();
                                    }
                                }
                            }
                            String body = reply.has("body") ? reply.get("body").getAsString() : "";
                            System.out.println("  • " + author + ": " + body);
                        }
                    }
                });

            } catch (Exception e) {
                e.printStackTrace();
            }
        }).start();
    }

    @FXML
    private void handleCreateDiscussion() {
        if (!NetworkUtils.isOnline()) {
            showAlert("Offline", "You need to be online to create a discussion.");
            return;
        }

        Dialog<String[]> dialog = new Dialog<>();
        dialog.setTitle("Create Discussion");
        dialog.setHeaderText("Create a new discussion in " + groupName);

        ButtonType createButton = new ButtonType("Create", ButtonType.OK.getButtonData());
        dialog.getDialogPane().getButtonTypes().addAll(createButton, ButtonType.CANCEL);

        TextField titleField = new TextField();
        titleField.setPromptText("Discussion title");

        TextArea contentArea = new TextArea();
        contentArea.setPromptText("Discussion content...");
        contentArea.setPrefHeight(150);

        VBox vbox = new VBox(10);
        vbox.setPadding(new Insets(20));
        vbox.getChildren().addAll(
            new Label("Title:"),
            titleField,
            new Label("Content:"),
            contentArea
        );

        dialog.getDialogPane().setContent(vbox);

        javafx.application.Platform.runLater(() -> titleField.requestFocus());

        dialog.setResultConverter(dialogButton -> {
            if (dialogButton == createButton) {
                String title = titleField.getText().trim();
                String content = contentArea.getText().trim();
                if (!title.isEmpty() && !content.isEmpty()) {
                    return new String[]{title, content};
                }
            }
            return null;
        });

        dialog.showAndWait().ifPresent(result -> {
            String title = result[0];
            String content = result[1];

            new Thread(() -> {
                try {
                    String json = String.format("{\"title\":\"%s\", \"body\":\"%s\"}", title, content);
                    String response = ApiClient.post("groups/" + groupId + "/discussions", json);
                    System.out.println("Create response: " + response);

                    javafx.application.Platform.runLater(() -> {
                        loadDiscussions();
                        showAlert("Success", "✅ Discussion created: " + title);
                    });

                } catch (Exception e) {
                    e.printStackTrace();
                    javafx.application.Platform.runLater(() -> {
                        showAlert("Error", "Failed to create discussion: " + e.getMessage());
                    });
                }
            }).start();
        });
    }

    @FXML
    private void handleReply() {
        String selected = discussionsList.getSelectionModel().getSelectedItem();
        if (selected == null || selected.contains("No discussions") || selected.contains("Failed")) {
            showAlert("Error", "Please select a discussion first");
            return;
        }

        String title = selected.replaceAll("💬 ", "").split(" \\(by ")[0];

        new Thread(() -> {
            try {
                String response = ApiClient.get("groups/" + groupId + "/discussions");
                JsonObject json = JsonParser.parseString(response).getAsJsonObject();
                JsonArray data = json.has("discussions") ? json.getAsJsonArray("discussions") :
                    (json.has("data") ? json.getAsJsonArray("data") : new JsonArray());

                int discussionId = -1;
                for (int i = 0; i < data.size(); i++) {
                    JsonObject disc = data.get(i).getAsJsonObject();
                    if (disc.get("title").getAsString().equals(title)) {
                        discussionId = disc.get("id").getAsInt();
                        break;
                    }
                }

                final int finalDiscussionId = discussionId;

                javafx.application.Platform.runLater(() -> {
                    if (finalDiscussionId == -1) {
                        showAlert("Error", "Could not find discussion");
                        return;
                    }
                    showReplyDialog(finalDiscussionId, title);
                });

            } catch (Exception e) {
                e.printStackTrace();
            }
        }).start();
    }

    private void showReplyDialog(int discussionId, String title) {
        Dialog<String> dialog = new Dialog<>();
        dialog.setTitle("Reply to Discussion");
        dialog.setHeaderText("Reply to: " + title);

        ButtonType replyButton = new ButtonType("Reply", ButtonType.OK.getButtonData());
        dialog.getDialogPane().getButtonTypes().addAll(replyButton, ButtonType.CANCEL);

        TextArea contentArea = new TextArea();
        contentArea.setPromptText("Write your reply...");
        contentArea.setPrefHeight(150);

        VBox vbox = new VBox(10);
        vbox.setPadding(new Insets(20));
        vbox.getChildren().addAll(
            new Label("Your Reply:"),
            contentArea
        );

        dialog.getDialogPane().setContent(vbox);

        javafx.application.Platform.runLater(() -> contentArea.requestFocus());

        dialog.setResultConverter(dialogButton -> {
            if (dialogButton == replyButton) {
                String content = contentArea.getText().trim();
                if (!content.isEmpty()) {
                    return content;
                }
            }
            return null;
        });

        dialog.showAndWait().ifPresent(content -> {
            System.out.println("Replying to: " + title);

            if (!NetworkUtils.isOnline()) {
                // Offline - save to database
                db.saveReply(discussionId, Session.getUser().getId(),
                    Session.getUser().getFullName(), content);

                // Show a message and keep the discussion view open
                javafx.application.Platform.runLater(() -> {
                    showAlert("Offline", "✅ Reply saved locally. It will appear when you go online.");
                    // Reload the discussion from database to show the reply
                    loadDiscussionFromDatabase(title);
                });
                return;
            }

            // Online - send to server
            new Thread(() -> {
                try {
                    String json = String.format("{\"body\":\"%s\"}", content);
                    String response = ApiClient.post("discussions/" + discussionId + "/replies", json);
                    System.out.println("Reply response: " + response);

                    javafx.application.Platform.runLater(() -> {
                        showAlert("Success", "✅ Reply added!");
                        // Reload the discussion to show the new reply
                        loadDiscussionFromApi(title);
                    });

                } catch (Exception e) {
                    e.printStackTrace();
                    javafx.application.Platform.runLater(() -> {
                        showAlert("Error", "Failed to add reply: " + e.getMessage());
                    });
                }
            }).start();
        });
    }

    private void showAlert(String title, String message) {
        Alert alert = new Alert(Alert.AlertType.INFORMATION);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }
}
