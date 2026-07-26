package com.edudiscuss.sync;


import com.edudiscuss.api.ApiClient;
import com.edudiscuss.dao.MessageDAO;
import com.edudiscuss.dao.SyncQueueDAO;
import com.edudiscuss.dao.SyncQueueDAO.SyncItem;
import com.edudiscuss.models.Message;

import com.google.gson.*;

import java.util.List;



public class MessageSynchronizer {


    private final MessageDAO messageDAO;

    private final SyncQueueDAO queueDAO;


    private boolean syncing = false;



    public MessageSynchronizer(){

        messageDAO = new MessageDAO();

        queueDAO = new SyncQueueDAO();

    }






    public synchronized void sync(){


        if(syncing){

            return;

        }


        syncing = true;


        try{

            upload();

            download();


        }finally{

            syncing=false;

        }

    }








    public void upload(){


        List<SyncItem> items =
            queueDAO.getPending();



        for(SyncItem item: items){



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



            if(message==null){

                queueDAO.remove(item.id);

                continue;

            }





            try{


                JsonObject body =
                    new JsonObject();



                body.addProperty(
                    "sender_id",
                    message.getSenderId()
                );


                body.addProperty(
                    "receiver_id",
                    message.getReceiverId()
                );


                body.addProperty(
                    "content",
                    message.getContent()
                );


                body.addProperty(
                    "created_at",
                    message.getCreatedAt()
                );





                ApiClient.ApiResponse response =
                    ApiClient.authPost(
                        "private-comms/"
                            +
                            message.getReceiverId(),

                        body.toString()
                    );






                if(response.statusCode==200 ||
                    response.statusCode==201){



                    int serverId =
                        extractServerId(
                            response.body
                        );



                    if(serverId!=-1){


                        messageDAO.markAsSynced(
                            message.getId(),
                            serverId
                        );


                        queueDAO.remove(
                            item.id
                        );


                        System.out.println(
                            "Message uploaded"
                        );

                    }


                }



            }catch(Exception e){


                System.out.println(
                    "Upload failed "
                        +
                        message.getId()
                );


                e.printStackTrace();

            }


        }

    }









    public void download(){


        try{


            ApiClient.ApiResponse response =
                ApiClient.authGet(
                    "private-comms/sync"
                );



            if(!response.isOk()){

                return;

            }





            JsonObject root =
                JsonParser
                    .parseString(
                        response.body
                    )
                    .getAsJsonObject();




            JsonArray messages =
                root.getAsJsonArray(
                    "messages"
                );




            for(JsonElement element: messages){



                JsonObject obj =
                    element.getAsJsonObject();



                int serverId =
                    obj.get("id")
                        .getAsInt();





                if(
                    messageDAO
                        .existsByServerId(serverId)
                ){

                    continue;

                }






                Message message =
                    new Message();



                message.setServerId(
                    serverId
                );


                message.setSenderId(
                    obj.get("sender_id")
                        .getAsInt()
                );


                message.setReceiverId(
                    obj.get("receiver_id")
                        .getAsInt()
                );



                message.setContent(
                    obj.get("content")
                        .getAsString()
                );

                message.setCreatedAt(
                    obj.get("created_at")
                        .getAsString()
                );

                message.setUpdatedAt(
                    obj.get("updated_at")
                        .getAsString()
                );
                message.setIsRead(
                    obj.get("is_read")
                        .getAsInt()
                );



                message.setSynced(
                    true
                );


                message.setDeleted(
                    false
                );


                message.setStatus(
                    "SENT"
                );

                messageDAO.insert(message);
            }

        }catch(Exception e){

            e.printStackTrace();

        }


    }

    private int extractServerId(
        String json
    ){


        try{


            JsonObject root =
                JsonParser
                    .parseString(json)
                    .getAsJsonObject();



            if(root.has("private_message")){


                return root
                    .getAsJsonObject(
                        "private_message"
                    )
                    .get("id")
                    .getAsInt();

            }



            return root
                .get("id")
                .getAsInt();



        }catch(Exception e){


            e.printStackTrace();

        }



        return -1;

    }


}
