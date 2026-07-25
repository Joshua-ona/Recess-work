package com.edudiscuss.controllers;

import com.edudiscuss.api.QuizApi;
import com.edudiscuss.models.QuizResultResponse;
import com.edudiscuss.utils.Navigator;
import com.google.gson.Gson;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.Label;

public class QuizResultController {

    @FXML private Label scoreLabel;
    @FXML private Label quizTitleLabel;
    @FXML private Label autoSubmittedBadge;

    private static final Gson gson = new Gson();

    public void loadResults(int quizId) {
        try {
            var response = QuizApi.results(quizId);
            if (!response.isOk()) {
                scoreLabel.setText("—");
                quizTitleLabel.setText("Couldn't load your result.");
                return;
            }

            QuizResultResponse result = gson.fromJson(response.body, QuizResultResponse.class);

            scoreLabel.setText(result.getScore() + " / " + result.getTotal());
            quizTitleLabel.setText(result.getQuiz().getTitle());

            if (result.isAutoSubmitted()) {
                autoSubmittedBadge.setVisible(true);
                autoSubmittedBadge.setManaged(true);
            }
        } catch (Exception e) {
            e.printStackTrace();
            scoreLabel.setText("—");
            quizTitleLabel.setText("Network error loading result.");
        }
    }

    @FXML
    public void backToQuizzes(javafx.event.ActionEvent event) {
        Button source = (Button) event.getSource();
        Navigator.goTo(source, "/views/student/quiz-list.fxml");
    }
}
