package com.edudiscuss.controllers;

import com.edudiscuss.services.ApiService;
import com.edudiscuss.models.StudentDashboard;
import com.edudiscuss.models.User;
import com.edudiscuss.services.ApiService;
import com.edudiscuss.utils.Session;
import com.edudiscuss.utils.QuizLockService;
import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;

import java.util.List;

import com.edudiscuss.models.Quiz;

public class StudentQuizzesController {
    @FXML private TableView<Quiz> quizTable;
    @FXML private TableColumn<Quiz, String> titleColumn;
    @FXML private TableColumn<Quiz, String> startTimeColumn;
    @FXML private TableColumn<Quiz, String> durationColumn;
    @FXML private TableColumn<Quiz, Void> actionColumn;
    @FXML private Label availableQuizzesLabel;

    private ObservableList<Quiz> quizList = FXCollections.observableArrayList();
    private ApiService apiService = new ApiService();

    @FXML
    public void initialize() {
        setupTableColumns();
        setupActionColumn();
        loadQuizzes();
    }

    private void setupTableColumns() {
        titleColumn.setCellValueFactory(cellData -> cellData.getValue().titleProperty());
        startTimeColumn.setCellValueFactory(cellData -> cellData.getValue().formattedStartTimeProperty());
        durationColumn.setCellValueFactory(cellData -> cellData.getValue().durationProperty());
    }

    private void setupActionColumn() {
        actionColumn.setCellFactory(col -> new TableCell<>() {
            private final Button startBtn = new Button("Start Quiz");

            {
                startBtn.setStyle(
                        "-fx-background-color: #2563eb;" +
                        "-fx-text-fill: white;" +
                        "-fx-font-weight: bold;"
                );
                startBtn.setOnAction(e -> {
                    Quiz quiz = getTableView().getItems().get(getIndex());
                    loadQuizAttempt(quiz);
                });
            }

            @Override
            protected void updateItem(Void item, boolean empty) {
                super.updateItem(item, empty);
                setGraphic(empty ? null : startBtn);
            }
        });
    }

    private void loadQuizzes(){

        try {

            List<Quiz> quizzes = apiService.getQuizzes();

            quizList.setAll(quizzes);

            quizTable.setItems(quizList);

            availableQuizzesLabel.setText(
                    quizList.size() + " quizzes published"
            );

        } catch(Exception e){

            e.printStackTrace();

            showAlert(
                    "Error",
                    "Failed to load quizzes"
            );

        }

    }

    private void showAlert(String title, String message) {
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }

    private void loadQuizAttempt(Quiz quiz) {
        try {
            var response = com.edudiscuss.api.QuizApi.start(quiz.getQuizId());

            if (!response.isOk()) {
                String serverMessage = extractMessage(response.body);
                showAlert("Error", serverMessage != null ? serverMessage : "Couldn't start this quiz. Please try again.");
                return;
            }

            com.google.gson.Gson gson = new com.google.gson.Gson();
            com.edudiscuss.models.QuizStartResponse data =
                    gson.fromJson(response.body, com.edudiscuss.models.QuizStartResponse.class);

            QuizAttemptController controller = com.edudiscuss.utils.Navigator.goToWithController(
                    quizTable, "/views/student/quiz.fxml");

            if (controller != null) {
                controller.loadAttempt(quiz.getQuizId(), data);
            }

        } catch (Exception e) {
            e.printStackTrace();
            showAlert("Error", "Network error starting quiz.");
        }
    }

    private String extractMessage(String responseBody) {
        try {
            com.google.gson.JsonObject obj = com.google.gson.JsonParser.parseString(responseBody).getAsJsonObject();
            return obj.has("message") ? obj.get("message").getAsString() : null;
        } catch (Exception e) {
            return null;
        }
    }
}
