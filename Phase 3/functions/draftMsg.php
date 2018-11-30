<?php
    include_once '/Users/re/Documents/CSE_111/Project/Phase 3/test_con.php';
    
    // Drafts a Message by inserting it into message
    function draftMsg(int $msg_id, string $subject, string $msg, int $user_id, int $parent_msg_id = null, int $attach_id = null){
        
        // Connects to the database
        $pdo = connect();
        
        //echo "MsgID: " . $msg_id . " Subject: " . $subject . " Msg: " . $msg . " User ID: " . $user_id . " Rent Msg ID: " .  $parent_msg_id . " Attach ID: " . $attach_id;
        
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
    }
?>
