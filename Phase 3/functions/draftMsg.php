<?php
    include_once '/Users/re/Documents/CSE_111/Project/Phase 3/test_con.php';
    
    // Drafts a Message by inserting it into message
    function draftMsg(int $msg_id, string $subject, string $msg, int $user_id, int $parent_msg_id = null, int $attach_id = null){
        
        // Connects to the database
        $pdo = connect();
        
        echo "MsgID: " . $msg_id . " Subject: " . $subject . " Msg: " . $msg . " User ID: " . $user_id . " Rent Msg ID: " .  $parent_msg_id . " Attach ID: " . $attach_id;
        
        try {
            $msg_insert = $pdo->prepare("INSERT INTO message (msg_id, subject, message, creator_id, create_date, parent_msg_id, attach_id) VALUES (?, ?, ?, ?, date('now'), ?, ? )");
            
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
    }
?>
