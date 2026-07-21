package com.edudiscuss.controllers;

import com.edudiscuss.api.LecturerQuizApi;
   
import com.edudiscuss.models.Group;
import com.edudiscuss.models.GroupListResponse;
import com.edudiscuss.models.QuizDetailResponse;
import com.edudiscuss.utils.Navigator;
import com.google.gson.Gson;
import javafx.collections.FXCollections;
import javafx.fxml.FXML;
import javafx.scene.control.*;

import java.time.LocalDate;
import java.time.LocalTime;
import java.time.format.DateTimeFormatter;

public class LecturerQuizCreateController {

    @FXML private Label errorLabel;
    @FXML private TextField titleField;
    @FXML private ComboBox<Group> groupCombo;
    @FXML private ComboBox<String> categoryCombo;
    @FXML private DatePicker startDatePicker;
    @FXML private Spinner<Integer> startHourSpinner;
    @FXML private Spinner<Integer> startMinuteSpinner;
    @FXML private Spinner<Integer> durationSpinner;

    private static final Gson gson = new Gson();

    @FXML
    public void initialize() {
        startHourSpinner.setValueFactory(new SpinnerValueFactory.IntegerSpinnerValueFactory(0, 23, 9));
        startMinuteSpinner.setValueFactory(new SpinnerValueFactory.IntegerSpinnerValueFactory(0, 59, 0, 5));
        durationSpinner.setValueFactory(new SpinnerValueFactory.IntegerSpinnerValueFactory(1, 300, 30));
        startDatePicker.setValue(LocalDate.now());

        categoryCombo.setItems(FXCollections.observableArrayList(
                "Level 100", "Level 200", "Level 300", "Level 400"));

        loadGroups();
    }

    private void loadGroups() {
        try {
            // Group listing is a shared endpoint, not lecturer-specific.
            var response = com.edudiscuss.api.ApiClient.authGet("groups");
            if (response.isOk()) {
                GroupListResponse parsed = gson.fromJson(response.body, GroupListResponse.class);
                groupCombo.setItems(FXCollections.observableArrayList(
                        parsed.getGroups() != null ? parsed.getGroups() : java.util.List.of()));
            }
        } catch (Exception e) {
            e.printStackTrace();
            showError("Couldn't load groups.");
        }
    }

    @FXML
    public void save() {
        String title = titleField.getText();
        Group group = groupCombo.getValue();
        String category = categoryCombo.getEditor().getText() != null && !categoryCombo.getEditor().getText().isBlank()
                ? categoryCombo.getEditor().getText() : categoryCombo.getValue();
        LocalDate date = startDatePicker.getValue();

        if (title == null || title.isBlank() || group == null || category == null || date == null) {
            showError("Please fill in all fields.");
            return;
        }

        LocalTime time = LocalTime.of(startHourSpinner.getValue(), startMinuteSpinner.getValue());
        String startTime = date.atTime(time).format(DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss"));

        try {
            var response = LecturerQuizApi.create(title, group.getId(), category, startTime, durationSpinner.getValue());

            if (response.isOk()) {
                QuizDetailResponse parsed = gson.fromJson(response.body, QuizDetailResponse.class);
                LecturerQuizEditController controller = Navigator.goToWithController(
                        titleField, "/views/lecturer/quiz-edit.fxml");
                if (controller != null) {
                    controller.loadQuiz(parsed.getQuiz().getQuizId());
                }
            } else {
                showError("Couldn't create the quiz. Check all fields are filled in correctly.");
            }
        } catch (Exception e) {
            e.printStackTrace();
            showError("Network error creating quiz.");
        }
    }

    private void showError(String message) {
        errorLabel.setText(message);
        errorLabel.setVisible(true);
        errorLabel.setManaged(true);
    }
}
