package com.edudiscuss.services;

import com.edudiscuss.dao.MessageDAO;
import com.edudiscuss.dao.SyncQueueDAO;
import com.edudiscuss.models.Message;
import com.edudiscuss.sync.MessageSynchronizer;
import com.edudiscuss.utils.Session;

import java.time.LocalDateTime;
import java.util.List;

public class MessageService {

    private final MessageDAO messageDAO = new MessageDAO();
    private final SyncQueueDAO queueDAO = new SyncQueueDAO();
    private final MessageSynchronizer synchronizer = new MessageSynchronizer();

    public void sendMessage(int receiverId, String content) {

        Message message = new Message();

        message.setSenderId(Session.getUserId());
        message.setReceiverId(receiverId);
        message.setContent(content);
        message.setCreatedAt(LocalDateTime.now().toString());
        message.setUpdatedAt(LocalDateTime.now().toString());
        message.setIsRead(0);
        message.setSynced(false);
        message.setDeleted(false);
        message.setStatus("QUEUED");

        int localId = messageDAO.insert(message);

        queueDAO.add("message", "CREATE", localId);

        synchronizer.upload();
    }

    public List<Message> getConversation(int userId) {

        return messageDAO.findConversation(
                Session.getUserId(),
                userId
        );
    }
}