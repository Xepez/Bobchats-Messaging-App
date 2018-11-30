<?php
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
            <br> <br>
            <label for='subject'>Subject:</label>
            <textarea name="subject" form="message" rows="1" cols="20"></textarea>
            <br> <br>
            <label for='messageBox'>Message:</label>
            <br>
            <textarea name="messageBox" form="message" rows="5" cols="60">Enter Message Here</textarea>
            <input type='submit' name='Submit' value='Submit'/>
        </form>
    </body>
</html>

<?php
// Makes sure out html has run
if(isset($_POST['Submit'])) {
    include_once 'test_con.php';
    include_once 'functions/draftMsg.php';
    include_once 'functions/sendMsg.php';
    
    // Catchs empty values
    if ($_POST['name'] == null && $_POST['group_name'] == null) {
        echo "Enter a user to send the message to!";
        return false;
    }
    if ($_POST['messageBox'] == null) {
        echo "Enter a message!";
        return false;
    }
    if ($_POST['subject'] == null) {
        echo "Enter a subject!";
        return false;
    }
    
    // Connects to the database
    $pdo = connect();
    
    // Initializations
    // Our User ID
    $user_id = $_SESSION['userID'];
    // Reciever ID
    $rec_id = null;
    // Group Reciever ID
    $group_rec_id = null;
    // The Subject
    $subject = trim($_POST['subject']);
    // The Message to Send
    $msg = $_POST['messageBox'];
    // Parent Message ID
    $parent_msg_id = null;
    // Attach ID
    $attach_id = null;
    
    //----------------------------------------vTEMPv----------------------------------------
    $reply = false;
    $attach = null;
    $msg_id = null;
    
    // The Parent Message if replying
    if ($reply) {
        // TODO
        $parent_msg_id = null;
    }
    // If there is an Attachment
    if ($attach != null) {
        // TODO
        $attach_id = null;
    }
    //----------------------------------------^TEMP^----------------------------------------
    
    // Gets reciever's id and verifies they exist
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
        
        if ($rec_id == null) {
            echo "No User By This Name";
            return;
        }
    }
    
    // Get ID of group we are messaging
    if ($_POST['group_name'] != null) {
        $gname = trim($_POST['group_name']);
        
        // Get ID of group we are messaging
        try {
            $rec_query = $pdo->prepare("SELECT group_id FROM groups WHERE group_name = ?");
            
            if (!$rec_query){
                echo "Broke Trying to Find Group Name";
            }
            
            $rec_query->bindParam(1, $gname);
            $rec_query->execute();
            $group_rec_id = $rec_query->fetchColumn();
        } catch(PDOException $e) {
            echo $e;
        }
        
        if ($group_rec_id == null) {
            echo "No Group By This Name";
            return;
        }
    }
    
    // Our Current Message ID
    $msg_id = 0;
    try {
        $msg_id_query = $pdo->prepare("SELECT (MAX(msg_id) +1) FROM message");
        $msg_id_query->execute();
        $msg_id = $msg_id_query->fetchColumn();
    } catch(PDOException $e) {
        echo $e;
    }
    
    // Drafts our message before sending it
    //draftMsg($msg_id, $subject, $msg, $user_id, $parent_msg_id, $attach_id);
    try {
        $msg_insert = $pdo->prepare("INSERT INTO message (
                                    msg_id,
                                    subject,
                                    message,
                                    creator_id,
                                    create_date,
                                    parent_msg_id,
                                    attach_id
                                    ) VALUES (
                                              ?, ?, ?, ?,
                                              date('now'), ?, ? )"
        );
        
        if (!$msg_insert) {
            echo "Message Insert Broke!";
        }
        
        $msg_insert->bindParam(1, $msg_id);
        $msg_insert->bindParam(2, $subject);
        $msg_insert->bindParam(3, $msg);
        $msg_insert->bindParam(4, $user_id);
        $msg_insert->bindParam(6, $parent_msg_id);
        $msg_insert->bindParam(7, $attach_id);
        
        $msg_insert->execute();
    } catch(PDOException $e) {
        echo $e;
    }
    
    // If we are sending msg to a individual user
    if ($rec_id != null) {
        sendMsg($rec_id, null, $msg_id);
    }
        
    /// If we are sending msg to a group of users
    if ($group_rec_id != null) {
        // Finds each member in a group
        try {
            $group_query = $pdo->prepare("SELECT user_id FROM user_group WHERE group_id = ?");
            
            if (!$group_query) {
                echo "Broke Trying to Find All Members in a Group";
            }
            
            $group_query->bindParam(1, $group_rec_id);
            $group_query->execute();
        } catch(PDOException $e) {
            echo $e;
        }
        
        // A users id in a group
        $grec_id = $group_query->fetchColumn();
        
        // For each member in a group
        while($grec_id != null) {
            
            sendMsg($grec_id, $group_rec_id, $msg_id);
            
            $grec_id = $group_query->fetchColumn();
        }
    }
}
?>
