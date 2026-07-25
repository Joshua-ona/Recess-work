package com.edudiscuss.repository;

import com.edudiscuss.dao.MessageDAO;
import com.edudiscuss.dao.SyncQueueDAO;
import com.edudiscuss.models.Message;

import java.util.List;

public class MessageRepository {


    private final MessageDAO dao;


    public MessageRepository(){

        dao = new MessageDAO();

    }



  private final SyncQueueDAO queueDAO =
        new SyncQueueDAO();



public void save(Message message){

    int id = dao.insert(message);


    queueDAO.add(
            "message",
            "CREATE",
            id
    );

}



    public List<Message> getMessages(){

        return dao.findAll();

    }



    public List<Message> getConversation(
            int user1,
            int user2
    ){

        return dao.findConversation(
                user1,
                user2
        );

    }



    public List<Message> getPendingMessages(){

        return dao.getPendingMessages();

    }



    public void markSynced(
            int localId,
            int serverId
    ){

        dao.markAsSynced(
                localId,
                serverId
        );

    }

}