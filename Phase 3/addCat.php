<?php
session_start();
?>

<html>
    <head>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <form id='addCat' action='addCat.php' method='post' accept-charset='UTF-8'>
            <fieldset>
                <legend>Add a Category</legend>
                <input type='hidden' name='submitted' id='submitted' value='1'/>
                <label for='cat_name'>Category Name:</label>
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
        header('Location: category.php');
    
    // Connects to the database
    $pdo = connect();
    
    if(empty($_POST['cat_name'])) {
        echo "Category Name is empty!";
        return false;
    }
    
    $cat_name = trim($_POST['cat_name']);
    
    // Gets New Category ID
    try {
        $id_query = $pdo->prepare("SELECT (MAX(cat_id) + 1) FROM category");
        $id_query->execute();
        $cat_id = $id_query->fetchColumn();
    } catch(PDOException $e) {
        echo $e;
    }
    
    // Inserts Category to database
    try {
        $cat_insert = $pdo->prepare('INSERT INTO category (cat_id, c_name) VALUES (:cid, :cname)');
        
        if (!$cat_insert) {
            echo "New Category Insert Broke";
        }
        
        $cat_insert->bindParam(':cid', $cat_id);
        $cat_insert->bindParam(':cname', $cat_name);
        
        $cat_insert->execute();
    } catch(PDOException $e) {
        echo $e;
    }
    echo "Inserted!";
}
?>

