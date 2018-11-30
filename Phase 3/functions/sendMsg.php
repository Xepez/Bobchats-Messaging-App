<?php
    include_once '/Users/re/Documents/CSE_111/Project/Phase 3/test_con.php';
    
    // Drafts a Message by inserting it into message
    function sendMsg(int $msg_rec_id, int $group_rec_id = null, int $msg_id){
        
        // Connects to the database
        $pdo = connect();
        
        try {
            $msg_send = $pdo->prepare("INSERT INTO msg_recipient (msg_rec_id, recipient_id, recipient_group_id, msg_id) VALUES ((SELECT MAX(msg_rec_id) + 1 FROM msg_recipient), ?, ?, ?)");
            
            if (!$msg_send) {
                echo "Message Send Broke!";
            }
            
            $msg_send->bindParam(2, $msg_rec_id);
            $msg_send->bindParam(3, $group_rec_id);
            $msg_send->bindParam(4, $msg_id);
            
            $msg_send->execute();
        } catch(PDOException $e) {
            echo $e;
        }
    }
?>
