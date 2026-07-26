package com.edudiscuss.api;

import com.edudiscuss.models.User;
import com.edudiscuss.utils.Session;

import com.google.gson.*;
import com.google.gson.reflect.TypeToken;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.IOException;
import java.lang.reflect.Type;
import java.net.URI;
import java.net.URLEncoder;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.time.Duration;
import java.util.List;
import java.util.Map;

public class ApiClient {

    public static String BASE_URL = "http://127.0.0.1:8000/api/";

    private static final HttpClient HTTP =
            HttpClient.newBuilder()
                    .connectTimeout(Duration.ofSeconds(10))
                    .build();

    private static final Gson GSON =
            new GsonBuilder().create();

    /* ==========================================================
       TOKEN HELPERS
       ========================================================== */

    private static String token() {
        try {
            if (Session.getToken() != null &&
                    !Session.getToken().isBlank()) {
                return Session.getToken();
            }
        } catch (Exception ignored) {
        }

        try {
            return Session.getInstance().getToken();
        } catch (Exception ignored) {
        }

        return null;
    }

    /* ==========================================================
       SIMPLE GET/POST (USED BY DISCUSSIONS/GROUPS)
       ========================================================== */

    public static String get(String endpoint)
            throws IOException, InterruptedException {

        HttpRequest.Builder builder =
                HttpRequest.newBuilder()
                        .uri(URI.create(BASE_URL + endpoint))
                        .header("Accept", "application/json")
                        .GET();

        if (token() != null) {
            builder.header(
                    "Authorization",
                    "Bearer " + token()
            );
        }

        HttpResponse<String> response =
                HTTP.send(
                        builder.build(),
                        HttpResponse.BodyHandlers.ofString()
                );

        return response.body();
    }

    public static String post(String endpoint,
                              String json)
            throws IOException, InterruptedException {

        HttpRequest.Builder builder =
                HttpRequest.newBuilder()
                        .uri(URI.create(BASE_URL + endpoint))
                        .header("Accept", "application/json")
                        .header("Content-Type", "application/json")
                        .POST(
                                HttpRequest.BodyPublishers
                                        .ofString(json)
                        );

        if (token() != null) {
            builder.header(
                    "Authorization",
                    "Bearer " + token()
            );
        }

        HttpResponse<String> response =
                HTTP.send(
                        builder.build(),
                        HttpResponse.BodyHandlers.ofString()
                );

        return response.body();
    }

    /* ==========================================================
       AUTH METHODS (QUIZZES, DISCUSSIONS, ETC.)
       ========================================================== */

    public static ApiResponse authGet(String endpoint)
            throws IOException, InterruptedException {

        HttpRequest request =
                HttpRequest.newBuilder()
                        .uri(URI.create(BASE_URL + endpoint))
                        .header("Accept", "application/json")
                        .header("Authorization",
                                "Bearer " + token())
                        .GET()
                        .build();

        HttpResponse<String> response =
                HTTP.send(
                        request,
                        HttpResponse.BodyHandlers.ofString()
                );

        return new ApiResponse(
                response.statusCode(),
                response.body()
        );
    }

    public static ApiResponse authPost(
            String endpoint,
            String json)
            throws IOException, InterruptedException {

        HttpRequest request =
                HttpRequest.newBuilder()
                        .uri(URI.create(BASE_URL + endpoint))
                        .header("Accept", "application/json")
                        .header("Content-Type",
                                "application/json")
                        .header("Authorization",
                                "Bearer " + token())
                        .POST(
                                HttpRequest.BodyPublishers
                                        .ofString(json)
                        )
                        .build();

        HttpResponse<String> response =
                HTTP.send(
                        request,
                        HttpResponse.BodyHandlers.ofString()
                );

        return new ApiResponse(
                response.statusCode(),
                response.body()
        );
    }

    public static ApiResponse authPostAsMethod(
            String endpoint,
            String json,
            String method)
            throws IOException, InterruptedException {

        String spoofedJson;
        String trimmed = json.trim();

        if (trimmed.equals("{}")) {
            spoofedJson =
                    "{\"_method\":\"" + method + "\"}";
        } else {
            spoofedJson =
                    trimmed.substring(
                            0,
                            trimmed.length() - 1
                    ) +
                            ",\"_method\":\"" +
                            method +
                            "\"}";
        }

        return authPost(endpoint, spoofedJson);
    }

