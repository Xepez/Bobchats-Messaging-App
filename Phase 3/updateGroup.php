<?php
session_start();
?>

<html>
<head>
<title></title>
<meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
</head>
<body>
<form id='updateCat' action='updateCat.php' method='post' accept-charset='UTF-8'>
<fieldset>
<legend>Update a Category</legend>
<input type='hidden' name='submitted' id='submitted' value='1'/>
<label for='cat_name'>Enter The Current Category Name:</label>
<input type='text' name='cat_name' id='cat_name'maxlength="50"/>
<label for='new_cat_name'>Enter The New Category Name:</label>
<input type='text' name='new_cat_name' id='new_cat_name'maxlength="50"/>
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
    
    if(empty($_POST['cat_name']) || empty($_POST['new_cat_name'])) {
        echo "Category Name is empty!";
        return false;
    }
    
    $cat_name = trim($_POST['cat_name']);
    $new_cat_name= trim($_POST['new_cat_name']);
    
    // Updates Specific Category from database
    try {
        $cat_update = $pdo->prepare("UPDATE category SET c_name = ? WHERE c_name = ?");
        
        if (!$cat_update) {
            echo "Update Category Broke";
        }
        
        $cat_update->bindParam(1, $new_cat_name);
        $cat_update->bindParam(2, $cat_name);
        
        $cat_update->execute();
    } catch(PDOException $e) {
        echo $e;
    }
}
?>


