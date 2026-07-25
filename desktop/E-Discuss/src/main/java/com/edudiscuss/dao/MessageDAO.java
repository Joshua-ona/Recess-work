package com.edudiscuss.dao;

import com.edudiscuss.models.Message;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class MessageDAO extends BaseDAO {


   public int insert(Message message) {


    String sql = """
           INSERT INTO messages(
    server_id,
    sender_id,
    receiver_id,
    content,
    created_at,
    updated_at,
    is_read,
    synced,
    status,
    deleted
)
VALUES(?,?,?,?,?,?,?,?,?,?)
            """; 


    try(
        PreparedStatement ps =
        getConnection()
        .prepareStatement(
            sql,
            Statement.RETURN_GENERATED_KEYS
        )
    ){


        ps.setObject(1,message.getServerId());

        ps.setInt(2,message.getSenderId());

        ps.setInt(3,message.getReceiverId());

        ps.setString(4,message.getContent());

        ps.setString(5,message.getCreatedAt());

        ps.setString(6,message.getUpdatedAt());

        ps.setInt(7,message.getIsRead());

       ps.setBoolean(8, false);
ps.setString(9, message.getStatus());
ps.setBoolean(10, false);



        ps.executeUpdate();



        ResultSet keys =
                ps.getGeneratedKeys();


        if(keys.next()){

            return keys.getInt(1);

        }


    }catch(Exception e){

        e.printStackTrace();

    }


    return -1;

}
public Message findById(int id){


    String sql =
            """
            SELECT *
            FROM messages
            WHERE id=?
            """;


    try(
        PreparedStatement ps =
        getConnection().prepareStatement(sql)
    ){


        ps.setInt(1,id);


        ResultSet rs =
                ps.executeQuery();



        if(rs.next()){

            return mapRow(rs);

        }


    }catch(Exception e){

        e.printStackTrace();

    }


    return null;

}


    public List<Message> findAll() {

        List<Message> messages = new ArrayList<>();

        String sql = """
                SELECT *
                FROM messages
                WHERE deleted = 0
                ORDER BY created_at ASC
                """;

        try (
                PreparedStatement ps = getConnection().prepareStatement(sql);
                ResultSet rs = ps.executeQuery()
        ) {

            while (rs.next()) {

                messages.add(mapRow(rs));

            }

        } catch (Exception e) {
            e.printStackTrace();
        }

        return messages;
    }


    public List<Message> findConversation(int user1, int user2) {

        List<Message> messages = new ArrayList<>();

        String sql = """
                SELECT *
                FROM messages
                WHERE
                (
                    sender_id = ?
                    AND receiver_id = ?
                )
                OR
                (
                    sender_id = ?
                    AND receiver_id = ?
                )
                AND deleted = 0
                ORDER BY created_at ASC
                """;


        try (PreparedStatement ps = getConnection().prepareStatement(sql)) {


            ps.setInt(1,user1);
            ps.setInt(2,user2);

            ps.setInt(3,user2);
            ps.setInt(4,user1);


            ResultSet rs = ps.executeQuery();


            while(rs.next()){

                messages.add(mapRow(rs));

            }


        } catch(Exception e){

            e.printStackTrace();

        }


        return messages;
    }



    public List<Message> getPendingMessages(){

        List<Message> messages = new ArrayList<>();

        String sql = """
                SELECT *
                FROM messages
                WHERE synced = 0
                AND deleted = 0
                """;


        try(
                PreparedStatement ps =
                        getConnection().prepareStatement(sql);

                ResultSet rs =
                        ps.executeQuery()
        ){

            while(rs.next()){

                messages.add(mapRow(rs));

            }


        }catch(Exception e){

            e.printStackTrace();

        }


        return messages;

    }



    public void markAsSynced(int localId,int serverId){


        String sql = """
                UPDATE messages
SET
    server_id=?,
    synced=1,
    status='SENT'
WHERE id=?
                """;


        try(PreparedStatement ps =
                    getConnection().prepareStatement(sql)){


            ps.setInt(1,serverId);
            ps.setInt(2,localId);


            ps.executeUpdate();


        }catch(Exception e){

            e.printStackTrace();

        }

    }
    public boolean existsByServerId(int serverId) {

    String sql = """
            SELECT 1
            FROM messages
            WHERE server_id = ?
            LIMIT 1
            """;

    try (PreparedStatement ps =
                 getConnection().prepareStatement(sql)) {

        ps.setInt(1, serverId);

        ResultSet rs = ps.executeQuery();

        return rs.next();

    } catch (Exception e) {

        e.printStackTrace();

    }

    return false;
}



    private Message mapRow(ResultSet rs)throws SQLException{


        Message message = new Message();


        message.setId(
                rs.getInt("id")
        );

        message.setStatus(
    rs.getString("status")
);


        message.setServerId(
                (Integer) rs.getObject("server_id")
        );


        message.setSenderId(
                rs.getInt("sender_id")
        );


        message.setReceiverId(
                rs.getInt("receiver_id")
        );


        message.setContent(
                rs.getString("content")
        );


        message.setCreatedAt(
                rs.getString("created_at")
        );


        message.setUpdatedAt(
                rs.getString("updated_at")
        );


        message.setIsRead(
                rs.getInt("is_read")
        );


        message.setSynced(
                rs.getBoolean("synced")
        );


        message.setDeleted(
                rs.getBoolean("deleted")
        );


        return message;

    }

}