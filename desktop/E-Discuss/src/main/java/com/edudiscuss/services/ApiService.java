package com.edudiscuss.services;

import com.edudiscuss.api.ApiClient;
import com.edudiscuss.models.Message;
import com.edudiscuss.models.StudentDashboard;
import com.edudiscuss.models.User;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;

import java.lang.reflect.Type;
import java.util.List;

public class ApiService {

    private final Gson gson = new Gson();

    public StudentDashboard getStudentDashboard() throws Exception {

        String response = ApiClient.get("student/dashboard");

        return gson.fromJson(response, StudentDashboard.class);
    }

    public List<User> getChatUsers() throws Exception {

        String response = ApiClient.get("private-comms/users");

        Type type = new TypeToken<List<User>>(){}.getType();

        return gson.fromJson(response, type);
    }

    public List<Message> getConversation(int userId) throws Exception {

        String response =
                ApiClient.get("private-comms/" + userId);

        System.out.println("Conversation JSON:");
        System.out.println(response);

        Type type =
                new TypeToken<List<Message>>(){}.getType();

        return gson.fromJson(response, type);
    }

    public void sendMessage(int userId, String message)
            throws Exception {

        String json = String.format("""
        {
            "content":"%s"
        }
        """, message);

        ApiClient.post("private-comms/" + userId, json);
    }
}