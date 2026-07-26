package com.edudiscuss.api;

import com.google.gson.Gson;
import com.google.gson.JsonObject;

import java.io.IOException;

/**
 * Lecturer quiz management calls, mirroring routes/api.php's /lecturer/quizzes/* group.
 */
public class LecturerQuizApi {

    private static final Gson gson = new Gson();

    public static ApiClient.ApiResponse list() throws IOException, InterruptedException {
        return ApiClient.authGet("lecturer/quizzes");
    }

    public static ApiClient.ApiResponse show(int quizId) throws IOException, InterruptedException {
        return ApiClient.authGet("lecturer/quizzes/" + quizId);
    }

    public static ApiClient.ApiResponse create(String title, int groupId, String targetCategory,
                                                String startTime, int durationMins)
            throws IOException, InterruptedException {
        JsonObject body = new JsonObject();
        body.addProperty("title", title);
        body.addProperty("group_id", groupId);
        body.addProperty("target_category", targetCategory);
        body.addProperty("start_time", startTime);
        body.addProperty("duration_mins", durationMins);
        return ApiClient.authPost("lecturer/quizzes", gson.toJson(body));
    }

    public static ApiClient.ApiResponse update(int quizId, String title, String targetCategory,
                                                String startTime, int durationMins)
            throws IOException, InterruptedException {
        JsonObject body = new JsonObject();
        body.addProperty("title", title);
        body.addProperty("target_category", targetCategory);
        body.addProperty("start_time", startTime);
        body.addProperty("duration_mins", durationMins);
        return ApiClient.authPost("lecturer/quizzes/" + quizId + "/update", gson.toJson(body));
    }

    public static ApiClient.ApiResponse publish(int quizId) throws IOException, InterruptedException {
        return ApiClient.authPost("lecturer/quizzes/" + quizId + "/publish", "{}");
    }

    public static ApiClient.ApiResponse delete(int quizId) throws IOException, InterruptedException {
        return ApiClient.authPost("lecturer/quizzes/" + quizId + "/delete", "{}");
    }

    public static ApiClient.ApiResponse uploadCsv(int quizId, java.io.File csvFile)
            throws IOException, InterruptedException {
        return ApiClient.authPostMultipart("lecturer/quizzes/" + quizId + "/upload", "csv_file", csvFile);
    }
}
