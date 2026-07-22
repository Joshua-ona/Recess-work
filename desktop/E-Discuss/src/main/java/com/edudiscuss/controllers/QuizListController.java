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

import java.time.format.DateTimeFormatter;

/**
 * Lecturer's "my quizzes" screen — lists quizzes THIS lecturer created
 * (draft or published), with Edit / Publish actions.
 *
 * Not to be confused with the student-facing quiz list, which lives in
 * StudentQuizzesController / student/quizzes.fxml.
 */
public class QuizListController {

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
    public void goToCreate() {
        Navigator.goTo(quizTable, "/views/lecturer/quiz-create.fxml");
    }

    @FXML
    public void initialize() {
        titleCol.setCellValueFactory(new PropertyValueFactory<>("title"));
        durationCol.setCellValueFactory(data ->
                new javafx.beans.property.SimpleStringProperty(data.getValue().getDurationMins() + " mins"));
        startCol.setCellValueFactory(data ->
                new javafx.beans.property.SimpleStringProperty(formatStart(data.getValue().getStartTime())));

        if (statusCol != null) {
            statusCol.setCellValueFactory(data ->
                    new javafx.beans.property.SimpleStringProperty(
                            data.getValue().isPublished() ? "Published" : "Draft"));
        }

        actionCol.setCellFactory(col -> new TableCell<>() {
            private final Button editBtn = new Button("Edit");
            private final Button publishBtn = new Button("Publish");
            private final javafx.scene.layout.HBox box = new javafx.scene.layout.HBox(6, editBtn, publishBtn);

            @Override
            protected void updateItem(Void item, boolean empty) {
                super.updateItem(item, empty);
                if (empty) {
                    setGraphic(null);
                    return;
                }
                Quiz quiz = getTableView().getItems().get(getIndex());

                editBtn.setOnAction(e -> editQuiz(quiz));

                publishBtn.setText(quiz.isPublished() ? "Published" : "Publish");
                publishBtn.setDisable(quiz.isPublished());
                publishBtn.setOnAction(e -> publishQuiz(quiz));

                setGraphic(box);
            }
        });

        loadQuizzes();
    }

    private void loadQuizzes() {
        try {
            var response = LecturerQuizApi.list();
            if (!response.isOk()) {
                showError("Couldn't load quizzes. Please try again.");
                return;
            }

            QuizListResponse parsed = gson.fromJson(response.body, QuizListResponse.class);
            var quizzes = parsed.getQuizzes() != null ? parsed.getQuizzes() : java.util.List.<Quiz>of();

            quizTable.setItems(FXCollections.observableArrayList(quizzes));
            subLabel.setText(quizzes.size() + " quiz" + (quizzes.size() == 1 ? "" : "zes") + " created");
        } catch (Exception e) {
            e.printStackTrace();
            showError("Network error loading quizzes.");
        }
    }

    private void editQuiz(Quiz quiz) {
        LecturerQuizEditController controller = Navigator.goToWithController(
                quizTable, "/views/lecturer/quiz-edit.fxml");
        if (controller != null) {
            controller.loadQuiz(quiz.getQuizId());
        }
    }

    private void publishQuiz(Quiz quiz) {
        try {
            var response = LecturerQuizApi.publish(quiz.getQuizId());
            if (response.isOk()) {
                loadQuizzes();
            } else {
                showError("Couldn't publish this quiz. Make sure it has questions first.");
            }
        } catch (Exception e) {
            e.printStackTrace();
            showError("Network error publishing quiz.");
        }
    }

    private void showError(String message) {
        errorLabel.setText(message);
        errorLabel.setVisible(true);
        errorLabel.setManaged(true);
    }

    private String formatStart(String isoStart) {
        try {
            return java.time.Instant.parse(isoStart)
                    .atZone(java.time.ZoneId.of("Africa/Kampala"))
                    .format(DISPLAY_FORMAT);
        } catch (Exception e) {
            return isoStart;
        }
    }
}