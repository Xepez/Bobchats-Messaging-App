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
                <label for='cat'>Enter A New Category for this (If Needed):</label>
                <input type='text' name='cat' id='cat' maxlength="50"/>
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
    
    if (empty($_POST['new_group_name']) && empty($_POST['cat'])) {
        echo "No New Information Entered!";
        return false;
    }
    
    $group_name = trim($_POST['group_name']);
    $new_group_name = trim($_POST['new_group_name']);
    $cat_name = trim($_POST['cat']);
    
    if ($cat_name != null) {
        try {
            $cat_query = $pdo->prepare("SELECT cat_id FROM category WHERE c_name = ?");
            
            if (!$cat_query) {
                echo "Select Cat in Group Broke";
            }
            
            $cat_query->bindParam(1, $cat_name);
            
            $cat_query->execute();
            $cat_id = $cat_query->fetchColumn();
        } catch(PDOException $e) {
            echo $e;
        }
        
        if ($cat_id == null) {
            echo "Not valid category name try again";
            return false;
        }
        
        // Updates Specific category from groups
        try {
            $group_update = $pdo->prepare("UPDATE groups SET cat_id = ? WHERE group_name = ?");
            
            if (!$group_update) {
                echo "Update Cat in Group Broke";
            }
            
            $group_update->bindParam(1, $cat_id);
            $group_update->bindParam(2, $group_name);

            $group_update->execute();
        } catch(PDOException $e) {
            echo $e;
        }
    }
    if ($new_group_name != null) {
        // Updates Specific group name from groups
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
    echo "Updated!";
}
?>


