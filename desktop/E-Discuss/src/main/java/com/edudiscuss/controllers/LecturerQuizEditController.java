package com.edudiscuss.controllers;

import com.edudiscuss.api.LecturerQuizApi;
import com.edudiscuss.models.Quiz;
import com.edudiscuss.models.QuizDetailResponse;
import com.google.gson.Gson;
import javafx.collections.FXCollections;
import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.stage.FileChooser;

import java.io.File;
import java.time.Instant;
import java.time.LocalDateTime;
import java.time.ZoneId;
import java.time.format.DateTimeFormatter;

public class LecturerQuizEditController {

    @FXML private Label titleLabel;
    @FXML private Label statusLabel;
    @FXML private Label messageLabel;
    @FXML private TextField titleField;
    @FXML private ComboBox<String> categoryCombo;
    @FXML private DatePicker startDatePicker;
    @FXML private Spinner<Integer> startHourSpinner;
    @FXML private Spinner<Integer> startMinuteSpinner;
    @FXML private Spinner<Integer> durationSpinner;
    @FXML private Label questionCountLabel;
    @FXML private Label csvFileLabel;
    @FXML private Button uploadBtn;
    @FXML private Button publishBtn;

    private static final Gson gson = new Gson();
    private int quizId;
    private File selectedCsv;

    @FXML
    public void initialize() {
        startHourSpinner.setValueFactory(new SpinnerValueFactory.IntegerSpinnerValueFactory(0, 23, 9));
        startMinuteSpinner.setValueFactory(new SpinnerValueFactory.IntegerSpinnerValueFactory(0, 59, 0, 5));
        durationSpinner.setValueFactory(new SpinnerValueFactory.IntegerSpinnerValueFactory(1, 300, 30));
        categoryCombo.setItems(FXCollections.observableArrayList(
                "Level 100", "Level 200", "Level 300", "Level 400"));
    }

    public void loadQuiz(int quizId) {
        this.quizId = quizId;
        try {
            var response = LecturerQuizApi.show(quizId);
            if (!response.isOk()) {
                showMessage("Couldn't load this quiz.");
                return;
            }
            QuizDetailResponse parsed = gson.fromJson(response.body, QuizDetailResponse.class);
            populate(parsed.getQuiz());
        } catch (Exception e) {
            e.printStackTrace();
            showMessage("Network error loading quiz.");
        }
    }

    private void populate(Quiz quiz) {
        titleLabel.setText(quiz.getTitle());
        statusLabel.setText(quiz.isPublished() ? "Published" : "Draft — not visible to students yet");
        titleField.setText(quiz.getTitle());
        categoryCombo.getEditor().setText(quiz.getTargetCategory());
        durationSpinner.getValueFactory().setValue(quiz.getDurationMins());
        publishBtn.setDisable(quiz.isPublished());

        try {
            LocalDateTime start = Instant.parse(quiz.getStartTime())
                    .atZone(ZoneId.of("Africa/Kampala")).toLocalDateTime();
            startDatePicker.setValue(start.toLocalDate());
            startHourSpinner.getValueFactory().setValue(start.getHour());
            startMinuteSpinner.getValueFactory().setValue(start.getMinute());
        } catch (Exception e) {
            e.printStackTrace();
        }

        int questionCount = quiz.getQuestions() != null ? quiz.getQuestions().size() : 0;
        questionCountLabel.setText(questionCount + " question" + (questionCount == 1 ? "" : "s") + " uploaded so far");
    }

    @FXML
    public void saveChanges() {
        String title = titleField.getText();
        String category = categoryCombo.getEditor().getText();
        var date = startDatePicker.getValue();

        if (title == null || title.isBlank() || category == null || category.isBlank() || date == null) {
            showMessage("Please fill in all fields.");
            return;
        }

        var time = java.time.LocalTime.of(startHourSpinner.getValue(), startMinuteSpinner.getValue());
        String startTime = date.atTime(time).format(DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss"));

        try {
            // Note: the backend's update() always sets is_published=1, matching
            // the web app's current (if odd) behavior — saving changes here
            // will publish the quiz even if you don't click "Publish quiz"
            // separately. Flagged to Esther's team; preserved as-is for now.
            var response = LecturerQuizApi.update(quizId, title, category, startTime, durationSpinner.getValue());
            if (response.isOk()) {
                loadQuiz(quizId);
                showMessage("Saved.");
            } else {
                showMessage("Couldn't save changes.");
            }
        } catch (Exception e) {
            e.printStackTrace();
            showMessage("Network error saving changes.");
        }
    }

    @FXML
    public void chooseCsv() {
        FileChooser chooser = new FileChooser();
        chooser.getExtensionFilters().add(new FileChooser.ExtensionFilter("CSV files", "*.csv", "*.txt"));
        File file = chooser.showOpenDialog(titleField.getScene().getWindow());
        if (file != null) {
            selectedCsv = file;
            csvFileLabel.setText(file.getName());
            uploadBtn.setDisable(false);
        }
    }

    @FXML
    public void uploadCsv() {
        if (selectedCsv == null) return;
        try {
            var response = LecturerQuizApi.uploadCsv(quizId, selectedCsv);
            if (response.isOk()) {
                showMessage("Questions uploaded.");
                loadQuiz(quizId);
                selectedCsv = null;
                csvFileLabel.setText("");
                uploadBtn.setDisable(true);
            } else {
                showMessage("Couldn't upload — check the CSV format (question, option_a-d, correct_answer).");
            }
        } catch (Exception e) {
            e.printStackTrace();
            showMessage("Network error uploading CSV.");
        }
    }

    @FXML
    public void publish() {
        try {
            var response = LecturerQuizApi.publish(quizId);
            if (response.isOk()) {
                loadQuiz(quizId);
                showMessage("Quiz published — visible to students now.");
            } else {
                showMessage("Couldn't publish.");
            }
        } catch (Exception e) {
            e.printStackTrace();
            showMessage("Network error publishing.");
        }
    }

    private void showMessage(String message) {
        messageLabel.setText(message);
        messageLabel.setVisible(true);
        messageLabel.setManaged(true);
    }
}