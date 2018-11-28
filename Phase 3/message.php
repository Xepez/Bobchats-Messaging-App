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
            <legend>Bobchats Messaging (Enter an individual user and/or group to send your message to) </legend>
            <input type='hidden' name='submitted' id='submitted' value='1'/>
            <label for='name'>Individual to Message:</label>
            <input type='text' name='name' id='name' maxlength="50"/>
            <label for='group_name'>Group to Message:</label>
            <input type='text' name='group_name' id='group_name' maxlength="50"/>
            <br>
            <textarea name="messageBox" form="message">Enter Message Here</textarea>
            <input type='submit' name='Submit' value='Submit'/>
        </form>
    </body>
</html>

<?php
// Makes sure out html has run
if(isset($_POST['Submit'])) {
    include 'test_con.php';
    
    // Connects to the database
    $pdo = connect();
    // Our User ID
    $user_id = $_SESSION['userID'];
    
    // Catchs empty values
    if ($_POST['name'] == null && $_POST['group_name']) {
        echo "Enter a user to send the message to!";
        return false;
    }
    if ($_POST['messageBox'] == null) {
        echo "Enter a message!";
        return false;
    }
    
    if ($_POST['name'] != null) {
        // Seperates $name = 0 - First name / 1 - Last name
        $flname = explode(" ", $_POST['name']);
        
        // Get ID of user we are messaging
        try {
            $rec_query = $pdo->prepare("SELECT user_id FROM user WHERE first_name = ? AND last_name = ?");
            $rec_query->bindParam(1, $flname[0]);
            $rec_query->bindParam(2, $flname[1]);
            $rec_query->execute();
            $reciever_id = $rec_query->fetchColumn();
        } catch(PDOException $e) {
            echo $e;
        }
        
        echo $reciever_id;
    }
    
    if ($_POST['group_name'] != null) {
        $gname = trim($_POST['group_name']);
        
        // Get ID of user we are messaging
        try {
            $rec_query = $pdo->prepare("SELECT group_id FROM group WHERE group_name = ?");
            $rec_query->bindParam(1, $gname);
            $rec_query->execute();
            $group_reciever_id = $rec_query->fetchColumn();
        } catch(PDOException $e) {
            echo $e;
        }
        
        echo $group_reciever_id;
    }

}
?>
