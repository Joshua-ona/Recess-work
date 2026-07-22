package com.edudiscuss.utils;

import com.edudiscuss.api.QuizApi;
import com.edudiscuss.controllers.QuizAttemptController;
import com.edudiscuss.models.ActiveQuizResponse;
import com.google.gson.Gson;
import javafx.scene.Node;

/**
 * Requirement #3 ("lock student into the active quiz") for a desktop app
 * with no server-side session/middleware to enforce it automatically.
 * Every navigation attempt must go through here first.
 */
public class QuizLockService {

    private static final Gson gson = new Gson();

    /**
     * Checks the server for an active quiz. If one exists, forces
     * navigation to the attempt screen and returns true (caller should
     * abort whatever navigation it was about to do). Returns false if the
     * student is free to navigate normally.
     */
    public static boolean enforceLock(Node fromNode) {
        try {
            var response = QuizApi.active();
            if (!response.isOk()) {
                // Network hiccup or auth issue — fail open rather than
                // trapping the student on a broken screen. The server
                // still enforces everything on the write endpoints.
                return false;
            }

            ActiveQuizResponse active = gson.fromJson(response.body, ActiveQuizResponse.class);

            if (active.isActive()) {
                QuizAttemptController controller = Navigator.goToWithController(
                        fromNode, "/views/student/quiz-attempt.fxml");
                if (controller != null) {
                    // Now passing the double value to resumeLockedQuiz
                    controller.resumeLockedQuiz(active.getQuizId(), active.getRemainingSeconds());
                }
                return true;
            }

            return false;
        } catch (Exception e) {
            e.printStackTrace();
            return false;
        }
    }
}