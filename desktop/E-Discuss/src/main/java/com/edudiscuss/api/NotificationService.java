package com.edudiscuss.api;

import com.edudiscuss.models.CountResponse;
import com.edudiscuss.models.NotificationResponse;
import com.google.gson.Gson;

public class NotificationService {

    private static final Gson gson = new Gson();

    public static NotificationResponse getNotifications() {

        try {

            ApiClient.ApiResponse response =
                    ApiClient.authGet("notifications");

            if (!response.isOk()) {
                return null;
            }

            return gson.fromJson(
                    response.body,
                    NotificationResponse.class
            );

        } catch (Exception e) {

            e.printStackTrace();
            return null;

        }

    }


    public static int getUnreadCount() {

        try {

            ApiClient.ApiResponse response =
                    ApiClient.authGet("notifications/count");

            if (!response.isOk()) {
                return 0;
            }

            CountResponse count =
                    gson.fromJson(
                            response.body,
                            CountResponse.class
                    );

            return count.getCount();

        } catch (Exception e) {

            e.printStackTrace();
            return 0;

        }

    }


    public static boolean markAsRead(int id) {

        try {

            ApiClient.ApiResponse response =
                    ApiClient.authPostAsMethod(
                            "notifications/" + id + "/read",
                            "{}",
                            "PUT"
                    );

            return response.isOk();

        } catch (Exception e) {

            e.printStackTrace();
            return false;

        }

    }

}