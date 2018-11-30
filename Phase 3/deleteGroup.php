<?php
session_start();
?>

<html>
    <head>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <form id='deleteGroup' action='deleteGroup.php' method='post' accept-charset='UTF-8'>
            <fieldset>
                <legend>Delete a Group (Enter a Name or an ID)</legend>
                <input type='hidden' name='submitted' id='submitted' value='1'/>
                <label for='group_name'>Enter The Group Name:</label>
                <input type='text' name='group_name' id='group_name'maxlength="50"/>
                <label for='group_id'> Or Enter Its ID:</label>
                <input type='text' name='group_id' id='group_id' maxlength="50"/>
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
    
    if(empty($_POST['group_name']) && empty($_POST['group_id'])) {
        echo "Group Name and ID is empty!";
        return false;
    }
    
    $group_name = trim($_POST['group_name']);
    
    if ($group_name != null) {
        try {
            $id_search = $pdo->prepare("SELECT group_id FROM groups WHERE group_name = ?");
            
            if (!$id_search) {
                echo "Group ID Query Broke";
            }
            
            $id_search->bindParam(1, $group_name);
            
            $id_search->execute();
            $del_group_id = $id_search->fetchColumn();
        } catch(PDOException $e) {
            echo $e;
        }
    }
    else
        $del_group_id = trim($_POST['group_id']);
    
    if ($del_group_id != null) {
        // Deletes Group from database
        try {
            $group_rm = $pdo->prepare("DELETE FROM groups WHERE group_id = ?");
            
            if (!$group_rm) {
                echo "Group Remove Broke";
            }
            
            $group_rm->bindParam(1, $del_group_id);
            
            $group_rm->execute();
        } catch(PDOException $e) {
            echo $e;
        }
        
        try {
            $group_rm = $pdo->prepare("DELETE FROM user_group WHERE group_id = ?");
            
            if (!$group_rm) {
                echo "User Group Remove Broke";
            }
            
            $group_rm->bindParam(1, $del_group_id);
            
            $group_rm->execute();
        } catch(PDOException $e) {
            echo $e;
        }
    }
    echo "Deleted!";
}
?>


