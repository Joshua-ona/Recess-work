package com.edudiscuss.api;

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
}