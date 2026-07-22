package com.edudiscuss.controllers;

import com.edudiscuss.Main;
import com.edudiscuss.api.ApiClient;
import com.edudiscuss.api.ApiResult;
import com.edudiscuss.models.User;
import com.edudiscuss.utils.Session;
import javafx.application.Platform;
import javafx.beans.property.SimpleStringProperty;
import javafx.collections.FXCollections;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.HBox;
import javafx.stage.Stage;

import java.io.IOException;
import java.util.List;
import java.util.Objects;
import java.util.Optional;

public class ManageUsersController {

    @FXML private TableView<User>           usersTable;
    @FXML private TableColumn<User, String> colName;
    @FXML private TableColumn<User, String> colEmail;
    @FXML private TableColumn<User, String> colRole;
    @FXML private TableColumn<User, String> colStatus;
    @FXML private TableColumn<User, String> colWarnings;
    @FXML private TableColumn<User, Void>   colActions;
    @FXML private TextField                 searchField;
    @FXML private Label                     statusLabel;
    @FXML private Label                     userNameLabel;
     @FXML
    private Button logoutButton;

    @FXML
    public void initialize() {
        userNameLabel.setText(Session.getInstance().getCurrentUser().getFullName());
        setupColumns();
        loadUsers(null);
        searchField.setOnAction(e -> handleSearch());
    }

    private void setupColumns() {
        colName.setCellValueFactory(c -> new SimpleStringProperty(c.getValue().getFullName()));
        colEmail.setCellValueFactory(c -> new SimpleStringProperty(c.getValue().getEmail()));
        colRole.setCellValueFactory(c -> new SimpleStringProperty(c.getValue().getRoleLabel()));
        colStatus.setCellValueFactory(c -> new SimpleStringProperty(c.getValue().getStatus()));
        colWarnings.setCellValueFactory(c -> new SimpleStringProperty(c.getValue().getWarningCount() + " / 2"));

        colStatus.setCellFactory(col -> new TableCell<>() {
            @Override
            protected void updateItem(String item, boolean empty) {
                super.updateItem(item, empty);
                if (empty || item == null) { setText(null); setStyle(""); return; }
                setText(item);
                setStyle(switch (item) {
                    case "blacklisted" -> "-fx-text-fill: #e74c3c; -fx-font-weight: bold;";
                    case "pending"     -> "-fx-text-fill: #f39c12;";
                    default            -> "-fx-text-fill: #27ae60;";
                });
            }
        });

        colActions.setCellFactory(col -> new TableCell<>() {
            @Override
            protected void updateItem(Void item, boolean empty) {
                super.updateItem(item, empty);
                if (empty) { setGraphic(null); return; }
                User user = getTableView().getItems().get(getIndex());
                int currentUserId = Session.getInstance().getCurrentUser().getId();

                // Admin cannot act on their own row
                if (user.getId() == currentUserId) {
                    Label you = new Label("(you)");
                    you.setStyle("-fx-text-fill: #888;");
                    setGraphic(you);
                    return;
                }

                HBox box = new HBox(6);
                box.setAlignment(Pos.CENTER_LEFT);
                box.setPadding(new Insets(2, 0, 2, 0));

                if (user.isBlacklisted()) {
                    Button reinstate = actionButton("Reinstate", "#27ae60");
                    reinstate.setOnAction(e -> doUnblacklist(user));
                    box.getChildren().add(reinstate);
                } else {
                    Button warn      = actionButton("Warn",      "#f39c12");
                    Button blacklist = actionButton("Blacklist",  "#e74c3c");
                    Button logout    = actionButton("Log out",   "#666");
                    warn.setOnAction(e      -> doWarn(user));
                    blacklist.setOnAction(e -> doBlacklist(user));
                    logout.setOnAction(e    -> doLogout(user));
                    box.getChildren().addAll(warn, blacklist, logout);
                }

                setGraphic(box);
            }
        });
    }

