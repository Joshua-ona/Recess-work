package com.edudiscuss.services;

import com.edudiscuss.api.ApiClient;
import com.edudiscuss.models.Message;
import com.edudiscuss.models.StudentDashboard;
import com.edudiscuss.models.User;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;
import com.edudiscuss.models.Quiz;

import java.lang.reflect.Type;
import java.util.List;

public class ApiService {

    private final Gson gson = new Gson();

    public StudentDashboard getStudentDashboard() throws Exception {

        String response = ApiClient.get("student/dashboard");

        return gson.fromJson(response, StudentDashboard.class);
    }

   public List<User> getChatUsers() throws Exception {

    ApiClient.ApiResponse response =
            ApiClient.authGet("private-comms/users");

    System.out.println("Status: " + response.statusCode);
    System.out.println(response.body);

    Type type = new TypeToken<List<User>>(){}.getType();

    return gson.fromJson(response.body, type);
}

    public List<Message> getConversation(int userId) throws Exception {

       ApiClient.ApiResponse response =
        ApiClient.authGet("private-comms/" + userId);

System.out.println(response.body);

Type type = new TypeToken<List<Message>>(){}.getType();

return gson.fromJson(response.body, type);
       
       
    }

    public void sendMessage(int userId, String message)
            throws Exception {

        String json = String.format("""
        {
            "content":"%s"
        }
        """, message);

    ApiClient.authPost("private-comms/" + userId, json);
    }
    public List<Quiz> getQuizzes() throws Exception {


  
    Type type =
            new TypeToken<List<Quiz>>(){}.getType();

ApiClient.ApiResponse response =
        ApiClient.authGet("quizzes");

return gson.fromJson(response.body, type);

}

    public void joinGroup(int groupId) throws Exception {
        ApiClient.post("groups/" + groupId + "/join", "");
    }

    public void requestGroup(String name, String description) throws Exception {
        com.google.gson.JsonObject payload = new com.google.gson.JsonObject();
        payload.addProperty("name", name);
        payload.addProperty("description", description);
        ApiClient.post("groups", gson.toJson(payload));
    }
}