package com.edudiscuss.controllers;

import com.edudiscuss.api.QuizApi;
import com.edudiscuss.models.Question;
import com.edudiscuss.models.QuizStartResponse;
import com.edudiscuss.utils.Navigator;
import com.google.gson.Gson;
import javafx.animation.KeyFrame;
import javafx.animation.Timeline;
import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.scene.layout.VBox;
import javafx.util.Duration;

import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.concurrent.atomic.AtomicInteger;

public class QuizAttemptController {

    @FXML private Label titleLabel;
    @FXML private Label timerLabel;
    @FXML private Label progressLabel;
    @FXML private Label errorLabel;
    @FXML private ProgressBar progressBar;
    @FXML private VBox questionsBox;
    @FXML private Button prevBtn;
    @FXML private Button nextBtn;

    private static final Gson gson = new Gson();
    private static final int PER_PAGE = 5;

    private int quizId;
    private List<Question> allQuestions;
    private final Map<String, String> answers = new LinkedHashMap<>(); // question_id (as string) -> "a"/"b"/"c"/"d"
    private final Map<String, ToggleGroup> toggleGroups = new LinkedHashMap<>();

    private int currentPage = 1;
    private int totalPages = 1;
    private int remainingSeconds;
    private Timeline timer;
    private boolean submitted = false;

    /** Called by QuizListController right after a fresh /start call. */
    public void loadAttempt(int quizId, QuizStartResponse data) {
        this.quizId = quizId;
        this.allQuestions = data.getQuestions();
        this.remainingSeconds = data.getRemainingSeconds();
        this.totalPages = (int) Math.ceil(allQuestions.size() / (double) PER_PAGE);

        if (data.getSavedAnswers() != null) {
            answers.putAll(data.getSavedAnswers());
        }

        titleLabel.setText(data.getQuiz().getTitle());
        startTimer();
        renderPage(1);
    }

    /**
     * Called by QuizLockService when the student was force-redirected here
     * because a quiz is already in progress (app restart, tried to
     * navigate away, etc). Re-fetches the question set via /start, which
     * is safe to call again — it just resumes the existing progress row.
     */
    public void resumeLockedQuiz(int quizId, int remainingSeconds) {
        try {
            var response = QuizApi.start(quizId);
            if (response.isOk()) {
                loadAttempt(quizId, gson.fromJson(response.body, QuizStartResponse.class));
            } else {
                showError("Couldn't resume your quiz. Please contact support.");
            }
        } catch (Exception e) {
            e.printStackTrace();
            showError("Network error resuming quiz.");
        }
    }

    private void startTimer() {
        renderTimer();
        timer = new Timeline(new KeyFrame(Duration.seconds(1), e -> {
            remainingSeconds--;
            renderTimer();

            if (remainingSeconds <= 0 && !submitted) {
                timer.stop();
                submitQuiz(true);
            }
        }));
        timer.setCycleCount(Timeline.INDEFINITE);
        timer.play();
    }

    private void renderTimer() {
        int s = Math.max(0, remainingSeconds);
        timerLabel.setText(String.format("%02d:%02d:%02d", s / 3600, (s % 3600) / 60, s % 60));
        timerLabel.getStyleClass().removeAll("timer-low");
        if (s <= 300) {
            timerLabel.getStyleClass().add("timer-low");
        }
    }

    private void renderPage(int page) {
        currentPage = page;
        questionsBox.getChildren().clear();
        toggleGroups.clear();

        int from = (page - 1) * PER_PAGE;
        int to = Math.min(from + PER_PAGE, allQuestions.size());

        for (int i = from; i < to; i++) {
            Question q = allQuestions.get(i);
            questionsBox.getChildren().add(buildQuestionCard(q, i + 1));
        }

        progressLabel.setText("Page " + page + " of " + totalPages);
        progressBar.setProgress(page / (double) Math.max(1, totalPages));

        prevBtn.setDisable(page <= 1);
        nextBtn.setText(page >= totalPages ? "Submit Quiz" : "Save & Next");
    }

    private VBox buildQuestionCard(Question q, int displayNumber) {
        VBox card = new VBox(8);
        card.getStyleClass().add("q-card");

        Label tag = new Label("Question " + displayNumber);
        tag.getStyleClass().add("q-tag");

        Label text = new Label(q.getQuestionText());
        text.getStyleClass().add("q-text");
        text.setWrapText(true);

        card.getChildren().addAll(tag, text);

        String qid = String.valueOf(q.getQuestionId());
        ToggleGroup group = new ToggleGroup();
        toggleGroups.put(qid, group);

        for (Map.Entry<String, String> option : q.getOptions().entrySet()) {
            RadioButton rb = new RadioButton(option.getValue());
            rb.setToggleGroup(group);
            rb.setUserData(option.getKey()); // "a"/"b"/"c"/"d"
            rb.getStyleClass().add("q-option");

            if (option.getKey().equalsIgnoreCase(answers.get(qid))) {
                rb.setSelected(true);
            }

            rb.setOnAction(e -> {
                answers.put(qid, (String) rb.getUserData());
                autosave();
            });

            card.getChildren().add(rb);
        }

        return card;
    }

    @FXML
    public void goPrevious() {
        if (currentPage > 1) {
            renderPage(currentPage - 1);
        }
    }

    @FXML
    public void goNext() {
        if (currentPage >= totalPages) {
            submitQuiz(false);
        } else {
            autosave();
            renderPage(currentPage + 1);
        }
    }

    /** Fire-and-forget autosave — same role as the web app's /answer POST on each page. */
    private void autosave() {
        try {
            var response = QuizApi.saveAnswers(quizId, answers);
            if (response.statusCode == 410 && !submitted) {
                // Deadline passed server-side between ticks — submit now
                // instead of waiting for the local timer to also notice.
                submitQuiz(true);
            }
        } catch (Exception e) {
            e.printStackTrace();
            // Non-fatal: the next autosave attempt, or the final submit's
            // own answers payload, will catch this up.
        }
    }

    private void submitQuiz(boolean autoSubmitted) {
        if (submitted) return;
        submitted = true;
        if (timer != null) timer.stop();

        try {
            var response = QuizApi.submit(quizId, answers, autoSubmitted);
            QuizResultController controller = Navigator.goToWithController(
                    questionsBox, "/views/student/quiz-result.fxml");
            if (controller != null) {
                controller.loadResults(quizId);
            }
        } catch (Exception e) {
            e.printStackTrace();
            submitted = false; // allow retry
            showError("Couldn't submit — check your connection and try again.");
            if (timer != null) timer.play();
        }
    }

    private void showError(String message) {
        errorLabel.setText(message);
        errorLabel.setVisible(true);
        errorLabel.setManaged(true);
    }
}
