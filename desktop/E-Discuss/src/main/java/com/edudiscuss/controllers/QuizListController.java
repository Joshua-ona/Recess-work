package com.edudiscuss.controllers;

import com.edudiscuss.api.QuizApi;
import com.edudiscuss.models.Quiz;
import com.edudiscuss.models.QuizListResponse;
import com.edudiscuss.utils.Navigator;
import com.edudiscuss.utils.QuizLockService;
import com.google.gson.Gson;
import javafx.collections.FXCollections;
import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.scene.control.cell.PropertyValueFactory;

import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.time.format.DateTimeParseException;

public class QuizListController {

    @FXML private Label subLabel;
    @FXML private Label errorLabel;
    @FXML private TableView<Quiz> quizTable;
    @FXML private TableColumn<Quiz, String> titleCol;
    @FXML private TableColumn<Quiz, String> startCol;
    @FXML private TableColumn<Quiz, String> durationCol;
    @FXML private TableColumn<Quiz, Void> actionCol;

    private static final Gson gson = new Gson();
    private static final DateTimeFormatter DISPLAY_FORMAT = DateTimeFormatter.ofPattern("MMM d, yyyy h:mm a");


    @FXML
    public void goToCreate() {

        Navigator.goTo(
                quizTable,
                "/views/lecturer/quiz-create.fxml"
        );
    }
    @FXML
    public void initialize() {
        // Same rule as the web app: if this student is locked into a
        // quiz, they shouldn't be looking at the list at all.
        if (QuizLockService.enforceLock(quizTable)) {
            return;
        }

        titleCol.setCellValueFactory(new PropertyValueFactory<>("title"));
        durationCol.setCellValueFactory(data ->
                new javafx.beans.property.SimpleStringProperty(data.getValue().getDurationMins() + " mins"));
        startCol.setCellValueFactory(data ->
                new javafx.beans.property.SimpleStringProperty(formatStart(data.getValue().getStartTime())));

        actionCol.setCellFactory(col -> new TableCell<>() {
            private final Button startBtn = new Button();

            @Override
            protected void updateItem(Void item, boolean empty) {
                super.updateItem(item, empty);
                if (empty) {
                    setGraphic(null);
                    return;
                }
                Quiz quiz = getTableView().getItems().get(getIndex());
                boolean locked = isBeforeStart(quiz.getStartTime());

                startBtn.setText(locked ? "Opens soon" : "Start quiz");
                startBtn.setDisable(locked);
                startBtn.getStyleClass().setAll(locked ? "btn-locked" : "btn-primary");
                startBtn.setOnAction(e -> startQuiz(quiz));
                setGraphic(startBtn);
            }
        });

        loadQuizzes();
    }

    private void loadQuizzes() {
        try {
            var response = QuizApi.available();
            if (!response.isOk()) {
                showError("Couldn't load quizzes. Please try again.");
                return;
            }

            QuizListResponse parsed = gson.fromJson(response.body, QuizListResponse.class);
            var quizzes = parsed.getQuizzes() != null ? parsed.getQuizzes() : java.util.List.<Quiz>of();

            quizTable.setItems(FXCollections.observableArrayList(quizzes));
            subLabel.setText(quizzes.size() + " quiz" + (quizzes.size() == 1 ? "" : "zes") + " published");
        } catch (Exception e) {
            e.printStackTrace();
            showError("Network error loading quizzes.");
        }
    }

    private void startQuiz(Quiz quiz) {
        try {
            var response = QuizApi.start(quiz.getQuizId());

            if (response.isOk()) {
                var startData = gson.fromJson(response.body, com.edudiscuss.models.QuizStartResponse.class);
                QuizAttemptController controller = Navigator.goToWithController(
                        quizTable, "/views/student/quiz-attempt.fxml");
                if (controller != null) {
                    controller.loadAttempt(quiz.getQuizId(), startData);
                }
                return;
            }

            // 403 (not open yet / window closed), 409 (already submitted /
            // another quiz in progress), 410 (time already ran out) — show
            // the server's message the same way the web app's flash error does.
            var errorBody = gson.fromJson(response.body, com.edudiscuss.models.QuizStartResponse.class);
            showError(errorBody.getMessage() != null ? errorBody.getMessage() : "Couldn't start this quiz.");
            loadQuizzes();
        } catch (Exception e) {
            e.printStackTrace();
            showError("Network error starting quiz.");
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

    private boolean isBeforeStart(String isoStart) {
        try {
            java.time.Instant start = java.time.Instant.parse(isoStart);
            return java.time.Instant.now().isBefore(start);
        } catch (Exception e) {
            return false;
        }
    }
}
