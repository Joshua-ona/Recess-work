package com.edudiscuss.sync;

import com.edudiscuss.api.ApiClient;
import com.edudiscuss.dao.NotificationDAO;
import com.edudiscuss.dao.SyncQueueDAO;
import com.edudiscuss.dao.SyncQueueDAO.SyncItem;
import com.edudiscuss.models.Notification;
import com.edudiscuss.utils.Session;
import com.google.gson.Gson;
import com.google.gson.JsonArray;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;

import java.time.LocalDateTime;
import java.util.List;

public class NotificationSynchronizer {

    private final NotificationDAO notificationDAO = new NotificationDAO();
    private final SyncQueueDAO queueDAO = new SyncQueueDAO();
    private final Gson gson = new Gson();

    public void upload() {
        List<SyncItem> items = queueDAO.getPending();

        for (SyncItem item : items) {
            if (!item.entity.equals("notification")) {
                continue;
            }

            if (!item.action.equals("CREATE")) {
                continue;
            }

            Notification notification = notificationDAO.findById(item.localId);
            if (notification == null) {
                continue;
            }

            try {
                JsonObject body = new JsonObject();
                body.addProperty("type", notification.getType());
                body.addProperty("title", notification.getTitle());
                body.addProperty("body", notification.getBody());
                
                if (notification.getReferenceId() != null) {
                    body.addProperty("reference_id", notification.getReferenceId());
                }

                ApiClient.ApiResponse response = 
                    ApiClient.authPost("notifications", body.toString());

                if (response.statusCode == 200 || response.statusCode == 201) {
                    JsonObject json = JsonParser.parseString(response.body).getAsJsonObject();
                    int serverId = json.get("id").getAsInt();

                    notificationDAO.markAsSynced(notification.getId(), serverId);
                    queueDAO.remove(item.id);

                    System.out.println("Notification synced: " + notification.getTitle());
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }

    public void download() {
    try {

        ApiClient.ApiResponse response =
                ApiClient.authGet("notifications");

       

        if (!response.isOk()) {
            return;
        }

        JsonObject json =
                JsonParser.parseString(response.body)
                        .getAsJsonObject();

        JsonArray notifications =
                json.getAsJsonArray("notifications");

        for (int i = 0; i < notifications.size(); i++) {

            JsonObject notifJson =
                    notifications.get(i).getAsJsonObject();

            int serverId =
                    notifJson.get("id").getAsInt();

            if (notificationDAO.existsByServerId(serverId)) {
                continue;
            }

            Notification notification = new Notification();

            notification.setServerId(serverId);
            notification.setUserId(Session.getUserId());

            notification.setType(
                    notifJson.get("type").getAsString()
            );

            notification.setTitle(
                    notifJson.get("title").getAsString()
            );

            notification.setBody(
                    notifJson.get("body").getAsString()
            );

            if (notifJson.has("reference_id")
                    && !notifJson.get("reference_id").isJsonNull()) {

                notification.setReferenceId(
                    notifJson.get("reference_id").getAsInt()
                );
            }

            notification.setCreatedAt(
                    notifJson.get("created_at").getAsString()
            );

            notification.setRead(false);
            notification.setSynced(true);
            notification.setDeleted(false);

            notificationDAO.insert(notification);

            System.out.println(
                "Downloaded notification: "
                + notification.getTitle()
            );
        }

    } catch (Exception e) {
        e.printStackTrace();
    }
}
        
      


}