    private Button actionButton(String text, String color) {
        Button btn = new Button(text);
        btn.setStyle(
            "-fx-background-color: transparent;" +
            "-fx-border-color: " + color + ";" +
            "-fx-border-radius: 4;" +
            "-fx-text-fill: " + color + ";" +
            "-fx-font-size: 11px;" +
            "-fx-padding: 2 8 2 8;" +
            "-fx-cursor: hand;"
        );
        return btn;
    }

    private void loadUsers(String search) {
        setStatus("Loading…");
        new Thread(() -> {
            ApiResult<List<User>> result = ApiClient.adminUsers(search);
            Platform.runLater(() -> {
                if (!result.isOk()) { setStatus("Error: " + result.getError()); return; }
                usersTable.setItems(FXCollections.observableArrayList(result.getValue()));
                setStatus(result.getValue().size() + " users");
            });
        }).start();
    }

    private void doBlacklist(User user) {
        if (!confirm("Blacklist " + user.getFullName() + "?")) return;
        runAction(() -> ApiClient.blacklistUser(user.getId()));
    }

    private void doUnblacklist(User user) {
        if (!confirm("Reinstate " + user.getFullName() + "?")) return;
        runAction(() -> ApiClient.unblacklistUser(user.getId()));
    }

    private void doWarn(User user) {
        TextInputDialog dialog = new TextInputDialog();
        dialog.setTitle("Warn " + user.getFullName());
        dialog.setHeaderText("Send a warning message to " + user.getFullName());
        dialog.setContentText("Violation / reason:");
        styleDialog(dialog);
        Optional<String> result = dialog.showAndWait();
        result.filter(s -> !s.isBlank()).ifPresent(msg ->
            runAction(() -> ApiClient.warnUser(user.getId(), msg))
        );
    }

    private void doLogout(User user) {
        if (!confirm("Force-log-out " + user.getFullName() + "?")) return;
        runAction(() -> ApiClient.logoutUser(user.getId()));
    }

    private void runAction(java.util.function.Supplier<ApiResult<String>> action) {
        new Thread(() -> {
            ApiResult<String> result = action.get();
            Platform.runLater(() -> {
                if (result.isOk()) {
                    showAlert(Alert.AlertType.INFORMATION, "Done", result.getValue());
                    loadUsers(searchField.getText().trim());
                } else {
                    showAlert(Alert.AlertType.ERROR, "Error", result.getError());
                }
            });
        }).start();
    }

    @FXML private void handleSearch()    { loadUsers(searchField.getText().trim()); }
    @FXML private void handleRefresh()   { loadUsers(searchField.getText().trim()); }
    @FXML private void goDashboard()     { Main.showAdminDashboard(); }
    @FXML private void goAddLecturer()   { Main.showAddLecturer(); }

    @FXML
private void handleLogout(javafx.event.ActionEvent event) {
    try {
        Session.clear();

        FXMLLoader loader =
                new FXMLLoader(getClass().getResource("/views/login.fxml"));

        Scene scene = new Scene(loader.load());

        scene.getStylesheets().add(
                Objects.requireNonNull(
                        getClass().getResource("/css/style.css")
                ).toExternalForm()
        );

        Stage stage =
                (Stage) ((Button) event.getSource())
                        .getScene()
                        .getWindow();

        stage.setScene(scene);
        stage.show();

    } catch (IOException e) {
        e.printStackTrace();
    }
}

    private boolean confirm(String message) {
        Alert alert = new Alert(Alert.AlertType.CONFIRMATION, message, ButtonType.YES, ButtonType.NO);
        alert.setHeaderText(null);
        styleDialog(alert);
        Optional<ButtonType> result = alert.showAndWait();
        return result.isPresent() && result.get() == ButtonType.YES;
    }

    private void showAlert(Alert.AlertType type, String title, String content) {
        Alert alert = new Alert(type, content, ButtonType.OK);
        alert.setTitle(title);
        alert.setHeaderText(null);
        styleDialog(alert);
        alert.showAndWait();
    }

    private void styleDialog(Dialog<?> d) {
        d.getDialogPane().getStylesheets().add(
            getClass().getResource("/css/style.css").toExternalForm()
        );
    }

    private void setStatus(String text) { statusLabel.setText(text); }
}
