package com.edudiscuss.controllers;

import com.edudiscuss.api.ApiClient;
import com.edudiscuss.database.DatabaseHelper;
import com.edudiscuss.utils.NetworkUtils;
import com.edudiscuss.utils.SyncManager;
import com.google.gson.JsonArray;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.ListView;
import javafx.scene.control.TextField;
import javafx.scene.control.TextInputDialog;
import javafx.stage.Stage;

import java.sql.ResultSet;

public class GroupsController {

    @FXML private ListView<String> myGroupsList;
    @FXML private ListView<String> availableGroupsList;
    @FXML private TextField searchField;

    private ObservableList<String> myGroups = FXCollections.observableArrayList();
    private ObservableList<String> availableGroups = FXCollections.observableArrayList();
    private DatabaseHelper db;

    @FXML
    public void initialize() {
        db = DatabaseHelper.getInstance();
        System.out.println("✅ GroupsController initialized");
        loadGroups();

        myGroupsList.setOnMouseClicked(event -> {
            if (event.getClickCount() == 2) {
                String selected = myGroupsList.getSelectionModel().getSelectedItem();
                if (selected != null) {
                    openGroup(selected);
                }
            }
        });
    }

    private void loadGroups() {
        new Thread(() -> {
            if (NetworkUtils.isOnline()) {
                loadGroupsFromApi();
            } else {
                System.out.println("📡 Offline - Loading groups from database");
                javafx.application.Platform.runLater(() -> loadGroupsFromDatabase());
            }
        }).start();
    }

    private void loadGroupsFromApi() {
        try {
            String response = ApiClient.get("groups");
            System.out.println("📥 API Response: " + response);

            JsonObject json = JsonParser.parseString(response).getAsJsonObject();
            JsonArray groups = json.getAsJsonArray("groups");

            javafx.application.Platform.runLater(() -> {
                myGroups.clear();
                availableGroups.clear();

                if (groups.size() == 0) {
                    myGroups.add("📭 No groups found");
                    myGroupsList.setItems(myGroups);
                    return;
                }

                for (int i = 0; i < groups.size(); i++) {
                    JsonObject group = groups.get(i).getAsJsonObject();
                    int id = group.get("id").getAsInt();
                    String name = group.get("name").getAsString();

                    boolean isMember = group.has("is_member") &&
                        group.get("is_member").getAsBoolean();

                    if (isMember) {
                        myGroups.add(id + " - " + name);
                        db.saveGroup(id, name, "", true);
                    } else {
                        availableGroups.add(id + " - " + name);
                        db.saveGroup(id, name, "", false);
                    }
                }

                myGroupsList.setItems(myGroups);
                availableGroupsList.setItems(availableGroups);
                System.out.println("✅ Groups loaded from API: " + myGroups.size() + " joined, " + availableGroups.size() + " available");
            });

        } catch (Exception e) {
            e.printStackTrace();
            javafx.application.Platform.runLater(() -> {
                System.out.println("⚠️ API failed, loading from database");
                loadGroupsFromDatabase();
            });
        }
    }

    private void loadGroupsFromDatabase() {
        try {
            myGroups.clear();
            availableGroups.clear();

            ResultSet rs = db.getGroups(true);
            if (rs != null) {
                while (rs.next()) {
                    String name = rs.getString("name");
                    int id = rs.getInt("id");
                    myGroups.add(id + " - " + name);
                }
            }

            rs = db.getGroups(false);
            if (rs != null) {
                while (rs.next()) {
                    String name = rs.getString("name");
                    int id = rs.getInt("id");
                    availableGroups.add(id + " - " + name);
                }
            }

            myGroupsList.setItems(myGroups);
            availableGroupsList.setItems(availableGroups);

            if (myGroups.isEmpty() && availableGroups.isEmpty()) {
                myGroups.add("📭 No groups found offline");
                myGroupsList.setItems(myGroups);
            }

            System.out.println("✅ Groups loaded from database: " + myGroups.size() + " joined, " + availableGroups.size() + " available");

        } catch (Exception e) {
            e.printStackTrace();
            myGroups.clear();
            myGroups.add("⚠️ Failed to load groups");
            myGroupsList.setItems(myGroups);
        }
    }

    @FXML
    private void filterGroups() {
        String query = searchField.getText().toLowerCase();

        if (query.isEmpty()) {
            myGroupsList.setItems(myGroups);
            availableGroupsList.setItems(availableGroups);
            return;
        }

        ObservableList<String> filteredMy = FXCollections.observableArrayList();
        for (String g : myGroups) {
            if (g.toLowerCase().contains(query)) {
                filteredMy.add(g);
            }
        }

        ObservableList<String> filteredAvailable = FXCollections.observableArrayList();
        for (String g : availableGroups) {
            if (g.toLowerCase().contains(query)) {
                filteredAvailable.add(g);
            }
        }

        myGroupsList.setItems(filteredMy);
        availableGroupsList.setItems(filteredAvailable);
    }

