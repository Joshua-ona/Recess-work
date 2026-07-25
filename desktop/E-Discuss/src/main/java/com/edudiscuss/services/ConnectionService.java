package com.edudiscuss.services;

import javafx.application.Platform;

import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.*;



public class ConnectionService {

    private final ScheduledExecutorService executor =
            Executors.newSingleThreadScheduledExecutor();

    private final List<ConnectionListener> listeners =
            new ArrayList<>();

    private ConnectionState currentState = ConnectionState.OFFLINE;

    public void start() {

        executor.scheduleAtFixedRate(() -> {

            ConnectionState newState =
                    NetworkService.isOnline()
                            ? ConnectionState.ONLINE
                            : ConnectionState.OFFLINE;

            if (newState != currentState) {

                currentState = newState;

                Platform.runLater(() -> {

                    for (ConnectionListener listener : listeners) {

                        listener.onConnectionChanged(currentState);

                    }

                });

            }

        },0,30, TimeUnit.SECONDS);

    }

    public void stop() {

        executor.shutdown();

    }

    public void addListener(ConnectionListener listener) {

        listeners.add(listener);

    }

}