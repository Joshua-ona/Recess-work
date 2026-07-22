package com.edudiscuss.api;

import com.edudiscuss.utils.Session;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.IOException;
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.nio.file.Files;

public class ApiClient {

    private static final String BASE_URL = "http://127.0.0.1:8000/api/";
    private static final HttpClient client = HttpClient.newHttpClient();

    /**
     * Public GET (no authentication)
     */
    public static String get(String endpoint)
            throws IOException, InterruptedException {

        HttpRequest request = HttpRequest.newBuilder()
                .uri(URI.create(BASE_URL + endpoint))
                .header("Accept", "application/json")
                .GET()
                .build();

        HttpResponse<String> response =
                client.send(request, HttpResponse.BodyHandlers.ofString());

        return response.body();
    }

    /**
     * Public POST (used for login)
     */
    public static String post(String endpoint, String json)
            throws IOException, InterruptedException {

        HttpRequest request = HttpRequest.newBuilder()
                .uri(URI.create(BASE_URL + endpoint))
                .header("Accept", "application/json")
                .header("Content-Type", "application/json")
                .POST(HttpRequest.BodyPublishers.ofString(json))
                .build();

        HttpResponse<String> response =
                client.send(request, HttpResponse.BodyHandlers.ofString());

        return response.body();
    }

    /**
     * Authenticated GET
     */
    public static ApiResponse authGet(String endpoint)
            throws IOException, InterruptedException {

        HttpRequest request = HttpRequest.newBuilder()
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
     * Authenticated POST
     */
    public static ApiResponse authPost(String endpoint, String json)
            throws IOException, InterruptedException {

        HttpRequest request = HttpRequest.newBuilder()
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
     * Authenticated POST with Laravel method spoofing
     */
    public static ApiResponse authPostAsMethod(String endpoint,
                                               String json,
                                               String method)
            throws IOException, InterruptedException {

        String spoofedJson;
        String trimmed = json.trim();

        if (trimmed.equals("{}")) {
            spoofedJson = "{\"_method\":\"" + method + "\"}";
        } else {
            spoofedJson = trimmed.substring(0, trimmed.length() - 1)
                    + ",\"_method\":\"" + method + "\"}";
        }

        HttpRequest request = HttpRequest.newBuilder()
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
     * Authenticated multipart upload
     */
    public static ApiResponse authPostMultipart(String endpoint,
                                                String fieldName,
                                                File file)
            throws IOException, InterruptedException {

        String boundary = "----EDiscussBoundary" + System.currentTimeMillis();
        byte[] fileBytes = Files.readAllBytes(file.toPath());

        String header = "--" + boundary + "\r\n"
                + "Content-Disposition: form-data; name=\"" + fieldName
                + "\"; filename=\"" + file.getName() + "\"\r\n"
                + "Content-Type: text/csv\r\n\r\n";

        String footer = "\r\n--" + boundary + "--\r\n";

        ByteArrayOutputStream body = new ByteArrayOutputStream();
        body.write(header.getBytes());
        body.write(fileBytes);
        body.write(footer.getBytes());

        HttpRequest request = HttpRequest.newBuilder()
                .uri(URI.create(BASE_URL + endpoint))
                .header("Accept", "application/json")
                .header("Content-Type",
                        "multipart/form-data; boundary=" + boundary)
                .header("Authorization", "Bearer " + Session.getToken())
                .POST(HttpRequest.BodyPublishers.ofByteArray(body.toByteArray()))
                .build();

        HttpResponse<String> response =
                client.send(request, HttpResponse.BodyHandlers.ofString());

        return new ApiResponse(response.statusCode(), response.body());
    }

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