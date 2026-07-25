package com.edudiscuss.sync;

import com.edudiscuss.api.ApiClient;
import com.edudiscuss.dao.MessageDAO;
import com.edudiscuss.dao.SyncQueueDAO;
import com.edudiscuss.dao.SyncQueueDAO.SyncItem;
import com.edudiscuss.models.Message;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;
import com.google.gson.JsonArray;


import java.util.List;

public class MessageSynchronizer {


    private final MessageDAO messageDAO;
    private final SyncQueueDAO queueDAO;


    public MessageSynchronizer(){

        messageDAO = new MessageDAO();
        queueDAO = new SyncQueueDAO();

    }



    public void upload(){


        List<SyncItem> items =
                queueDAO.getPending();



        for(SyncItem item : items){


            if(!item.entity.equals("message")){
                continue;
            }


            if(!item.action.equals("CREATE")){
                continue;
            }



            Message message =
                    messageDAO.findById(
                            item.localId
                    );


            if(message == null){
                continue;
            }



            try {

JsonObject body = new JsonObject();

body.addProperty(
        "content",
        message.getContent()
);



                ApiClient.ApiResponse response =
        ApiClient.authPost(
    "private-comms/" + message.getReceiverId(),
    body.toString()
);



                System.out.println(
                        "MESSAGE SYNC RESPONSE:"
                );


                System.out.println(
                        response.body
                );



                if(response.statusCode == 200 ||
                   response.statusCode == 201){



                    int serverId =
                            extractId(
                                    response.body
                            );



                    messageDAO.markAsSynced(
                            message.getId(),
                            serverId
                    );



                    queueDAO.remove(
                            item.id
                    );



                    System.out.println(
                            "Message synced"
                    );

                }


            }catch(Exception e){

                e.printStackTrace();

            }


        }

    }
    public void download() {

    try {

        ApiClient.ApiResponse response =
                ApiClient.authGet("private-comms/sync");

        if (!response.isOk()) {
            return;
        }

        JsonObject json =
                JsonParser.parseString(response.body)
                        .getAsJsonObject();

        JsonArray messages =
                json.getAsJsonArray("messages");

        for (JsonElement element : messages) {

            Message message =
                    new com.google.gson.Gson()
                            .fromJson(element, Message.class);

            // Skip if already downloaded
            if (messageDAO.existsByServerId(message.getId())) {
                continue;
            }

            message.setServerId(message.getId());

            message.setStatus("SENT");

            message.setSynced(true);

            messageDAO.insert(message);

        }

    } catch (Exception e) {

        e.printStackTrace();

    }

}



  private int extractId(String json) {

    try {

        JsonObject obj = JsonParser
                .parseString(json)
                .getAsJsonObject();

        JsonObject privateMessage =
                obj.getAsJsonObject("private_message");

        return privateMessage
                .get("id")
                .getAsInt();

    } catch (Exception e) {

        e.printStackTrace();

        return -1;

    }

}
}