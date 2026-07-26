package com.edudiscuss.utils;

import com.edudiscuss.api.ApiClient;
import com.edudiscuss.database.DatabaseHelper;
import com.edudiscuss.sync.MessageSynchronizer;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;

import java.sql.ResultSet;

public class SyncManager {
    private DatabaseHelper db;
    private final MessageSynchronizer messageSynchronizer = new MessageSynchronizer();

    public SyncManager() {
        db = DatabaseHelper.getInstance();
        System.out.println("✅ SyncManager initialized");
    }

    public void syncAll() {
        if (!NetworkUtils.isOnline()) {
            System.out.println("⚠️ Offline - Cannot sync");
            return;
        }

        System.out.println("🔄 Starting sync...");
        syncReplies();
        messageSynchronizer.sync();
        System.out.println("✅ Sync complete!");
    }

    public void syncReplies() {
        try {
            ResultSet unsynced = db.getUnsyncedReplies();
            if (unsynced == null) {
                System.out.println("No unsynced replies found");
                return;
            }

            int count = 0;
            while (unsynced.next()) {
                int replyId = unsynced.getInt("id");
                int discussionId = unsynced.getInt("discussion_id");
                String content = unsynced.getString("content");

                try {
                    String json = String.format("{\"body\":\"%s\"}", content);
                    String response = ApiClient.post("discussions/" + discussionId + "/replies", json);

                    JsonObject jsonResponse = JsonParser.parseString(response).getAsJsonObject();
                    if (jsonResponse.has("message")) {
                        db.markReplySynced(replyId);
                        System.out.println("✅ Synced reply: " + replyId);
                        count++;
                    }
                } catch (Exception e) {
                    System.out.println("⚠️ Failed to sync reply: " + replyId);
                    e.printStackTrace();
                }
            }

            if (count > 0) {
                System.out.println("✅ Synced " + count + " replies");
            }

        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
