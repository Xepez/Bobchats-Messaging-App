<?php
session_start();
?>

<html>
    <head>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <form id='home' action='home.php' method='post' accept-charset='UTF-8'>
            <fieldset>
                <legend>Category Manager</legend>
                <input type='hidden' name='submitted' id='submitted' value='1'/>
                <input type='submit' name='addCat' value='Add a Category'/>
                <br>
                <input type='submit' name='deleteCat' value='Delete a Category'/>
                <br>
                <input type='submit' name='updateCat' value='Update a Category'/>
                <br>
                <input type='submit' name='home' value='Home'/>
            </fieldset>
        </form>
    </body>
</html>

<?php
    // Makes sure our html has run
    if(isset($_POST['home'])) {
        header('Location: home.php');
    }
    if(isset($_POST['addCat'])) {
        header('Location: addCat.php');
    }
    if(isset($_POST['updateCat'])) {
        header('Location: updateCat.php');
    }
    if(isset($_POST['deleteCat'])) {
        header('Location: deleteCat.php');
    }
?>

