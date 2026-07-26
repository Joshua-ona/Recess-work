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
            Connection conn = getConnection();

            PreparedStatement ps =
                conn.prepareStatement(
                    sql,
                    Statement.RETURN_GENERATED_KEYS
                )
        ){

            ps.setObject(
                1,
                message.getServerId()
            );

            ps.setInt(
                2,
                message.getSenderId()
            );

            ps.setInt(
                3,
                message.getReceiverId()
            );

            ps.setString(
                4,
                message.getContent()
            );

            ps.setString(
                5,
                message.getCreatedAt()
            );

            ps.setString(
                6,
                message.getUpdatedAt()
            );

            ps.setInt(
                7,
                message.getIsRead()
            );

            ps.setBoolean(
                8,
                false
            );

            ps.setString(
                9,
                message.getStatus()
            );

            ps.setBoolean(
                10,
                false
            );


            ps.executeUpdate();


            try(ResultSet rs = ps.getGeneratedKeys()){

                if(rs.next()){

                    return rs.getInt(1);

                }

            }


        }catch(Exception e){

            e.printStackTrace();

        }


        return -1;
    }





    public Message findById(int id){

        String sql = """
                SELECT *
                FROM messages
                WHERE id=?
                AND deleted=0
                """;


        try(
            Connection conn=getConnection();

            PreparedStatement ps =
                conn.prepareStatement(sql)
        ){

            ps.setInt(1,id);


            try(ResultSet rs = ps.executeQuery()){

                if(rs.next()){

                    return mapRow(rs);

                }

            }


        }catch(Exception e){

            e.printStackTrace();

        }


        return null;
    }





    public List<Message> findAll(){

        List<Message> messages = new ArrayList<>();


        String sql = """
                SELECT *
                FROM messages
                WHERE deleted=0
                ORDER BY created_at ASC
                """;


        try(
            Connection conn=getConnection();

            PreparedStatement ps =
                conn.prepareStatement(sql);

            ResultSet rs =
                ps.executeQuery()
        ){

            while(rs.next()){

                messages.add(
                    mapRow(rs)
                );

            }


        }catch(Exception e){

            e.printStackTrace();

        }


        return messages;
    }





    public List<Message> findConversation(
        int user1,
        int user2
    ){

        List<Message> messages = new ArrayList<>();


        String sql = """
                SELECT *
                FROM messages
                WHERE
                (
                    (
                        sender_id=?
                        AND receiver_id=?
                    )
                    OR
                    (
                        sender_id=?
                        AND receiver_id=?
                    )
                )
                AND deleted=0
                ORDER BY created_at ASC
                """;


        try(
            Connection conn=getConnection();

            PreparedStatement ps =
                conn.prepareStatement(sql)
        ){

            ps.setInt(1,user1);
            ps.setInt(2,user2);

            ps.setInt(3,user2);
            ps.setInt(4,user1);



            try(ResultSet rs=ps.executeQuery()){


                while(rs.next()){

                    messages.add(
                        mapRow(rs)
                    );

                }

            }


        }catch(Exception e){

            e.printStackTrace();

        }


        return messages;
    }







    public List<Message> getPendingMessages(){

        List<Message> messages = new ArrayList<>();


        String sql = """
                SELECT *
                FROM messages
                WHERE synced=0
                AND deleted=0
                ORDER BY created_at ASC
                """;


        try(
            Connection conn=getConnection();

            PreparedStatement ps =
                conn.prepareStatement(sql);

            ResultSet rs =
                ps.executeQuery()

        ){

            while(rs.next()){

                messages.add(
                    mapRow(rs)
                );

            }


        }catch(Exception e){

            e.printStackTrace();

        }


        return messages;
    }







    public void markAsSynced(
        int localId,
        int serverId
    ){

        String sql = """
                UPDATE messages
                SET
                    server_id=?,
                    synced=1,
                    status='SENT'
                WHERE id=?
                """;


        try(
            Connection conn=getConnection();

            PreparedStatement ps =
                conn.prepareStatement(sql)

        ){

            ps.setInt(1,serverId);

            ps.setInt(2,localId);


            ps.executeUpdate();


        }catch(Exception e){

            e.printStackTrace();

        }

    }







    public boolean existsByServerId(
        int serverId
    ){

        String sql = """
                SELECT 1
                FROM messages
                WHERE server_id=?
                AND deleted=0
                LIMIT 1
                """;


        try(
            Connection conn=getConnection();

            PreparedStatement ps =
                conn.prepareStatement(sql)

        ){

            ps.setInt(
                1,
                serverId
            );


            try(ResultSet rs=ps.executeQuery()){

                return rs.next();

            }


        }catch(Exception e){

            e.printStackTrace();

        }


        return false;
    }








    public int countUnread(
        int userId
    ){

        String sql = """
                SELECT COUNT(*)
                FROM messages
                WHERE receiver_id=?
                AND is_read=0
                AND deleted=0
                """;


        try(
            Connection conn=getConnection();

            PreparedStatement ps =
                conn.prepareStatement(sql)

        ){

            ps.setInt(
                1,
                userId
            );


            ResultSet rs =
                ps.executeQuery();


            if(rs.next()){

                return rs.getInt(1);

            }


        }catch(Exception e){

            e.printStackTrace();

        }


        return 0;
    }







    public void markConversationRead(
        int receiverId,
        int senderId
    ){

        String sql = """
                UPDATE messages
                SET is_read=1
                WHERE receiver_id=?
                AND sender_id=?
                AND is_read=0
                AND deleted=0
                """;


        try(
            Connection conn=getConnection();

            PreparedStatement ps =
                conn.prepareStatement(sql)

        ){

            ps.setInt(1,receiverId);

            ps.setInt(2,senderId);


            ps.executeUpdate();


        }catch(Exception e){

            e.printStackTrace();

        }

    }







    public void softDelete(
        int messageId
    ){

        String sql = """
                UPDATE messages
                SET deleted=1
                WHERE id=?
                """;


        try(
            Connection conn=getConnection();

            PreparedStatement ps =
                conn.prepareStatement(sql)

        ){

            ps.setInt(
                1,
                messageId
            );

            ps.executeUpdate();


        }catch(Exception e){

            e.printStackTrace();

        }

    }







    private Message mapRow(
        ResultSet rs
    ) throws SQLException {


        Message message = new Message();


        message.setId(
            rs.getInt("id")
        );


        Object serverId =
            rs.getObject("server_id");


        if(serverId != null){

            message.setServerId(
                rs.getInt("server_id")
            );

        }else{

            message.setServerId(null);

        }



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


        message.setStatus(
            rs.getString("status")
        );


        message.setDeleted(
            rs.getBoolean("deleted")
        );


        return message;
    }

}
