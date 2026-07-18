package com.edudiscuss.api;

import com.google.gson.Gson;
import com.google.gson.JsonObject;

import java.io.IOException;
import java.util.Map;

/**
 * All quiz-related network calls, mirroring routes/api.php:
 *   GET  /quizzes                 -> available()
 *   GET  /quizzes/active           -> active()
 *   POST /quizzes/{id}/start       -> start(id)
 *   POST /quizzes/{id}/save-answers-> saveAnswers(id, answers)
 *   POST /quizzes/{id}/submit      -> submit(id, answers, autoSubmitted)
 *   GET  /quizzes/{id}/results     -> results(id)
 */
public class QuizApi {

    private static final Gson gson = new Gson();

    public static ApiClient.ApiResponse available() throws IOException, InterruptedException {
        return ApiClient.authGet("quizzes");
    }

    public static ApiClient.ApiResponse active() throws IOException, InterruptedException {
        return ApiClient.authGet("quizzes/active");
    }

    public static ApiClient.ApiResponse start(int quizId) throws IOException, InterruptedException {
        return ApiClient.authPost("quizzes/" + quizId + "/start", "{}");
    }

    public static ApiClient.ApiResponse saveAnswers(int quizId, Map<String, String> answers)
            throws IOException, InterruptedException {
        JsonObject body = new JsonObject();
        body.add("answers", gson.toJsonTree(answers));
        return ApiClient.authPost("quizzes/" + quizId + "/save-answers", gson.toJson(body));
    }

    public static ApiClient.ApiResponse submit(int quizId, Map<String, String> answers, boolean autoSubmitted)
            throws IOException, InterruptedException {
        JsonObject body = new JsonObject();
        body.add("answers", gson.toJsonTree(answers));
        body.addProperty("auto_submitted", autoSubmitted);
        return ApiClient.authPost("quizzes/" + quizId + "/submit", gson.toJson(body));
    }

    public static ApiClient.ApiResponse results(int quizId) throws IOException, InterruptedException {
        return ApiClient.authGet("quizzes/" + quizId + "/results");
    }
}
