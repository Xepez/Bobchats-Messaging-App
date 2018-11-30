<?php
session_start();
?>

<html>
    <head>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <form id='new_user' action='addUser.php' method='post' accept-charset='UTF-8'>
            <fieldset>
                <legend>Create Account</legend>
                <input type='hidden' name='submitted' id='submitted' value='1'/>
                <label for='firstname'>Enter Your First Name:</label>
                <input type='text' name='firstname' id='firstname'maxlength="50"/>
                <label for='lastname'>Enter Your Last Name:</label>
                <input type='text' name='lastname' id='lastname' maxlength="50"/>
                <input type='submit' name='Submit' value='Submit'/>
            </fieldset>
        </form>
    </body>
</html>

<?php
// Makes sure our html has run
if(isset($_POST['Submit'])) {
    include_once 'test_con.php';
    
    // Connects to the database
    $pdo = connect();

    if(empty($_POST['firstname'])) {
        echo "First Name is empty!";
        return false;
    }
    if(empty($_POST['lastname'])) {
        echo "Last Name is empty!";
        return false;
    }
    
    $user_id = 0;
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    
    // Gets New User ID
    try {
        $id_query = $pdo->prepare("SELECT (MAX(user_id) + 1) FROM user");
        $id_query->execute();
        $user_id = $id_query->fetchColumn();
    } catch(PDOException $e) {
        echo $e;
    }
    
    // Inserts User to database
    try {
        $login_insert = $pdo->prepare("INSERT INTO user (user_id, first_name, last_name) VALUES (?, ?, ?)");
        
        if (!$login_insert) {
            echo "New User Insert Broke";
        }
        
        $login_insert->bindParam(1, $user_id);
        $login_insert->bindParam(2, $firstname);
        $login_insert->bindParam(3, $lastname);
        
        $login_insert->execute();
    } catch(PDOException $e) {
        echo $e;
    }
    
    // Redirects User back to Login Page
    header('Location: login.php');
    
}
?>
