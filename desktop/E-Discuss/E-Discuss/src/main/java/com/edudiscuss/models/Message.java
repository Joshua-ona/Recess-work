package com.edudiscuss.models;

/**
 * Represents a private message.
 *
 * Two accessor styles are kept on purpose:
 *  - snake_case getters/setters mirror the Laravel API's JSON keys
 *    (sender_id, receiver_id, created_at, is_read) so Gson can
 *    deserialize API responses straight into this class, and so the
 *    chat UI (MessageBubble, MessagesController) keeps working.
 *  - camelCase getters/setters are used by the local SQLite layer
 *    (MessageDAO, MessageSynchronizer) for offline sync bookkeeping
 *    (server_id, synced, status, deleted, updated_at).
 * Both sets read/write the same underlying fields.
 */
public class Message {

    private int id;
    private Integer server_id;
    private int sender_id;
    private int receiver_id;
    private String content;
    private String created_at;
    private String updated_at;
    private int is_read;
    private boolean synced;
    private String status;
    private boolean deleted;

    private User sender;

    /* ===================== snake_case (API / UI) ===================== */

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getSender_id() {
        return sender_id;
    }

    public void setSender_id(int sender_id) {
        this.sender_id = sender_id;
    }

    public int getReceiver_id() {
        return receiver_id;
    }

    public void setReceiver_id(int receiver_id) {
        this.receiver_id = receiver_id;
    }

    public String getContent() {
        return content;
    }

    public void setContent(String content) {
        this.content = content;
    }

    public String getCreated_at() {
        return created_at;
    }

    public void setCreated_at(String created_at) {
        this.created_at = created_at;
    }

    public int getIs_read() {
        return is_read;
    }

    public void setIs_read(int is_read) {
        this.is_read = is_read;
    }

    public User getSender() {
        return sender;
    }

    public void setSender(User sender) {
        this.sender = sender;
    }

    /* ================ camelCase (local SQLite / sync layer) =============== */

    public Integer getServerId() {
        return server_id;
    }

    public void setServerId(Integer serverId) {
        this.server_id = serverId;
    }

    public int getSenderId() {
        return sender_id;
    }

    public void setSenderId(int senderId) {
        this.sender_id = senderId;
    }

    public int getReceiverId() {
        return receiver_id;
    }

    public void setReceiverId(int receiverId) {
        this.receiver_id = receiverId;
    }

    public String getCreatedAt() {
        return created_at;
    }

    public void setCreatedAt(String createdAt) {
        this.created_at = createdAt;
    }

    public String getUpdatedAt() {
        return updated_at;
    }

    public void setUpdatedAt(String updatedAt) {
        this.updated_at = updatedAt;
    }

    public int getIsRead() {
        return is_read;
    }

    public void setIsRead(int isRead) {
        this.is_read = isRead;
    }

    public boolean isSynced() {
        return synced;
    }

    public void setSynced(boolean synced) {
        this.synced = synced;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public boolean isDeleted() {
        return deleted;
    }

    public void setDeleted(boolean deleted) {
        this.deleted = deleted;
    }
}
