<?php
session_start();
?>

<html>
    <head>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <form id='groups' action='groups.php' method='post' accept-charset='UTF-8'>
            <fieldset>
                <legend>Group Manager</legend>
                <input type='hidden' name='submitted' id='submitted' value='1'/>
                <input type='submit' name='addGroup' value='Add a Group'/>
                <br>
                <input type='submit' name='deleteGroup' value='Delete a Group'/>
                <br>
                <input type='submit' name='updateGroup' value='Update a Group'/>
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
if(isset($_POST['addGroup'])) {
    header('Location: addGroup.php');
}
if(isset($_POST['updateGroup'])) {
    header('Location: updateGroup.php');
}
if(isset($_POST['deleteGroup'])) {
    header('Location: deleteGroup.php');
}
?>


