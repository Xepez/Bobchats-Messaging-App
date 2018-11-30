<?php
session_start();
?>

<html>
    <head>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <form id='addGroup' action='addGroup.php' method='post' accept-charset='UTF-8'>
            <fieldset>
                <legend>Add a Group</legend>
                <input type='hidden' name='submitted' id='submitted' value='1'/>
                <label for='group_name'>Group Name:</label>
                <input type='text' name='group_name' id='group_name'maxlength="50"/>
                <label for='cat_name'>Enter a Category(Optional)</label>
                <input type='text' name='cat_name' id='cat_name'maxlength="50"/>
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
    
    $cat_name = trim($_POST['cat_name']);
    
    if ($_POST['cat_name'] != null) {
        try {
            $cid_query = $pdo->prepare("SELECT cat_id FROM category WHERE c_name = ?");
            
            $cid_query->bindParam(1, $cat_name);
            
            $cid_query->execute();
            $cat_id = $cid_query->fetchColumn();
        } catch(PDOException $e) {
            echo $e;
        }
        if ($cat_id == null) {
            echo "Not valid category";
            return false;
        }
    }
    else
        $cat_id = null;
    
    $group_name = trim($_POST['group_name']);
    
    // Gets New Group ID
    try {
        $id_query = $pdo->prepare("SELECT (MAX(group_id) + 1) FROM groups");
        $id_query->execute();
        $group_id = $id_query->fetchColumn();
    } catch(PDOException $e) {
        echo $e;
    }
    
    // Inserts Group to database
    try {
        $group_insert = $pdo->prepare('INSERT INTO groups (group_id, group_name, cat_id) VALUES (:gid, :gname, :cid)');
        
        if (!$group_insert) {
            echo "New Group Insert Broke";
        }
        
        $group_insert->bindParam(':gid', $group_id);
        $group_insert->bindParam(':gname', $group_name);
        $group_insert->bindParam(':cid', $cat_id);
        
        $group_insert->execute();
    } catch(PDOException $e) {
        echo $e;
    }
}
?>


