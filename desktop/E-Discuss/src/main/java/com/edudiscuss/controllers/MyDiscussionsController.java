package com.edudiscuss.controllers;

import com.edudiscuss.api.ApiClient;
import com.edudiscuss.utils.NetworkUtils;
import com.google.gson.JsonArray;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.Label;
import javafx.scene.control.ListView;
import javafx.stage.Stage;

import java.util.HashMap;
import java.util.Map;

public class MyDiscussionsController {

    @FXML private Label pageTitle;
    @FXML private ListView<String> startedList;
    @FXML private ListView<String> repliedList;

    private ObservableList<String> startedItems = FXCollections.observableArrayList();
    private ObservableList<String> repliedItems = FXCollections.observableArrayList();

    // Maps the display string back to its group/discussion IDs, so we can
    // open the right discussion on double-click without re-parsing text.
    private final Map<String, int[]> startedLookup = new HashMap<>();
    private final Map<String, int[]> repliedLookup = new HashMap<>();

    @FXML
    public void initialize() {
        pageTitle.setText("💬 My Discussions");
        loadMyDiscussions();

        startedList.setOnMouseClicked(event -> {
            if (event.getClickCount() == 2) {
                String selected = startedList.getSelectionModel().getSelectedItem();
                if (selected != null && startedLookup.containsKey(selected)) {
                    int[] ids = startedLookup.get(selected);
                    openDiscussion(ids[0], ids[1]);
                }
            }
        });

        repliedList.setOnMouseClicked(event -> {
            if (event.getClickCount() == 2) {
                String selected = repliedList.getSelectionModel().getSelectedItem();
                if (selected != null && repliedLookup.containsKey(selected)) {
                    int[] ids = repliedLookup.get(selected);
                    openDiscussion(ids[0], ids[1]);
                }
            }
        });
    }

    @FXML
    private void handleRefresh() {
        loadMyDiscussions();
    }

    private void loadMyDiscussions() {
        if (!NetworkUtils.isOnline()) {
            startedItems.setAll("📡 Offline - connect to internet to see your discussions");
            repliedItems.clear();
            startedList.setItems(startedItems);
            repliedList.setItems(repliedItems);
            return;
        }

        new Thread(() -> {
            try {
                String response = ApiClient.get("my-discussions");
                System.out.println("📥 My Discussions Response: " + response);

                JsonObject json = JsonParser.parseString(response).getAsJsonObject();
                JsonArray started = json.has("started") ? json.getAsJsonArray("started") : new JsonArray();
                JsonArray replied = json.has("replied") ? json.getAsJsonArray("replied") : new JsonArray();

                javafx.application.Platform.runLater(() -> {
                    populateList(started, startedItems, startedLookup, startedList, "You haven't started any discussions yet.");
                    populateList(replied, repliedItems, repliedLookup, repliedList, "You haven't replied to any discussions yet.");
                });

            } catch (Exception e) {
                e.printStackTrace();
                javafx.application.Platform.runLater(() -> {
                    startedItems.setAll("⚠️ Failed to load discussions");
                    repliedItems.clear();
                    startedList.setItems(startedItems);
                    repliedList.setItems(repliedItems);
                });
            }
        }).start();
    }

    private void populateList(JsonArray data, ObservableList<String> items,
                              Map<String, int[]> lookup, ListView<String> listView,
                              String emptyMessage) {
        items.clear();
        lookup.clear();

        if (data.size() == 0) {
            items.add("📭 " + emptyMessage);
            listView.setItems(items);
            return;
        }

        for (int i = 0; i < data.size(); i++) {
            JsonObject disc = data.get(i).getAsJsonObject();
            String title = disc.has("title") ? disc.get("title").getAsString() : "Untitled";
            int discussionId = disc.get("id").getAsInt();
            int groupId = disc.get("group_id").getAsInt();

            String groupName = "Unknown group";
            if (disc.has("group") && !disc.get("group").isJsonNull()) {
                JsonObject group = disc.get("group").getAsJsonObject();
                if (group.has("name")) {
                    groupName = group.get("name").getAsString();
                }
            }

            String display = "💬 " + title + "  (in " + groupName + ")";
            items.add(display);
            lookup.put(display, new int[]{groupId, discussionId});
        }

        listView.setItems(items);
    }

    private void openDiscussion(int groupId, int discussionId) {
        try {
            FXMLLoader loader = new FXMLLoader(
                getClass().getResource("/views/student/discussions.fxml")
            );
            Scene scene = new Scene(loader.load());

            DiscussionsController controller = loader.getController();
            controller.setGroup(groupId, "Group");

            Stage stage = (Stage) startedList.getScene().getWindow();
            stage.setScene(scene);
            stage.show();

        } catch (Exception e) {
            e.printStackTrace();
            showAlert("Error", "Failed to open discussion: " + e.getMessage());
        }
    }

    private void showAlert(String title, String message) {
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }
}
