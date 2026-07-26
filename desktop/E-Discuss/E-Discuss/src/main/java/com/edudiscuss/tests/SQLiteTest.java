package com.edudiscuss.tests;

import com.edudiscuss.database.DatabaseInitializer;
import com.edudiscuss.models.Message;
import com.edudiscuss.repository.MessageRepository;

import java.time.LocalDateTime;

public class SQLiteTest {


    public static void main(String[] args) {


        DatabaseInitializer.initialize();


        MessageRepository repo =
                new MessageRepository();



        Message message =
                new Message();


        message.setSenderId(1);

        message.setReceiverId(2);

        message.setContent(
                "Offline message test"
        );


        String now =
                LocalDateTime.now().toString();


        message.setCreatedAt(now);

        message.setUpdatedAt(now);

        message.setIsRead(0);

        message.setSynced(false);

        message.setDeleted(false);



        repo.save(message);



        repo.getMessages()
                .forEach(m ->
                        System.out.println(
                                m.getContent()
                        )
                );


    }

}