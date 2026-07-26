package com.edudiscuss.services;


import com.edudiscuss.dao.MessageDAO;
import com.edudiscuss.dao.SyncQueueDAO;
import com.edudiscuss.models.Message;
import com.edudiscuss.sync.MessageSynchronizer;
import com.edudiscuss.utils.Session;


import java.time.LocalDateTime;
import java.util.List;



public class MessageService {


    private final MessageDAO messageDAO;

    private final SyncQueueDAO queueDAO;

    private final MessageSynchronizer synchronizer;



    public MessageService(){

        this.messageDAO = new MessageDAO();

        this.queueDAO = new SyncQueueDAO();

        this.synchronizer = new MessageSynchronizer();

    }





    /**
     * Creates a local message first.
     * Sync happens separately.
     */
    public boolean sendMessage(
        int receiverId,
        String content
    ){


        if(content == null || content.trim().isEmpty()){

            return false;

        }


        Message message = new Message();


        LocalDateTime now =
            LocalDateTime.now();



        message.setSenderId(
            Session.getUserId()
        );


        message.setReceiverId(
            receiverId
        );


        message.setContent(
            content.trim()
        );


        message.setCreatedAt(
            now.toString()
        );


        message.setUpdatedAt(
            now.toString()
        );


        message.setIsRead(
            0
        );


        message.setSynced(
            false
        );


        message.setDeleted(
            false
        );


        message.setStatus(
            "QUEUED"
        );



        int localId =
            messageDAO.insert(message);



        if(localId == -1){

            return false;

        }



        queueDAO.add(
            "message",
            "CREATE",
            localId
        );


        return true;

    }







    /**
     * Manually trigger sync.
     * Called when:
     * - internet returns
     * - app starts
     * - user presses refresh
     */
    public void syncMessages(){

        synchronizer.upload();

    }








    public List<Message> getConversation(
        int otherUserId
    ){


        return messageDAO.findConversation(

            Session.getUserId(),

            otherUserId

        );

    }








    public int getUnreadCount(){

        return messageDAO.countUnread(
            Session.getUserId()
        );

    }








    public void markConversationRead(
        int senderId
    ){

        messageDAO.markConversationRead(

            Session.getUserId(),

            senderId

        );

    }








    public void deleteMessage(
        int messageId
    ){

        messageDAO.softDelete(
            messageId
        );

    }


}
