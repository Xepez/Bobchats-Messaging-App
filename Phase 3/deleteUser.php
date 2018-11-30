<?php
session_start();
?>

<html>
    <head>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <form id='delete_user' action='deleteUser.php' method='post' accept-charset='UTF-8'>
            <fieldset>
                <legend>Delete an Account (Enter a Full Name or an ID)</legend>
                <input type='hidden' name='submitted' id='submitted' value='1'/>
                <label for='firstname'>Enter The First Name:</label>
                <input type='text' name='firstname' id='firstname'maxlength="50"/>
                <label for='lastname'>Enter The Last Name:</label>
                <input type='text' name='lastname' id='lastname' maxlength="50"/>
                <label for='user_id'>Enter The ID:</label>
                <input type='text' name='user_id' id='user_id' maxlength="50"/>
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
    
    if(empty($_POST['firstname']) && empty($_POST['user_id'])) {
        echo "First Name is empty!";
        return false;
    }
    if(empty($_POST['lastname']) && empty($_POST['user_id'])) {
        echo "Last Name is empty!";
        return false;
    }
    
    $del_user_id = trim($_POST['user_id']);
    $del_firstname = trim($_POST['firstname']);
    $del_lastname = trim($_POST['lastname']);
    
    // Delete User from database
    if (del_user_id == null) {
        try {
            $login_delete = $pdo->prepare('UPDATE user SET first_name = "[Deleted]", last_name = "[Deleted]" WHERE first_name = ? AND last_name = ?');
            
            if (!$login_delete) {
                echo "Delete User by Name Broke";
            }
            
            $login_delete->bindParam(1, $del_firstname);
            $login_delete->bindParam(2, $del_lastname);
            
            $login_delete->execute();
        } catch(PDOException $e) {
            echo $e;
        }
    }
    else {
        try {
            $login_delete = $pdo->prepare('UPDATE user SET first_name = "[Deleted]", last_name = "[Deleted]" WHERE user_id = ?');
            
            if (!$login_delete) {
                echo "Delete User by ID Broke";
            }
            
            $login_delete->bindParam(1, $del_user_id);
            
            $login_delete->execute();
        } catch(PDOException $e) {
            echo $e;
        }
    }
    
    // Redirects User back to Login Page
    header('Location: home.php');
    
}
?>
