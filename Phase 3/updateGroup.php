<?php
session_start();
?>

<html>
    <head>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <form id='updateGroup' action='updateGroup.php' method='post' accept-charset='UTF-8'>
            <fieldset>
                <legend>Update a Group</legend>
                <input type='hidden' name='submitted' id='submitted' value='1'/>
                <label for='group_name'>Enter The Current Group Name:</label>
                <input type='text' name='group_name' id='group_name' maxlength="50"/>
                <label for='new_group_name'>Enter The New Group Name:</label>
                <input type='text' name='new_group_name' id='new_group_name' maxlength="50"/>
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
    
    if(empty($_POST['group_name']) || empty($_POST['new_group_name'])) {
        echo "Group Name is empty!";
        return false;
    }
    
    $group_name = trim($_POST['group_name']);
    $new_group_name = trim($_POST['new_group_name']);
    
    // Updates Specific Category from database
    try {
        $group_update = $pdo->prepare("UPDATE groups SET group_name = ? WHERE group_name = ?");
        
        if (!$group_update) {
            echo "Update Group Broke";
        }
        
        $group_update->bindParam(1, $new_group_name);
        $group_update->bindParam(2, $group_name);
        
        $group_update->execute();
    } catch(PDOException $e) {
        echo $e;
    }
}
?>


