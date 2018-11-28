<?php
    // Starts Sessions so we can pass variables
    session_start();
?>

<html>
    <head>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <form id='login' action='login.php' method='post' accept-charset='UTF-8'>
            <fieldset>
                <legend>Bobchats Login</legend>
                <input type='hidden' name='submitted' id='submitted' value='1'/>
                <label for='firstname'>First Name:</label>
                <input type='text' name='firstname' id='firstname'maxlength="50"/>
                <label for='lastname'>Last Name:</label>
                <input type='text' name='lastname' id='lastname' maxlength="50"/>
                <input type='submit' name='Submit' value='Submit'/>
            </fieldset>
        </form>
    </body>
</html>

<?php
// Makes sure out html has run
if(isset($_POST['Submit'])) {
    include 'test_con.php';

    // Connects to the database
    $pdo = connect();
    
    if(empty($_POST['firstname'])) {
        echo "First Name is empty!";
        return false;
    }
    if(empty($_POST['lastname'])) {
        echo "Last Name is empty!";
        return false;
    }
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    
    //echo $firstname;
    //echo $lastname;

    // First and Last Name Query
    try {
        $login_query = $pdo->prepare("SELECT user_id FROM user WHERE first_name = ? AND last_name = ?");
        $login_query->bindParam(1, $firstname);
        $login_query->bindParam(2, $lastname);
        $login_query->execute();
        $user_id = $login_query->fetchColumn();
    } catch(PDOException $e) {
        echo $e;
    }
    
    // Check if there is a user with name that we entered
    if ($user_id == null) {
        echo "No User With This Name";
        return false;
    }
    else {
        // If so save the id and open the message system
        $_SESSION['userID'] = $user_id;
        header('Location: message.php');
    }
}
?>