    @FXML
    public void joinGroup() {
        if (!NetworkUtils.isOnline()) {
            Alert alert = new Alert(Alert.AlertType.INFORMATION);
            alert.setTitle("Offline");
            alert.setHeaderText(null);
            alert.setContentText("You need to be online to join a group.");
            alert.showAndWait();
            return;
        }

        String selected = availableGroupsList.getSelectionModel().getSelectedItem();
        if (selected == null) {
            Alert alert = new Alert(Alert.AlertType.INFORMATION);
            alert.setTitle("Error");
            alert.setHeaderText(null);
            alert.setContentText("Please select a group first");
            alert.showAndWait();
            return;
        }

        String[] parts = selected.split(" - ");
        String groupId = parts[0];
        String groupName = parts[1];

        System.out.println("Joining: " + groupName);

        new Thread(() -> {
            try {
                String response = ApiClient.post("groups/" + groupId + "/join", "");
                System.out.println("Join response: " + response);

                javafx.application.Platform.runLater(() -> {
                    loadGroups();
                    System.out.println("✅ Joined group: " + groupName);
                });

            } catch (Exception e) {
                e.printStackTrace();
                javafx.application.Platform.runLater(() -> {
                    Alert alert = new Alert(Alert.AlertType.ERROR);
                    alert.setTitle("Error");
                    alert.setHeaderText(null);
                    alert.setContentText("Failed to join group: " + e.getMessage());
                    alert.showAndWait();
                });
            }
        }).start();
    }

    private void openGroup(String selected) {
        try {
            String[] parts = selected.split(" - ");
            int groupId = Integer.parseInt(parts[0]);
            String groupName = parts[1];

            System.out.println("Opening group: " + groupName + " (ID: " + groupId + ")");

            FXMLLoader loader = new FXMLLoader(
                getClass().getResource("/views/student/discussions.fxml")
            );
            Scene scene = new Scene(loader.load());

            DiscussionsController controller = loader.getController();
            controller.setGroup(groupId, groupName);

            Stage stage = (Stage) myGroupsList.getScene().getWindow();
            stage.setScene(scene);
            stage.show();

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleCreateGroup() {
        if (!NetworkUtils.isOnline()) {
            Alert alert = new Alert(Alert.AlertType.INFORMATION);
            alert.setTitle("Offline");
            alert.setHeaderText(null);
            alert.setContentText("You need to be online to create a group.");
            alert.showAndWait();
            return;
        }

        TextInputDialog nameDialog = new TextInputDialog();
        nameDialog.setTitle("Create Group");
        nameDialog.setHeaderText("Create a new discussion group");
        nameDialog.setContentText("Enter group name:");

        nameDialog.showAndWait().ifPresent(groupName -> {
            if (groupName.trim().isEmpty()) {
                Alert alert = new Alert(Alert.AlertType.INFORMATION);
                alert.setTitle("Error");
                alert.setHeaderText(null);
                alert.setContentText("Group name cannot be empty");
                alert.showAndWait();
                return;
            }

            TextInputDialog descDialog = new TextInputDialog();
            descDialog.setTitle("Create Group");
            descDialog.setHeaderText(null);
            descDialog.setContentText("Enter group description (optional):");

            String description = descDialog.showAndWait().orElse("");

            System.out.println("Creating group: " + groupName);

            new Thread(() -> {
                try {
                    String json = String.format(
                        "{\"name\":\"%s\", \"description\":\"%s\"}",
                        groupName, description
                    );
                    String response = ApiClient.post("groups", json);
                    System.out.println("Create response: " + response);

                    javafx.application.Platform.runLater(() -> {
                        loadGroups();
                        System.out.println("✅ Group created: " + groupName);
                    });

                } catch (Exception e) {
                    e.printStackTrace();
                    javafx.application.Platform.runLater(() -> {
                        Alert alert = new Alert(Alert.AlertType.ERROR);
                        alert.setTitle("Error");
                        alert.setHeaderText(null);
                        alert.setContentText("Failed to create group: " + e.getMessage());
                        alert.showAndWait();
                    });
                }
            }).start();
        });
    }

    @FXML
    private void handleSync() {
        if (!NetworkUtils.isOnline()) {
            Alert alert = new Alert(Alert.AlertType.INFORMATION);
            alert.setTitle("Offline");
            alert.setHeaderText(null);
            alert.setContentText("Cannot sync while offline. Please connect to internet.");
            alert.showAndWait();
            return;
        }

        System.out.println("🔄 Syncing...");
        SyncManager syncManager = new SyncManager();
        syncManager.syncAll();

        Alert alert = new Alert(Alert.AlertType.INFORMATION);
        alert.setTitle("Sync");
        alert.setHeaderText(null);
        alert.setContentText("✅ Sync completed!");
        alert.showAndWait();
    }
}