    public static ApiResponse authPostMultipart(
            String endpoint,
            String fieldName,
            File file)
            throws IOException, InterruptedException {

        String boundary =
                "----EDiscussBoundary"
                        + System.currentTimeMillis();

        byte[] fileBytes =
                java.nio.file.Files
                        .readAllBytes(file.toPath());

        String header =
                "--" + boundary + "\r\n"
                        + "Content-Disposition: form-data; "
                        + "name=\"" + fieldName + "\"; "
                        + "filename=\"" + file.getName()
                        + "\"\r\n"
                        + "Content-Type: text/csv\r\n\r\n";

        String footer =
                "\r\n--" + boundary + "--\r\n";

        ByteArrayOutputStream body =
                new ByteArrayOutputStream();

        body.write(header.getBytes());
        body.write(fileBytes);
        body.write(footer.getBytes());

        HttpRequest request =
                HttpRequest.newBuilder()
                        .uri(
                                URI.create(
                                        BASE_URL + endpoint
                                )
                        )
                        .header(
                                "Accept",
                                "application/json"
                        )
                        .header(
                                "Content-Type",
                                "multipart/form-data; boundary="
                                        + boundary
                        )
                        .header(
                                "Authorization",
                                "Bearer " + token()
                        )
                        .POST(
                                HttpRequest.BodyPublishers
                                        .ofByteArray(
                                                body.toByteArray()
                                        )
                        )
                        .build();

        HttpResponse<String> response =
                HTTP.send(
                        request,
                        HttpResponse.BodyHandlers.ofString()
                );

        return new ApiResponse(
                response.statusCode(),
                response.body()
        );
    }

    /* ==========================================================
       LOGIN
       ========================================================== */

    public static ApiResult<LoginResponse> login(
            String email,
            String password) {

        Map<String, String> body =
                Map.of(
                        "email", email,
                        "password", password
                );

        return post(
                "/login",
                body,
                LoginResponse.class,
                null
        );
    }

    public static ApiResult<Void> logout() {
        return post(
                "/logout",
                Map.of(),
                Void.class,
                token()
        );
    }

    /* ==========================================================
       ADMIN
       ========================================================== */

    public static ApiResult<DashboardStats>
    adminDashboard() {

        return get(
                "admin/dashboard",
                DashboardStats.class
        );
    }

    public static ApiResult<List<User>>
    adminUsers(String search) {

        String path =
                "admin/users"
                        + (search != null
                        && !search.isBlank()
                        ? "?search="
                        + encode(search)
                        : "");

        Type listType =
                new TypeToken<
                        PagedResponse<User>>() {
                }.getType();

        HttpResponse<String> resp =
                doGet(path);

        if (resp == null) {
            return ApiResult.error(
                    "Connection failed."
            );
        }

        if (resp.statusCode() != 200) {
            return ApiResult.error(
                    extractMessage(resp.body())
            );
        }

        PagedResponse<User> paged =
                GSON.fromJson(
                        resp.body(),
                        listType
                );

        return ApiResult.success(
                paged != null
                        && paged.data != null
                        ? paged.data
                        : List.of()
        );
    }

    public static ApiResult<String>
    blacklistUser(int id) {
        return postMessage(
                "admin/users/" + id
                        + "/blacklist",
                Map.of()
        );
    }

    public static ApiResult<String>
    unblacklistUser(int id) {
        return postMessage(
                "admin/users/" + id
                        + "/unblacklist",
                Map.of()
        );
    }

    public static ApiResult<String>
    warnUser(
            int id,
            String message) {

        return postMessage(
                "admin/users/" + id
                        + "/warn",
                Map.of(
                        "message",
                        message
                )
        );
    }

    public static ApiResult<String>
    logoutUser(int id) {

        return postMessage(
                "admin/users/" + id
                        + "/logout",
                Map.of()
        );
    }

    public static ApiResult<String>
    inviteLecturer(
            String first,
            String last,
            String email) {

        return postMessage(
                "admin/lecturers",
                Map.of(
                        "first_name",
                        first,
                        "last_name",
                        last,
                        "email",
                        email
                )
        );
    }

    /* ==========================================================
       INTERNAL HELPERS
       ========================================================== */

