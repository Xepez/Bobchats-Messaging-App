<?php
session_start();
?>

<html>
    <head>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <form id='deleteCat' action='deleteCat.php' method='post' accept-charset='UTF-8'>
            <fieldset>
                <legend>Delete a Category (Enter a Name or an ID)</legend>
                <input type='hidden' name='submitted' id='submitted' value='1'/>
                <label for='cat_name'>Enter The Category Name:</label>
                <input type='text' name='cat_name' id='cat_name'maxlength="50"/>
                <label for='cat_id'> Or Enter Its ID:</label>
                <input type='text' name='cat_id' id='cat_id' maxlength="50"/>
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
        header('Location: category.php');
    
    // Connects to the database
    $pdo = connect();
    
    if(empty($_POST['cat_name']) && empty($_POST['cat_id'])) {
        echo "Category Name and ID is empty!";
        return false;
    }
    
    $cat_name = trim($_POST['cat_name']);
    
    if ($cat_name != null) {
        try {
            $id_search = $pdo->prepare("SELECT cat_id FROM category WHERE c_name = ?");
            
            if (!$id_search) {
                echo "Category ID Query Broke";
            }
            
            $id_search->bindParam(1, $cat_name);
            
            $id_search->execute();
            $del_cat_id = $id_search->fetchColumn();
        } catch(PDOException $e) {
            echo $e;
        }
    }
    else
        $del_cat_id = trim($_POST['cat_id']);
    
    if ($del_cat_id != null) {
        // Deletes Category from database
        try {
            $cat_rm = $pdo->prepare("DELETE FROM category WHERE cat_id = ?");
            
            if (!$cat_rm) {
                echo "Category Remove Broke";
            }
            
            $cat_rm->bindParam(1, $del_cat_id);
            
            $cat_rm->execute();
        } catch(PDOException $e) {
            echo $e;
        }
        // Deletes Category from groups with that category
        try {
            $cat_rm = $pdo->prepare("UPDATE groups SET cat_id = null WHERE cat_id = ?");
            
            if (!$cat_rm) {
                echo "Category Remove from Group Broke";
            }
            
            $cat_rm->bindParam(1, $del_cat_id);
            
            $cat_rm->execute();
        } catch(PDOException $e) {
            echo $e;
        }
    }
    echo "Deleted!";
}
?>

