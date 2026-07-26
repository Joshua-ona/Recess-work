package com.edudiscuss.models;

/**
 * Represents an in-app notification.
 *
 * snake_case getters (getCreated_at, etc.) mirror the Laravel API's
 * JSON keys and are used by the UI (NotificationController). camelCase
 * getters/setters are used by the local SQLite layer (NotificationDAO,
 * NotificationSynchronizer) for offline sync bookkeeping.
 */
public class Notification {

    private int id;
    private Integer server_id;
    private int user_id;
    private String type;
    private String title;
    private String body;
    private Integer reference_id;
    private String created_at;
    private boolean read;
    private boolean synced;
    private boolean deleted;

    public Notification() {
    }

    /* ===================== snake_case (API / UI) ===================== */

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public String getTitle() {
        return title;
    }

    public void setTitle(String title) {
        this.title = title;
    }

    public String getBody() {
        return body;
    }

    public void setBody(String body) {
        this.body = body;
    }

    public int getReference_id() {
        return reference_id != null ? reference_id : 0;
    }

    public String getCreated_at() {
        return created_at;
    }

    public void setCreated_at(String created_at) {
        this.created_at = created_at;
    }

    public boolean isRead() {
        return read;
    }

    public void setRead(boolean read) {
        this.read = read;
    }

    /* ================ camelCase (local SQLite / sync layer) =============== */

    public Integer getServerId() {
        return server_id;
    }

    public void setServerId(Integer serverId) {
        this.server_id = serverId;
    }

    public int getUserId() {
        return user_id;
    }

    public void setUserId(int userId) {
        this.user_id = userId;
    }

    public Integer getReferenceId() {
        return reference_id;
    }

    public void setReferenceId(Integer referenceId) {
        this.reference_id = referenceId;
    }

    public String getCreatedAt() {
        return created_at;
    }

    public void setCreatedAt(String createdAt) {
        this.created_at = createdAt;
    }

    public boolean isSynced() {
        return synced;
    }

    public void setSynced(boolean synced) {
        this.synced = synced;
    }

    public boolean isDeleted() {
        return deleted;
    }

    public void setDeleted(boolean deleted) {
        this.deleted = deleted;
    }
}
