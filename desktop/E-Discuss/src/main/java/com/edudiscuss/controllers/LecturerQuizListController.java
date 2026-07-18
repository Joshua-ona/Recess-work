package com.edudiscuss.controllers;

import com.edudiscuss.api.LecturerQuizApi;
import com.edudiscuss.models.Quiz;
import com.edudiscuss.models.QuizListResponse;
import com.edudiscuss.utils.Navigator;
import com.google.gson.Gson;
import javafx.collections.FXCollections;
import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.scene.layout.HBox;

import java.time.Instant;
import java.time.ZoneId;
import java.time.format.DateTimeFormatter;

public class LecturerQuizListController {

    @FXML private Label subLabel;
    @FXML private Label errorLabel;
    @FXML private TableView<Quiz> quizTable;
    @FXML private TableColumn<Quiz, String> titleCol;
    @FXML private TableColumn<Quiz, String> startCol;
    @FXML private TableColumn<Quiz, String> durationCol;
    @FXML private TableColumn<Quiz, String> statusCol;
    @FXML private TableColumn<Quiz, Void> actionCol;

    private static final Gson gson = new Gson();
    private static final DateTimeFormatter DISPLAY_FORMAT = DateTimeFormatter.ofPattern("MMM d, yyyy h:mm a");

    @FXML
    public void initialize() {
        titleCol.setCellValueFactory(new PropertyValueFactory<>("title"));
        durationCol.setCellValueFactory(d ->
            new javafx.beans.property.SimpleStringProperty(d.getValue().getDurationMins() + " mins"));
        startCol.setCellValueFactory(d ->
            new javafx.beans.property.SimpleStringProperty(formatStart(d.getValue().getStartTime())));
        statusCol.setCellValueFactory(d ->
            new javafx.beans.property.SimpleStringProperty(d.getValue().isPublished() ? "Published" : "Draft"));

        actionCol.setCellFactory(col -> new TableCell<>() {
            private final Button editBtn = new Button("Edit");
            private final Button publishBtn = new Button("Publish");
            private final Button deleteBtn = new Button("Delete");
            private final HBox box = new HBox(6, editBtn, publishBtn, deleteBtn);

            @Override
            protected void updateItem(Void item, boolean empty) {
                super.updateItem(item, empty);
                if (empty) {
                    setGraphic(null);
                    return;
                }
                Quiz quiz = getTableView().getItems().get(getIndex());

                editBtn.setOnAction(e -> goToEdit(quiz));
                publishBtn.setDisable(quiz.isPublished());
                publishBtn.setOnAction(e -> publish(quiz));
                deleteBtn.setOnAction(e -> delete(quiz));

                setGraphic(box);
            }
        });

        load();
    }

    private void load() {
        try {
            var response = LecturerQuizApi.list();
            if (!response.isOk()) {
                showError("Couldn't load your quizzes.");
                return;
            }
            QuizListResponse parsed = gson.fromJson(response.body, QuizListResponse.class);
            var quizzes = parsed.getQuizzes() != null ? parsed.getQuizzes() : java.util.List.<Quiz>of();
            quizTable.setItems(FXCollections.observableArrayList(quizzes));
            subLabel.setText(quizzes.size() + " quiz" + (quizzes.size() == 1 ? "" : "zes") + " total");
        } catch (Exception e) {
            e.printStackTrace();
            showError("Network error loading quizzes.");
        }
    }

    @FXML
    public void goToCreate() {
        Navigator.goTo(quizTable, "/views/lecturer/quiz-create.fxml");
    }

    private void goToEdit(Quiz quiz) {
        LecturerQuizEditController controller = Navigator.goToWithController(
            quizTable, "/views/lecturer/quiz-edit.fxml");
        if (controller != null) {
            controller.loadQuiz(quiz.getQuizId());
        }
    }

    private void publish(Quiz quiz) {
        try {
            var response = LecturerQuizApi.publish(quiz.getQuizId());
            if (response.isOk()) {
                load();
            } else {
                showError("Couldn't publish this quiz.");
            }
        } catch (Exception e) {
            e.printStackTrace();
            showError("Network error publishing quiz.");
        }
    }

    private void delete(Quiz quiz) {
        Alert confirm = new Alert(Alert.AlertType.CONFIRMATION,
            "Delete \"" + quiz.getTitle() + "\"? This can't be undone.");
        confirm.showAndWait().ifPresent(response -> {
            if (response == ButtonType.OK) {
                try {
                    var apiResponse = LecturerQuizApi.delete(quiz.getQuizId());
                    if (apiResponse.isOk()) {
                        load();
                    } else {
                        showError("Couldn't delete this quiz.");
                    }
                } catch (Exception e) {
                    e.printStackTrace();
                    showError("Network error deleting quiz.");
                }
            }
        });
    }

    private void showError(String message) {
        errorLabel.setText(message);
        errorLabel.setVisible(true);
        errorLabel.setManaged(true);
    }

    private String formatStart(String isoStart) {
        try {
            return Instant.parse(isoStart).atZone(ZoneId.of("Africa/Kampala")).format(DISPLAY_FORMAT);
        } catch (Exception e) {
            return isoStart;
        }
    }
}
