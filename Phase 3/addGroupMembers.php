<?php
session_start();
?>

<html>
    <head>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <form id='addGroupMembers' action='addGroupMembers.php' method='post' accept-charset='UTF-8'>
            <fieldset>
                <legend>Add a Group Member to an Exsisting Group</legend>
                <input type='hidden' name='submitted' id='submitted' value='1'/>
                <label for='group_name'>Group Name:</label>
                <input type='text' name='group_name' id='group_name'maxlength="50"/>
                <label for='user_name'>Enter an User:</label>
                <input type='text' name='user_name' id='user_name'maxlength="50"/>
                <input type='submit' name='Submit' value='Submit'/>
                <br>
                <input type='submit' name='back' value='Back'/>
                <input type='submit' name='home' value='Home'/>
            </fieldset>
        </form>
    </body>
</html>

<?php
// Makes sure our html has run
if(isset($_POST['Submit']) || isset($_POST['home']) || isset($_POST['back'])) {
    include_once 'test_con.php';
    
    if (isset($_POST['home']))
        header('Location: home.php');
    if (isset($_POST['back']))
        header('Location: groups.php');
    
    // Connects to the database
    $pdo = connect();
    
    if(empty($_POST['group_name'])) {
        echo "Group Name is empty!";
        return false;
    }
    if(empty($_POST['user_name'])) {
        echo "User Name is empty!";
        return false;
    }
    
    // Get ID of user we are messaging
    $flname = explode(" ", $_POST['user_name']);
    try {
        $uid_query = $pdo->prepare("SELECT user_id FROM user WHERE first_name = ? AND last_name = ?");
        $uid_query->bindParam(1, $flname[0]);
        $uid_query->bindParam(2, $flname[1]);
        $uid_query->execute();
        $user_id = $uid_query->fetchColumn();
    } catch(PDOException $e) {
        echo $e;
    }
    if ($user_id == null) {
        echo "No User By This Name";
        return false;
    }
    
    $group_name = trim($_POST['group_name']);
    try {
        $gid_query = $pdo->prepare("SELECT group_id FROM groups WHERE group_name = ?");
        $gid_query->bindParam(1, $group_name);
        $gid_query->execute();
        $group_id = $gid_query->fetchColumn();
    } catch(PDOException $e) {
        echo $e;
    }
    if ($group_id == null) {
        echo "No Group By This Name";
        return false;
    }
    
    // Gets New User Group ID
    try {
        $id_query = $pdo->prepare("SELECT (MAX(user_group_id) + 1) FROM user_group");
        $id_query->execute();
        $user_group_id = $id_query->fetchColumn();
    } catch(PDOException $e) {
        echo $e;
    }
    
    // Inserts user to user group table
    try {
        $user_group_insert = $pdo->prepare('INSERT INTO user_group (group_id, user_group_id, user_id) VALUES (:gid, :ugid, :uid)');
        
        if (!$user_group_insert) {
            echo "New User Group Insert Broke";
        }
        
        $user_group_insert->bindParam(':gid', $group_id);
        $user_group_insert->bindParam(':ugid', $user_group_id);
        $user_group_insert->bindParam(':uid', $user_id);
        
        $user_group_insert->execute();
    } catch(PDOException $e) {
        echo $e;
    }
    echo "Inserted!";
}
?>
