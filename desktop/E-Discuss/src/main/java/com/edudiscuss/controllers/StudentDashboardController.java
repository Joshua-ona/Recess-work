package com.edudiscuss.controllers;

import com.edudiscuss.models.StudentDashboard;
import com.edudiscuss.models.User;
import com.edudiscuss.services.ApiService;
import com.edudiscuss.utils.Session;
import com.edudiscuss.utils.QuizLockService;
import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import com.edudiscuss.models.Quiz;

public class QuizzesController {
    @FXML private TableView<Quiz> quizTable;
    @FXML private TableColumn<Quiz, String> titleColumn;
    @FXML private TableColumn<Quiz, String> startTimeColumn;
    @FXML private TableColumn<Quiz, String> durationColumn;
    @FXML private Button startQuizBtn;
    @FXML private Label availableQuizzesLabel;

    private ObservableList<Quiz> quizList = FXCollections.observableArrayList();
    private ApiService apiService = new ApiService();

    @FXML
    public void initialize() {
        setupTableColumns();
        loadQuizzes();
        setupTableSelection();
    }

    private void setupTableColumns() {
        titleColumn.setCellValueFactory(cellData -> cellData.getValue().titleProperty());
        startTimeColumn.setCellValueFactory(cellData -> cellData.getValue().formattedStartTimeProperty());
        durationColumn.setCellValueFactory(cellData -> cellData.getValue().durationProperty());
    }

    private void loadQuizzes() {
        // Fetch from Laravel API
        apiService.getQuizzes(response -> {
            if (response.isSuccess()) {
                quizList.setAll(response.getQuizzes());
                quizTable.setItems(quizList);
                availableQuizzesLabel.setText(quizList.size() + " quizzes published");
            } else {
                showAlert("Error", "Failed to load quizzes");
            }
        });
    }

    private void setupTableSelection() {
        quizTable.getSelectionModel().selectedItemProperty().addListener(
            (obs, oldSelection, newSelection) -> {
                startQuizBtn.setDisable(newSelection == null);
            }
        );
    }

    @FXML
    private void startQuiz() {
        Quiz selected = quizTable.getSelectionModel().getSelectedItem();
        if (selected != null) {
            // Navigate to quiz taking view
            loadQuizAttempt(selected);
        }
    }
}

