<?php
session_start();
?>

<html>
    <head>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <form id='update_user' action='updateInfo.php' method='post' accept-charset='UTF-8'>
            <fieldset>
                <legend>Update Info (Leave Empty To Not Change)</legend>
                <input type='hidden' name='submitted' id='submitted' value='1'/>
                <label for='firstname'>New First Name:</label>
                <input type='text' name='firstname' id='firstname'maxlength="50"/>
                <label for='lastname'>New Last Name:</label>
                <input type='text' name='lastname' id='lastname' maxlength="50"/>
                <input type='submit' name='Submit' value='Submit'/>
                <br>
                <input type='submit' name='home' value='Home'/>
            </fieldset>
        </form>
    </body>
</html>

<?php
// Makes sure our html has run
if(isset($_POST['Submit']) || isset($_POST['home'])) {
    include_once 'test_con.php';
    
    if (isset($_POST['home']))
        header('Location: home.php');
    
    // Connects to the database
    $pdo = connect();
    
    if(empty($_POST['firstname']) && empty($_POST['lastname'])) {
        echo "Nothing to Update";
        return false;
    }
    
    $user_id = $_SESSION['userID'];
    $new_firstname = $_SESSION['firstname'];
    $new_lastname = $_SESSION['lastname'];
    
    if ($_POST['firstname'] != null)
        $new_firstname = trim($_POST['firstname']);
    if ($_POST['lastname'] != null)
        $new_lastname = trim($_POST['lastname']);
    
    // Updates User Info in db
    try {
        $login_update = $pdo->prepare("UPDATE user SET first_name = ?, last_name = ? WHERE user_id = ?");
        
        if (!$login_update) {
            echo "User Update Broke";
        }
        
        $login_update->bindParam(1, $new_firstname);
        $login_update->bindParam(2, $new_lastname);
        $login_update->bindParam(3, $user_id);
        
        $login_update->execute();
    } catch(PDOException $e) {
        echo $e;
    }
    
    // Redirects User back to Message Page
    $_SESSION['firstname'] = $new_firstname;
    $_SESSION['lastname'] = $new_lastname;
    header('Location: home.php');
    
}
?>

