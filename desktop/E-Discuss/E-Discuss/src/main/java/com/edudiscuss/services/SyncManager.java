package com.edudiscuss.services;

import com.edudiscuss.sync.MessageSynchronizer;


public class SyncManager {


    private final MessageSynchronizer messageSynchronizer;


    public SyncManager(){

        messageSynchronizer =
                new MessageSynchronizer();

    }



    public void sync(){


        System.out.println("SYNC START");


        if(!NetworkService.isOnline()){

            System.out.println(
                    "OFFLINE"
            );

            return;
        }



        System.out.println(
                "SYNCING..."
        );


        messageSynchronizer.upload();



        System.out.println(
                "SYNC DONE"
        );

    }

}