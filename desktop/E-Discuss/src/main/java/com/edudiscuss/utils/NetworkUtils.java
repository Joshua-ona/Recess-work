package com.edudiscuss.utils;

import com.edudiscuss.api.ApiClient;

import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.time.Duration;

public class NetworkUtils {

    /**
     * "Online" for this app specifically means "can we reach the Laravel
     * API server". Pinging an external host like 8.8.8.8 is the wrong
     * check here: this is a local-first desktop client talking to a
     * local dev server (127.0.0.1:8000), and ICMP ping is frequently
     * blocked by Windows Firewall / campus networks even when normal
     * HTTP traffic works fine — that was previously causing this check
     * to report "offline" even while the API server was reachable,
     * silently forcing every screen onto stale local SQLite data.
     *
     * Any HTTP response at all (even a 404) proves the server is
     * reachable; only a connection failure/timeout means it's actually
     * down.
     */
    public static boolean isOnline() {
        try {
            HttpClient client = HttpClient.newBuilder()
                    .connectTimeout(Duration.ofSeconds(3))
                    .build();

            HttpRequest request = HttpRequest.newBuilder()
                    .uri(URI.create(ApiClient.BASE_URL))
                    .timeout(Duration.ofSeconds(3))
                    .GET()
                    .build();

            client.send(request, HttpResponse.BodyHandlers.discarding());
            return true;

        } catch (Exception e) {
            return false;
        }
    }
}
