package com.edudiscuss.controllers;

import com.edudiscuss.models.User;
import com.edudiscuss.services.LecturerDashboardService;
import com.edudiscuss.utils.Session;
import javafx.fxml.FXML;

import javafx.scene.control.Label;
import javafx.scene.layout.VBox;

public class LecturerDashboardController {

    @FXML private Label greetingLabel;

    @FXML private Label studentCountLabel;
    @FXML private Label threadsThisWeekLabel;
    @FXML private Label unansweredLabel;
    @FXML private Label satisfactionLabel;

    @FXML private VBox unansweredContainer;
    @FXML private VBox engagementContainer;
    

    private final LecturerDashboardService dashboardService =
            new LecturerDashboardService();

   
@FXML
public void initialize() {
    loadHeader(); 
    
    
    String json =
            dashboardService.getDashboardData();

    System.out.println(json);

    if(studentCountLabel != null)
        studentCountLabel.setText("0");

    if(threadsThisWeekLabel != null)
        threadsThisWeekLabel.setText("0");

    if(unansweredLabel != null)
        unansweredLabel.setText("0");

    if(satisfactionLabel != null)
        satisfactionLabel.setText("0.0");
}

    private void loadHeader() {

        User user = Session.getUser();

        if (user == null) {
            greetingLabel.setText("Good day");
            return;
        }

        greetingLabel.setText(
                "Good afternoon, Dr. "
                        + user.getFullName()
        );
    }

    

    

   
}