<?php
    // Starts Sessions so we can pass variables
    session_start();
?>

<html>
    <head>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <form id='message' action='message.php' method='post' accept-charset='UTF-8'>
            <legend>Bobchats Messaging (Enter an individual user and/or group to send your message to) </legend>
            <input type='hidden' name='submitted' id='submitted' value='1'/>
            <label for='name'>Individual to Message:</label>
            <input type='text' name='name' id='name' maxlength="50"/>
            <label for='group_name'>Group to Message:</label>
            <input type='text' name='group_name' id='group_name' maxlength="50"/>
            <br>
            <textarea name="messageBox" form="message">Enter Message Here</textarea>
            <input type='submit' name='Submit' value='Submit'/>
        </form>
    </body>
</html>

<?php
// Makes sure out html has run
if(isset($_POST['Submit'])) {
    include 'test_con.php';
    
    // Connects to the database
    $pdo = connect();
    // Our User ID
    $user_id = $_SESSION['userID'];
    
    // Catchs empty values
    if ($_POST['name'] == null && $_POST['group_name']) {
        echo "Enter a user to send the message to!";
        return false;
    }
    if ($_POST['messageBox'] == null) {
        echo "Enter a message!";
        return false;
    }
    
    // Finds id of msg recipient
    if ($_POST['name'] != null) {
        // Seperates $name = 0 - First name / 1 - Last name
        $flname = explode(" ", $_POST['name']);
        
        // Get ID of user we are messaging
        try {
            $rec_query = $pdo->prepare("SELECT user_id FROM user WHERE first_name = ? AND last_name = ?");
            $rec_query->bindParam(1, $flname[0]);
            $rec_query->bindParam(2, $flname[1]);
            $rec_query->execute();
            $rec_id = $rec_query->fetchColumn();
        } catch(PDOException $e) {
            echo $e;
        }
    }
    
    // Finds id of msg recpient group
    if ($_POST['group_name'] != null) {
        $gname = trim($_POST['group_name']);
        
        // Get ID of user we are messaging
        try {
            $rec_query = $pdo->prepare("SELECT group_id FROM group WHERE group_name = ?");
            $rec_query->bindParam(1, $gname);
            $rec_query->execute();
            $group_rec_id = $rec_query->fetchColumn();
        } catch(PDOException $e) {
            echo $e;
        }
    }

    // Our Message to Send
    $msg = $_POST['messageBox'];
    // Our Message Subject
    $subject = $_POST['subject'];
    // The Parent Message if replying
    if ($reply) {
        // TODO
        $parent_msg_id = null;
    }
    else {
        $parent_msg_id = null;
    }
    // If there is an Attachment
    if ($attach != null) {
        // TODO
        $attach_id = null;
    }
    else {
        $attach_id = null;
    }
    
    // Our Current Message ID
    try {
        $msg_id_query = pdo->prepare("SELECT (MAX(msg_id) +1) FROM message");
        $msg_id = $msg_id_query->execute();
    } catch(PDOException $e) {
        echo $e;
    }
    
    // Create a Draft of our message
    try {
        $msg_insert = pdo->prepare("INSERT INTO message (
                                   msg_id,
                                   subject,
                                   message,
                                   creator_id,
                                   create_date,
                                   parent_msg_id,
                                   attach_id
                                   ) VALUES (
                                             ?, ?, ?, ?,
                                             NOW(), ?, ? )"
        );
        
        $msg_insert->bindParam(1, $msg_id);
        $msg_insert->bindParam(2, $subject);
        $msg_insert->bindParam(3, $msg);
        $msg_insert->bindParam(4, $user_id);
        $msg_insert->bindParam(5, $parent_msg_id);
        $msg_insert->bindParam(6, $attach_id);

        $msg_insert->execute();
    } catch(PDOException $e) {
        echo $e;
    }
    
    // If we are sending msg to a user
    if ($rec_id != null) {
        try {
            $msg_send = pdo->prepare("INSERT INTO msg_recipient (
                                     msg_rec_id,
                                     recipient_id,
                                     recipient_group_id,
                                     msg_id
                                     ) VALUES (
                                               (SELECT MAX(msg_rec_id) + 1 FROM msg_recipient),
                                               ?, ?, ?)"
            );
            
            $msg_send->bindParam(1, $rec_id);
            $msg_send->bindParam(2, null);
            $msg_send->bindParam(3, $msg_id);
            
            $msg_insert->execute();
        } catch(PDOException $e) {
            echo $e;
        }
    }
    
    // If we are sending msg to a group
    if ($group_rec_id != null) {
        
        // Finds each member in a group
        try {
            $group_query = pdo->prepare("SELECT user_id FROM user_group WHERE group_id = ?");
            $group_query->bindParam(1, $group_rec_id);
            $members = $group_query->execute();
        } catch(PDOException $e) {
            echo $e;
        }
        
        // For each member in a group
        while(($grec_id = $members->fetchRow) != null) {
            try {
                $msg_send = pdo->prepare("INSERT INTO msg_recipient (
                                         msg_rec_id,
                                         recipient_id,
                                         recipient_group_id,
                                         msg_id
                                         ) VALUES (
                                                   (SELECT MAX(msg_rec_id) + 1 FROM msg_recipient),
                                                   ?, ?, ?)"
                );
                
                $msg_send->bindParam(1, $grec_id);
                $msg_send->bindParam(2, $group_rec_id);
                $msg_send->bindParam(3, $msg_id);
                
                $msg_insert->execute();
            } catch(PDOException $e) {
                echo $e;
            }
            
            echo $grec_id;
        }
    }
}
?>
