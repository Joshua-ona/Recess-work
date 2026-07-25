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

import java.util.List;

import com.edudiscuss.models.Quiz;

public class StudentQuizzesController {
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

    private void loadQuizzes(){

    try {

        List<Quiz> quizzes =
                apiService.getQuizzes();


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
    private void showAlert(String title, String message) {
    Alert alert = new Alert(Alert.AlertType.ERROR);
    alert.setTitle(title);
    alert.setHeaderText(null);
    alert.setContentText(message);
    alert.showAndWait();
}

private void loadQuizAttempt(Quiz quiz) {
    System.out.println("Starting quiz: " + quiz.getTitle());

    // later:
    // Navigator.goTo(startQuizBtn,
    //      "/views/student/quiz-attempt.fxml");
}
}

