package com.edudiscuss.dao;

import com.edudiscuss.models.Notification;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class NotificationDAO extends BaseDAO {

    public int insert(Notification notification) {
        String sql = """
            INSERT INTO notifications(
                server_id,
                user_id,
                type,
                title,
                body,
                reference_id,
                created_at,
                is_read,
                synced,
                deleted
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        """;

        try (PreparedStatement ps = getConnection().prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            
            ps.setObject(1, notification.getServerId());
            ps.setInt(2, notification.getUserId());
            ps.setString(3, notification.getType());
            ps.setString(4, notification.getTitle());
            ps.setString(5, notification.getBody());
            ps.setObject(6, notification.getReferenceId());
            ps.setString(7, notification.getCreatedAt());
            ps.setInt(8, notification.isRead() ? 1 : 0);
            ps.setInt(9, notification.isSynced() ? 1 : 0);
            ps.setInt(10, notification.isDeleted() ? 1 : 0);

            ps.executeUpdate();

            ResultSet keys = ps.getGeneratedKeys();
            if (keys.next()) {
                return keys.getInt(1);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return -1;
    }

    public Notification findById(int id) {
        String sql = "SELECT * FROM notifications WHERE id = ? AND deleted = 0";
        
        try (PreparedStatement ps = getConnection().prepareStatement(sql)) {
            ps.setInt(1, id);
            ResultSet rs = ps.executeQuery();
            
            if (rs.next()) {
                return mapRow(rs);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return null;
    }

    public List<Notification> findByUserId(int userId) {
        List<Notification> notifications = new ArrayList<>();
        String sql = """
            SELECT * FROM notifications 
            WHERE user_id = ? AND deleted = 0 
            ORDER BY created_at DESC
        """;

        try (PreparedStatement ps = getConnection().prepareStatement(sql)) {
            ps.setInt(1, userId);
            ResultSet rs = ps.executeQuery();

            while (rs.next()) {
                notifications.add(mapRow(rs));
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return notifications;
    }

    public List<Notification> getUnread(int userId) {
        List<Notification> notifications = new ArrayList<>();
        String sql = """
            SELECT * FROM notifications 
            WHERE user_id = ? AND is_read = 0 AND deleted = 0 
            ORDER BY created_at DESC
        """;

        try (PreparedStatement ps = getConnection().prepareStatement(sql)) {
            ps.setInt(1, userId);
            ResultSet rs = ps.executeQuery();

            while (rs.next()) {
                notifications.add(mapRow(rs));
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return notifications;
    }

    public int getUnreadCount(int userId) {
        String sql = "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0 AND deleted = 0";
        
        try (PreparedStatement ps = getConnection().prepareStatement(sql)) {
            ps.setInt(1, userId);
            ResultSet rs = ps.executeQuery();
            
            if (rs.next()) {
                return rs.getInt(1);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return 0;
    }

    public void markAsRead(int notificationId) {
        String sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
        try (PreparedStatement ps = getConnection().prepareStatement(sql)) {
            ps.setInt(1, notificationId);
            ps.executeUpdate();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void markAllAsRead(int userId) {
        String sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0";
        try (PreparedStatement ps = getConnection().prepareStatement(sql)) {
            ps.setInt(1, userId);
            ps.executeUpdate();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public boolean existsByServerId(int serverId) {
        String sql = "SELECT 1 FROM notifications WHERE server_id = ? LIMIT 1";
        try (PreparedStatement ps = getConnection().prepareStatement(sql)) {
            ps.setInt(1, serverId);
            ResultSet rs = ps.executeQuery();
            return rs.next();
        } catch (Exception e) {
            e.printStackTrace();
        }
        return false;
    }

    public void markAsSynced(int localId, int serverId) {
        String sql = "UPDATE notifications SET server_id = ?, synced = 1 WHERE id = ?";
        try (PreparedStatement ps = getConnection().prepareStatement(sql)) {
            ps.setInt(1, serverId);
            ps.setInt(2, localId);
            ps.executeUpdate();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void delete(int id) {
        String sql = "UPDATE notifications SET deleted = 1 WHERE id = ?";
        try (PreparedStatement ps = getConnection().prepareStatement(sql)) {
            ps.setInt(1, id);
            ps.executeUpdate();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private Notification mapRow(ResultSet rs) throws SQLException {
        Notification notification = new Notification();
        notification.setId(rs.getInt("id"));
        
        Integer serverId = (Integer) rs.getObject("server_id");
        if (serverId != null) {
            notification.setServerId(serverId);
        }
        
        notification.setUserId(rs.getInt("user_id"));
        notification.setType(rs.getString("type"));
        notification.setTitle(rs.getString("title"));
        notification.setBody(rs.getString("body"));
        
        Integer refId = (Integer) rs.getObject("reference_id");
        if (refId != null) {
            notification.setReferenceId(refId);
        }
        
        notification.setCreatedAt(rs.getString("created_at"));
        notification.setRead(rs.getInt("is_read") == 1);
        notification.setSynced(rs.getInt("synced") == 1);
        notification.setDeleted(rs.getInt("deleted") == 1);
        
        return notification;
    }
}