package com.edudiscuss.services;

import java.net.HttpURLConnection;
import java.net.URL;

public class NetworkService {

    private static final String SERVER =
            "http://127.0.0.1:8000/api/ping";

    public static boolean isOnline() {

        try {

            HttpURLConnection connection =
                    (HttpURLConnection)
                    new URL(SERVER).openConnection();

            connection.setRequestMethod("GET");
            connection.setConnectTimeout(2000);
            connection.setReadTimeout(2000);

            int code = connection.getResponseCode();

            System.out.println("PING RESPONSE: " + code);

            return code == 200;

        } catch (Exception e) {

            System.out.println("PING FAILED: " + e.getMessage());

            return false;

        }

    }

}