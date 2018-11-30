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
                <label for='lastname'> And Enter The Last Name:</label>
                <input type='text' name='lastname' id='lastname' maxlength="50"/>
                <label for='user_id'> Or Enter The ID:</label>
                <input type='text' name='user_id' id='user_id' maxlength="50"/>
                <input type='submit' name='Submit' value='Submit'/>
                <br>
                <input type='submit' name='home' value='Home'/>
            </fieldset>
        </form>
    </body>
</html>

<?php
// Makes sure our html has run
if(isset($_POST['Submit']) || isset($_POST['home'])) {
    include_once 'test_con.php';
    
    if (isset($_POST['home']))
        header('Location: home.php');
    
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
    
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    
    if ($firstname != null && $lastname != null) {
        try {
            $id_search = $pdo->prepare("SELECT user_id FROM user WHERE first_name = ? AND last_name = ?");
            
            if (!$id_search) {
                echo "User ID Query Broke";
            }
            
            $id_search->bindParam(1, $firstname);
            $id_search->bindParam(2, $lastname);
            
            $id_search->execute();
            $del_user_id = $id_search->fetchColumn();
        } catch(PDOException $e) {
            echo $e;
        }
    }
    else
        $del_user_id = trim($_POST['user_id']);
    
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
    
    // Redirects User back to Login Page
    header('Location: deleteUser.php');
    
    
    
}
?>