    private static <T>
    ApiResult<T> get(
            String path,
            Class<T> type) {

        HttpResponse<String> resp =
                doGet(path);

        if (resp == null) {
            return ApiResult.error(
                    "Connection failed."
            );
        }

        if (resp.statusCode() != 200) {
            return ApiResult.error(
                    extractMessage(resp.body())
            );
        }

        return ApiResult.success(
                GSON.fromJson(
                        resp.body(),
                        type
                )
        );
    }

    private static <T>
    ApiResult<T> post(
            String path,
            Map<String, String> body,
            Class<T> type,
            String token) {

        HttpResponse<String> resp =
                doPost(path, body, token);

        if (resp == null) {
            return ApiResult.error(
                    "Connection failed."
            );
        }

        if (resp.statusCode() >= 400) {
            return ApiResult.error(
                    extractMessage(resp.body())
            );
        }

        if (type == Void.class) {
            return ApiResult.success(null);
        }

        return ApiResult.success(
                GSON.fromJson(
                        resp.body(),
                        type
                )
        );
    }

    private static ApiResult<String>
    postMessage(
            String path,
            Map<String, String> body) {

        HttpResponse<String> resp =
                doPost(path, body, token());

        if (resp == null) {
            return ApiResult.error(
                    "Connection failed."
            );
        }

        if (resp.statusCode() >= 400) {
            return ApiResult.error(
                    extractMessage(resp.body())
            );
        }

        return ApiResult.success(
                extractMessage(resp.body())
        );
    }

    private static HttpResponse<String>
    doGet(String path) {

        try {

            HttpRequest.Builder req =
                    HttpRequest.newBuilder()
                            .uri(
                                    URI.create(
                                            BASE_URL + path
                                    )
                            )
                            .header(
                                    "Accept",
                                    "application/json"
                            )
                            .timeout(
                                    Duration.ofSeconds(15)
                            )
                            .GET();

            if (token() != null) {
                req.header(
                        "Authorization",
                        "Bearer " + token()
                );
            }

            return HTTP.send(
                    req.build(),
                    HttpResponse.BodyHandlers.ofString()
            );

        } catch (Exception e) {
            return null;
        }
    }

    private static HttpResponse<String>
    doPost(
            String path,
            Map<String, String> body,
            String token) {

        try {

            String json =
                    GSON.toJson(body);

            HttpRequest.Builder req =
                    HttpRequest.newBuilder()
                            .uri(
                                    URI.create(
                                            BASE_URL + path
                                    )
                            )
                            .header(
                                    "Accept",
                                    "application/json"
                            )
                            .header(
                                    "Content-Type",
                                    "application/json"
                            )
                            .timeout(
                                    Duration.ofSeconds(15)
                            )
                            .POST(
                                    HttpRequest.BodyPublishers
                                            .ofString(json)
                            );

            if (token != null) {
                req.header(
                        "Authorization",
                        "Bearer " + token
                );
            }

            return HTTP.send(
                    req.build(),
                    HttpResponse.BodyHandlers.ofString()
            );

        } catch (Exception e) {
            return null;
        }
    }

    private static String extractMessage(
            String json) {

        try {
            JsonObject obj =
                    JsonParser.parseString(json)
                            .getAsJsonObject();

            if (obj.has("message")) {
                return obj.get("message")
                        .getAsString();
            }

        } catch (Exception ignored) {
        }

        return "An unexpected error occurred.";
    }

    private static String encode(String s) {
        try {
            return URLEncoder.encode(
                    s,
                    "UTF-8"
            );
        } catch (Exception e) {
            return s;
        }
    }

    /* ==========================================================
       RESPONSE CLASSES
       ========================================================== */

    public static class ApiResponse {

        public final int statusCode;
        public final String body;

        public ApiResponse(
                int statusCode,
                String body) {

            this.statusCode =
                    statusCode;

            this.body =
                    body;
        }

        public boolean isOk() {
            return statusCode >= 200
                    && statusCode < 300;
        }
    }

    public static class LoginResponse {
        public String token;
        public User user;
    }

    public static class DashboardStats {
        public int total_members;
        public int active_today;
        public int pending_count;
        public int blacklisted_count;
    }

    private static class PagedResponse<T> {
        List<T> data;
    }
}