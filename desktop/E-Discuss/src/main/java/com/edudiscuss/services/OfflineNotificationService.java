package com.edudiscuss.services;

import com.edudiscuss.dao.NotificationDAO;
import com.edudiscuss.dao.SyncQueueDAO;
import com.edudiscuss.models.Notification;
import com.edudiscuss.sync.NotificationSynchronizer;
import com.edudiscuss.utils.Session;

import java.time.LocalDateTime;
import java.util.List;

public class OfflineNotificationService {

    private final NotificationDAO notificationDAO = new NotificationDAO();
    private final SyncQueueDAO queueDAO = new SyncQueueDAO();
    private final NotificationSynchronizer synchronizer = new NotificationSynchronizer();

    // Save notification locally and sync later
    public void saveNotification(Notification notification) {
        notification.setUserId(Session.getUserId());
        notification.setSynced(false);
        notification.setDeleted(false);

        if (notification.getCreatedAt() == null) {
            notification.setCreatedAt(LocalDateTime.now().toString());
        }

        int localId = notificationDAO.insert(notification);
        queueDAO.add("notification", "CREATE", localId);

        // Try to sync immediately
        synchronizer.upload();
    }

    // Get all notifications from local DB
    public List<Notification> getNotifications() {
        return notificationDAO.findByUserId(Session.getUserId());
    }

    // Get unread notifications
    public List<Notification> getUnreadNotifications() {
        return notificationDAO.getUnread(Session.getUserId());
    }

    // Get unread count
    public int getUnreadCount() {
        return notificationDAO.getUnreadCount(Session.getUserId());
    }

    // Mark as read
    public void markAsRead(int notificationId) {
        notificationDAO.markAsRead(notificationId);
    }

    // Mark all as read
    public void markAllAsRead() {
        notificationDAO.markAllAsRead(Session.getUserId());
    }

    // Sync with server
    public void syncNow() {
        synchronizer.upload();
        synchronizer.download();
    }
}