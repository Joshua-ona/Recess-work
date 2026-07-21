package com.edudiscuss.api;

import com.edudiscuss.utils.Session;

import java.io.IOException;
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;

public class ApiClient {

    private static final String BASE_URL =
        "http://127.0.0.1:8000/api/";

    private static final HttpClient client =
        HttpClient.newHttpClient();

    public static String post(String endpoint,
                              String json)
        throws IOException, InterruptedException {

        HttpRequest request =
            HttpRequest.newBuilder()
                .uri(URI.create(BASE_URL + endpoint))
                .header("Accept", "application/json")
                .header("Content-Type", "application/json")
                .POST(
                    HttpRequest.BodyPublishers
                        .ofString(json)
                )
                .build();

        HttpResponse<String> response =
            client.send(
                request,
                HttpResponse.BodyHandlers.ofString()
            );

        return response.body();
    }

    /**
     * Authenticated GET — attaches the Bearer token from Session.
     * Used for every quiz endpoint once the student is logged in.
     */
    public static ApiResponse authGet(String endpoint)
        throws IOException, InterruptedException {

        HttpRequest request =
            HttpRequest.newBuilder()
                .uri(URI.create(BASE_URL + endpoint))
                .header("Accept", "application/json")
                .header("Authorization", "Bearer " + Session.getToken())
                .GET()
                .build();

        HttpResponse<String> response =
            client.send(request, HttpResponse.BodyHandlers.ofString());

        return new ApiResponse(response.statusCode(), response.body());
    }

    /**
     * Authenticated POST — attaches the Bearer token from Session.
     */
    public static ApiResponse authPost(String endpoint, String json)
        throws IOException, InterruptedException {

        HttpRequest request =
            HttpRequest.newBuilder()
                .uri(URI.create(BASE_URL + endpoint))
                .header("Accept", "application/json")
                .header("Content-Type", "application/json")
                .header("Authorization", "Bearer " + Session.getToken())
                .POST(HttpRequest.BodyPublishers.ofString(json))
                .build();

        HttpResponse<String> response =
            client.send(request, HttpResponse.BodyHandlers.ofString());

        return new ApiResponse(response.statusCode(), response.body());
    }

    /**
     * Authenticated POST with Laravel's _method override — used for PUT/DELETE
     * since we're sending JSON bodies, not HTML forms.
     */
    public static ApiResponse authPostAsMethod(String endpoint, String json, String method)
        throws IOException, InterruptedException {

        // Laravel spoofs PUT/DELETE via a "_method" field in the payload
        // itself (not a header) — merge it into the JSON body.
        String spoofedJson;
        String trimmed = json.trim();
        if (trimmed.equals("{}")) {
            spoofedJson = "{\"_method\":\"" + method + "\"}";
        } else {
            spoofedJson = trimmed.substring(0, trimmed.length() - 1)
                + ",\"_method\":\"" + method + "\"}";
        }

        HttpRequest request =
            HttpRequest.newBuilder()
                .uri(URI.create(BASE_URL + endpoint))
                .header("Accept", "application/json")
                .header("Content-Type", "application/json")
                .header("Authorization", "Bearer " + Session.getToken())
                .POST(HttpRequest.BodyPublishers.ofString(spoofedJson))
                .build();

        HttpResponse<String> response =
            client.send(request, HttpResponse.BodyHandlers.ofString());

        return new ApiResponse(response.statusCode(), response.body());
    }

    /**
     * Authenticated multipart file upload — used for the CSV question upload.
     */
    public static ApiResponse authPostMultipart(String endpoint, String fieldName, java.io.File file)
        throws IOException, InterruptedException {

        String boundary = "----EDiscussBoundary" + System.currentTimeMillis();
        byte[] fileBytes = java.nio.file.Files.readAllBytes(file.toPath());

        String header = "--" + boundary + "\r\n"
            + "Content-Disposition: form-data; name=\"" + fieldName + "\"; filename=\"" + file.getName() + "\"\r\n"
            + "Content-Type: text/csv\r\n\r\n";
        String footer = "\r\n--" + boundary + "--\r\n";

        var bodyStream = new java.io.ByteArrayOutputStream();
        bodyStream.write(header.getBytes());
        bodyStream.write(fileBytes);
        bodyStream.write(footer.getBytes());

        HttpRequest request =
            HttpRequest.newBuilder()
                .uri(URI.create(BASE_URL + endpoint))
                .header("Accept", "application/json")
                .header("Content-Type", "multipart/form-data; boundary=" + boundary)
                .header("Authorization", "Bearer " + Session.getToken())
                .POST(HttpRequest.BodyPublishers.ofByteArray(bodyStream.toByteArray()))
                .build();

        HttpResponse<String> response =
            client.send(request, HttpResponse.BodyHandlers.ofString());

        return new ApiResponse(response.statusCode(), response.body());
    }

    /**
     * Wraps a raw HTTP response so callers can branch on status code
     * (e.g. 403 "not open yet", 409 "already submitted", 410 "time's up")
     * instead of guessing from JSON shape alone.
     */
    public static class ApiResponse {
        public final int statusCode;
        public final String body;

        public ApiResponse(int statusCode, String body) {
            this.statusCode = statusCode;
            this.body = body;
        }

        public boolean isOk() {
            return statusCode >= 200 && statusCode < 300;
        }
    }
}
