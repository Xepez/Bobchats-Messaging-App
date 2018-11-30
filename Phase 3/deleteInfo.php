<?php
    session_start();
    include_once 'test_con.php';

    // Connects to the database
    $pdo = connect();
    
    $del_user_id = $_SESSION['userID'];
        
    if ($del_user_id != null) {
        // "Deletes" User from database
        try {
            $login_delete = $pdo->prepare("UPDATE user SET first_name = '[Deleted]', last_name = '[Deleted]' WHERE user_id = ?");
            
            if (!$login_delete) {
                echo "Delete User Broke";
            }
            
            $login_delete->bindParam(1, $del_user_id);
            
            $login_delete->execute();
        } catch(PDOException $e) {
            echo $e;
        }
        
        // Removes from groups
        try {
            $group_rm = $pdo->prepare("DELETE FROM user_group WHERE user_id = ?");
            
            if (!$group_rm) {
                echo "Group Remove Broke";
            }
            
            $group_rm->bindParam(1, $del_user_id);
            
            $group_rm->execute();
        } catch(PDOException $e) {
            echo $e;
        }
    }
    echo "Your Account has been Deleted!";
?>

