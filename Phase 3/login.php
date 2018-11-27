<?php
    include 'test_con.php';

    $pdo = connect();
    
    if(empty($_POST['firstname'])) {
        $this->HandleError("First Name is empty!");
        return false;
    }
    if(empty($_POST['lastname'])) {
        $this->HandleError("Last Name is empty!");
        return false;
    }
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    
    //echo $firstname;
    //echo $lastname;

    // First and Last Name Query
    try {
        $login_query = $pdo->prepare("SELECT user_id FROM user WHERE first_name = ? AND last_name = ?");
        $login_query->bindParam(1, $firstname);
        $login_query->bindParam(2, $lastname);
        $login_query->execute();
        $user_id = $login_query->fetchColumn();
    } catch(PDOException $e) {
        echo $e;
    }

    // Check Both Queries
    if ($user_id == null) {
        echo "No User With This Name";
        return false;
    }
    else {
        echo "WORKED!";
    }
    
?>
