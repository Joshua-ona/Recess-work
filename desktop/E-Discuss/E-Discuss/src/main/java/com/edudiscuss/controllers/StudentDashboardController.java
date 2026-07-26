package com.edudiscuss.controllers;


import com.edudiscuss.models.StudentDashboard;
import com.edudiscuss.models.User;
import com.edudiscuss.services.ApiService;
import com.edudiscuss.utils.Session;

import javafx.application.Platform;
import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.scene.layout.VBox;

import java.time.LocalTime;


public class StudentDashboardController {


    @FXML
    private Label greetingLabel;


    @FXML
    private Label postsLabel;


    @FXML
    private Label quizLabel;


    @FXML
    private Label groupsLabel;


    @FXML
    private Label scoreLabel;



    @FXML
    private ListView<String> myGroupsList;


    @FXML
    private ListView<String> browseGroupsList;


    @FXML
    private VBox recommendedGroupsBox;


    @FXML
    private TextField groupNameField;


    @FXML
    private Button requestGroupButton;


    @FXML
    private Label groupRequestStatusLabel;



    private final ApiService apiService = new ApiService();



    @FXML
    public void initialize(){

        loadGreeting();

        loadDashboard();

    }





    @FXML
    private void handleRequestGroup(){

        String name = groupNameField.getText() == null ? "" : groupNameField.getText().trim();

        if(name.isEmpty()){
            groupRequestStatusLabel.setStyle("-fx-font-size:11px; -fx-text-fill:#dc2626;");
            groupRequestStatusLabel.setText("Enter a group name first.");
            return;
        }

        requestGroupButton.setDisable(true);
        groupRequestStatusLabel.setStyle("-fx-font-size:11px; -fx-text-fill:#666;");
        groupRequestStatusLabel.setText("Sending request...");

        new Thread(() -> {

            try {
                apiService.requestGroup(name, "");

                Platform.runLater(() -> {
                    requestGroupButton.setDisable(false);
                    groupNameField.clear();
                    groupRequestStatusLabel.setStyle("-fx-font-size:11px; -fx-text-fill:#166534;");
                    groupRequestStatusLabel.setText("Requested — waiting for admin approval.");
                });

            } catch (Exception e) {
                e.printStackTrace();

                Platform.runLater(() -> {
                    requestGroupButton.setDisable(false);
                    groupRequestStatusLabel.setStyle("-fx-font-size:11px; -fx-text-fill:#dc2626;");
                    groupRequestStatusLabel.setText("Failed: " + e.getMessage());
                });
            }

        }).start();
    }




    private void loadGreeting(){

        User user = Session.getUser();


        if(user == null){

            greetingLabel.setText("Welcome");

            return;
        }



        int hour = LocalTime.now().getHour();


        String greeting;


        if(hour < 12){

            greeting="Good morning";

        }
        else if(hour < 17){

            greeting="Good afternoon";

        }
        else{

            greeting="Good evening";

        }


        greetingLabel.setText(
            greeting+", "+user.getFirst_name()
        );

    }





    private void loadDashboard(){


        try{


            StudentDashboard dashboard =
                apiService.getStudentDashboard();



            updateStats(dashboard);


            loadGroups(dashboard);


            loadRecommendations(dashboard);



        }
        catch(Exception e){


            e.printStackTrace();


            postsLabel.setText("0");
            quizLabel.setText("0");
            groupsLabel.setText("0");
            scoreLabel.setText("0");


        }


    }






    private void updateStats(StudentDashboard dashboard){


        postsLabel.setText(
            String.valueOf(
                dashboard.getPosts()
            )
        );


        quizLabel.setText(
            String.valueOf(
                dashboard.getQuizzes()
            )
        );



        groupsLabel.setText(
            String.valueOf(
                dashboard.getGroups()
            )
        );



        scoreLabel.setText(
            String.format(
                "%.0f",
                dashboard.getParticipation()
            )
        );

    }





    private void loadGroups(StudentDashboard dashboard){



        myGroupsList.getItems().clear();


        if(dashboard.getMyGroups()!=null){


            myGroupsList.getItems()
                .addAll(
                    dashboard.getMyGroups()
                );

        }



        browseGroupsList.getItems().clear();


        if(dashboard.getBrowseGroups()!=null){


            browseGroupsList.getItems()
                .addAll(
                    dashboard.getBrowseGroups()
                );

        }


    }





    private void loadRecommendations(StudentDashboard dashboard){


        recommendedGroupsBox.getChildren()
            .clear();



        if(
            dashboard.getRecommendedGroups() == null ||
                dashboard.getRecommendedGroups().isEmpty()
        ){

            Label empty =
                new Label(
                    "No recommendations yet.\n" +
                        "Join groups to get personalised suggestions."
                );


            recommendedGroupsBox
                .getChildren()
                .add(empty);


            return;
        }



        dashboard.getRecommendedGroups()
            .forEach(group -> {


                VBox card =
                    new VBox(8);


                card.getStyleClass()
                    .add("card");



                Label name =
                    new Label(
                        group.getName()
                    );


                name.getStyleClass()
                    .add("group-title");



                Label description =
                    new Label(
                        group.getDescription()
                    );


                Label match =
                    new Label(
                        String.format(
                            "%.0f%% match",
                            group.getScore()
                        )
                    );


                match.getStyleClass()
                    .add("match-label");



                Button join =
                    new Button(
                        "Join"
                    );

                join.getStyleClass().add("purple-btn");



                join.setOnAction(e -> {

                    join.setDisable(true);
                    join.setText("Joining...");

                    new Thread(() -> {

                        try {
                            apiService.joinGroup(group.getId());

                            Platform.runLater(() -> {
                                join.setText("Joined ✓");
                                // Refresh everything so My Groups / Browse / Recommendations
                                // all stay in sync with the server, same as the web dashboard.
                                loadDashboard();
                            });

                        } catch (Exception ex) {
                            ex.printStackTrace();

                            Platform.runLater(() -> {
                                join.setDisable(false);
                                join.setText("Join");

                                Alert alert = new Alert(Alert.AlertType.ERROR);
                                alert.setTitle("Error");
                                alert.setHeaderText(null);
                                alert.setContentText("Failed to join group: " + ex.getMessage());
                                alert.showAndWait();
                            });
                        }

                    }).start();

                });



                card.getChildren()
                    .addAll(
                        name,
                        description,
                        match,
                        join
                    );



                recommendedGroupsBox
                    .getChildren()
                    .add(card);


            });


    }



}
