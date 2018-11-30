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
            <label for='group_name'>Or Group to Message:</label>
            <input type='text' name='group_name' id='group_name' maxlength="50"/>
            <br> <br>
            <label for='subject'>Subject:</label>
            <textarea name="subject" form="message" rows="1" cols="20"></textarea>
            <br>
            <label for='attachment'>Attachment:</label>
            <input type="file" name="attachment" maxlength="50" allow="text/*">
            <br>
            <label for='messageBox'>Message:</label>
            <br>
            <textarea name="messageBox" form="message" rows="5" cols="60">Enter Message Here</textarea>
            <input type='submit' name='Submit' value='Submit'/>
            <br>
            <input type='submit' name='home' value='Home'/>
        </form>
    </body>
</html>

<?php
// Makes sure out html has run
if(isset($_POST['Submit']) || isset($_POST['home']) /*|| $_SESSION['reply'] == true*/) {
    include_once 'test_con.php';
    
    if (isset($_POST['home']))
        header('Location: home');

    // Catchs empty values
    if (($_POST['name'] == null || $_POST['name'] == '[Deleted] [Deleted]') && $_POST['group_name'] == null) {
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
    
    /*
    // The Parent Message if replying
    // Checks if there is a reply
    if ($_SESSION['reply']) {
        // Sets the parent message id
        $parent_msg_id = $_SESSION['reply_msg_id'];
        // Finds out if we are replying to a user or a group
        if ($_SESSION['reply_user_id'] != null)
            $rec_id = $_SESSION['reply_user_id'];
        elseif ($_SESSION['reply_group_id'] != null)
            $grec_id = $_SESSION['reply_group_id'];
        // Sets reply back to null so we don't accidentally call it again
        $_SESSION['reply'] = false;
    }
    */
    
    /*
     // ON REPLY CLICK
     $_SESSION['reply'] = true;
     $_SESSION['reply_msg_id'] = $msg_id;
     if ($group_recipient_id != null)
        $_SESSION['reply_group_id'] = $group_recipient_id;
     elseif($recipient_id != null)
         $_SESSION['reply_user_id'] = $creator_id;
     */

    // If there is an Attachment
    if ($_POST['attachment'] != null) {
        // Get our attachment's ID
        try {
            $msg_attach = $pdo->prepare("SELECT (MAX(attach_id) +1) FROM attachment");
            $msg_attach->execute();
            $attach_id = $msg_attach->fetchColumn();
        } catch(PDOException $e) {
            echo $e;
        }
        if (!$attach_id)
            $attach_id = 0;
        
        // Insert our attachment into the attachment table
        try {
            $attach_insert = $pdo->prepare('INSERT INTO attachment (attach_id, attach_msg) VALUES (:aids, :amsg)');
            
            if (!$attach_insert) {
                echo "Attachment Insert Broke!";
            }
            
            $attach_insert->bindParam(':aids', $attach_id);
            $attach_insert->bindParam(':amsg', $_POST['attachment']);
            
            $attach_insert->execute();
        } catch(PDOException $e) {
            echo $e;
        }
    }

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
    try {
        $msg_id_query = $pdo->prepare("SELECT (MAX(msg_id) +1) FROM message");
        $msg_id_query->execute();
        $msg_id = $msg_id_query->fetchColumn();
    } catch(PDOException $e) {
        echo $e;
    }
    if (!$msg_id)
        $msg_id = 0;
    
    // Drafts our message before sending it
    try {
        $msg_insert = $pdo->prepare('INSERT INTO message (msg_id, subject, message, creator_id, create_date, parent_msg_id, attach_id) VALUES (:mid, :sub, :msg, :uid, date("now"), :pmi, :ai)');
        
        if (!$msg_insert) {
            echo "Message Insert Broke!";
        }
        
        $msg_insert->bindParam(':mid', $msg_id);
        $msg_insert->bindParam(':sub', $subject);
        $msg_insert->bindParam(':msg', $msg);
        $msg_insert->bindParam(':uid', $user_id);
        $msg_insert->bindParam(':pmi', $parent_msg_id);
        $msg_insert->bindParam(':ai', $attach_id);
        
        $msg_insert->execute();
    } catch(PDOException $e) {
        echo $e;
    }
    
    // If we are sending msg to a individual user
    if ($rec_id != null) {
        
        if ($rec_id == $user_id) {
            echo "Can't Send to Self!";
            return false;
        }
        
        try {
            $msg_r = $pdo->prepare("SELECT MAX(msg_rec_id) + 1 FROM msg_recipient");
            $msg_r->execute();
            $msg_rec_id = $msg_r->fetchColumn();
        } catch(PDOException $e) {
            echo $e;
        }
        if (!$msg_rec_id)
            $msg_rec_id = 0;
        
        try {
            $msg_send = $pdo->prepare('INSERT INTO msg_recipient (msg_rec_id, recipient_id, recipient_group_id, msg_id) VALUES (:mr, :mri, :gri, :mid)');
            
            if (!$msg_send) {
                echo "Message Send Broke!";
            }
            
            $msg_send->bindParam(':mr', $msg_rec_id);
            $msg_send->bindParam(':mri', $rec_id);
            $msg_send->bindParam(':gri', $group_rec_id);
            $msg_send->bindParam(':mid', $msg_id);
            
            $msg_send->execute();
        } catch(PDOException $e) {
            echo $e;
        }
    }
        
    /// If we are sending msg to a group of users
    if ($group_rec_id != null) {
        // Finds each member in a group
        try {
            $group_query = $pdo->prepare("SELECT DISTINCT user_id FROM user_group WHERE group_id = ?");
            
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
            if ($grec_id == $user_id) {
                $grec_id = $group_query->fetchColumn();
                continue;
            }
            
            try {
                $msg_r = $pdo->prepare("SELECT MAX(msg_rec_id) + 1 FROM msg_recipient");
                $msg_r->execute();
                $msg_rec_id = $msg_r->fetchColumn();
            } catch(PDOException $e) {
                echo $e;
            }
            if (!$msg_rec_id)
                $msg_rec_id = 0;
            
            
            try {
                $msg_send = $pdo->prepare('INSERT INTO msg_recipient (msg_rec_id, recipient_id, recipient_group_id, msg_id) VALUES (:mr, :mri, :gri, :mid)');
                
                if (!$msg_send) {
                    echo "Message Send Broke!";
                }
                
                //echo " msg_rec_id: " . $msg_rec_id . " rec_id: " . $rec_id . " group_rec_id: " . $group_rec_id . " msg_id: " . $msg_id;
                
                $msg_send->bindParam(':mr', $msg_rec_id);
                $msg_send->bindParam(':mri', $grec_id);
                $msg_send->bindParam(':gri', $group_rec_id);
                $msg_send->bindParam(':mid', $msg_id);
                
                $msg_send->execute();
            } catch(PDOException $e) {
                echo $e;
            }
            
            $grec_id = $group_query->fetchColumn();
        }
    }
    echo "Sent!";
}
?>
