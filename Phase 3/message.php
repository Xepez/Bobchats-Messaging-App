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
        <form id='message' action='message.php' method='post' accept-charset='UTF-8'>
            <legend>Bobchats Messaging</legend>
            <input type='hidden' name='submitted' id='submitted' value='1'/>
            <label for='name'>Who to Message:</label>
            <input type='text' name='name' id='name' maxlength="50"/>
            <br>
            <textarea name="messageBox" form="message">Enter Message Here</textarea>
            <input type='submit' name='Submit' value='Submit'/>
        </form>
    </body>
</html>

<?php
// Makes sure out html has run
if(isset($_POST['Submit'])) {
    // PDO
    $pdo = $_SESSION['PDO'];
    // Our User ID
    $user_id = $_SESSION['userID'];
    
    // Name of the user we are sending the message to
    $name = trim($_POST['name']);
    // 
    $flname = explode(" ", $name);
    
    echo $name;
    echo$_POST['messageBox'];

}
?>
