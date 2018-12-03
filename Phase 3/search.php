<?php
session_start();
?>

<html>
    <head>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <form id='search' action='messages.php' method='post' accept-charset='UTF-8'>
            <fieldset>
                <legend>Bobchats Login</legend>
                <label for='search_word'>Search For Messages with Specific Word:</label>
                <textarea name="search_word" form="message" rows="1" cols="20"></textarea>
                <label for='search_user'>Search For Messages By Specific User:</label>
                <textarea name="search_user" form="message" rows="1" cols="20"></textarea>
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
    
    if(empty($_POST['search_word']) && empty($_POST['search_user'])) {
        echo "The Both Boxes are empty!";
        return false;
    }
    
    $search_word = trim($_POST['search_word']);
    $search_user = trim($_POST['search_user']);
    
    if ($search_user == null) {
        // Search for Messages relating to specific key words
        try {
            $search_query = $pdo->prepare("SELECT message.create_date, sender.first_name, sender.last_name, subject, message
                                          FROM msg_recipient
                                          INNER JOIN message ON message.msg_id = msg_recipient.msg_id
                                          INNER JOIN user sender ON sender.user_id = message.creator_id
                                          WHERE recipient_id = ?
                                          AND message LIKE ?
                                          ORDER BY message.create_date DESC");
            
            if (!$search_query) {
                echo "Search Query Broke";
            }
            
            $search_query->bindParam(1, $search_word);
            
            $search_query->execute();
        } catch(PDOException $e) {
            echo $e;
        }
    }
    elseif ($search_word == null) {
        // Search for Messages by specific user
        $flname = explode(" ", $search_user);
        try {
        $search_query = $pdo->prepare("SELECT message.create_date, sender.first_name, sender.last_name, subject, message
                                    FROM msg_recipient
                                    INNER JOIN message ON message.msg_id = msg_recipient.msg_id
                                    INNER JOIN user sender ON sender.user_id = message.creator_id
                                    WHERE recipient_id = ?
                                      AND sender.first_name = ?
                                      AND sender.last_name = ?
                                    ORDER BY message.create_date DESC");
            
            if (!$search_query) {
                echo "Search Query Broke";
            }
            
            $search_query->bindParam(1, $flname[0]);
            $search_query->bindParam(2, $flname[1]);
            
            $search_query->execute();
        } catch(PDOException $e) {
            echo $e;
        }
    }
    else {
        // Search for Messages with specific words and a user
        try {
            $search_query = $pdo->prepare("SELECT message.create_date, sender.first_name, sender.last_name, subject, message
                                          FROM msg_recipient
                                          INNER JOIN message ON message.msg_id = msg_recipient.msg_id
                                          INNER JOIN user sender ON sender.user_id = message.creator_id
                                          WHERE recipient_id = ?
                                            AND sender.first_name = ?
                                            AND sender.last_name = ?
                                            AND message LIKE ?
                                          ORDER BY message.create_date DESC");
                                        
            if (!$search_query) {
            echo "Search Query Broke";
            }
            
            $search_query->bindParam(1, $flname[0]);
            $search_query->bindParam(2, $flname[1]);
            $search_query->bindParam(3, $search_word;
            
            $search_query->execute();
        } catch(PDOException $e) {
            echo $e;
        }
    }
}
?>
