package com.edudiscuss.dao;

import java.sql.*;
import java.time.LocalDateTime;
import java.util.ArrayList;
import java.util.List;

public class SyncQueueDAO extends BaseDAO {

    // Add to sync queue
    public void add(String entity, String action, int localId) {
        String sql = """
            INSERT INTO sync_queue(
                entity, action, local_id, status, created_at
            ) VALUES (?, ?, ?, ?, ?)
        """;

        try (PreparedStatement ps = getConnection().prepareStatement(sql)) {
            ps.setString(1, entity);
            ps.setString(2, action);
            ps.setInt(3, localId);
            ps.setString(4, "PENDING");
            ps.setString(5, LocalDateTime.now().toString());

            ps.executeUpdate();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    // Get pending items
    public List<SyncItem> getPending() {
        List<SyncItem> items = new ArrayList<>();
        String sql = """
            SELECT * FROM sync_queue 
            WHERE status = 'PENDING' 
            ORDER BY created_at ASC
        """;

        try (PreparedStatement ps = getConnection().prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {

            while (rs.next()) {
                items.add(mapRow(rs));
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return items;
    }

    // Remove item after successful sync
    public void remove(int id) {
        String sql = "DELETE FROM sync_queue WHERE id = ?";
        try (PreparedStatement ps = getConnection().prepareStatement(sql)) {
            ps.setInt(1, id);
            ps.executeUpdate();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    // Mark as failed with error
    public void markFailed(int id, String error) {
        String sql = """
            UPDATE sync_queue 
            SET status = 'FAILED', 
                error_message = ?, 
                attempts = attempts + 1 
            WHERE id = ?
        """;

        try (PreparedStatement ps = getConnection().prepareStatement(sql)) {
            ps.setString(1, error);
            ps.setInt(2, id);
            ps.executeUpdate();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    // Mark as synced
    public void markSynced(int id) {
        String sql = """
            UPDATE sync_queue 
            SET status = 'SYNCED', 
                synced_at = ? 
            WHERE id = ?
        """;

        try (PreparedStatement ps = getConnection().prepareStatement(sql)) {
            ps.setString(1, LocalDateTime.now().toString());
            ps.setInt(2, id);
            ps.executeUpdate();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    // Inner class for queue items
    public static class SyncItem {
        public int id;
        public String entity;
        public String action;
        public int localId;
        public String status;
        public int attempts;
        public String error;
        public String createdAt;
        public String syncedAt;
    }

    private SyncItem mapRow(ResultSet rs) throws SQLException {
        SyncItem item = new SyncItem();
        item.id = rs.getInt("id");
        item.entity = rs.getString("entity");
        item.action = rs.getString("action");
        item.localId = rs.getInt("local_id");
        item.status = rs.getString("status");
        item.attempts = rs.getInt("attempts");
        item.error = rs.getString("error_message");
        item.createdAt = rs.getString("created_at");
        item.syncedAt = rs.getString("synced_at");
        return item;
    }
}