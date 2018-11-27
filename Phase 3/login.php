<?php
    include 'test_con.php';

    connect();
    
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

    // First Name Query
    $username_query = $test_pdo->prepare("SELECT first_name FROM user WHERE first_name = ?");
    $username_query->bindParam(1, $firstname);
    $username_query->execute();
    $first = $username_query->fetchColumn();
    
    // Last Name Query
    $username_query = $test_pdo->prepare("SELECT first_name FROM user WHERE first_name = ?");
    $username_query->bindParam(1, $firstname);
    $username_query->execute();
    $first = $username_query->fetchColumn();
    
    // Check Both Queries
    if (first == null || last == null) {
        $this->HandleError("No User With This Name");
        return false
    }
    else {
        echo "WORKED!";
    }
    
?>
