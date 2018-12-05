<?php
session_start();
?>

<html>
    <head>
        <style>
            .illustration {
            width: 50%;
            length: 50%;
            }
        </style>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <?php
            include_once 'test_con.php';
            
            if ($_SESSION['view_attach_id'] == null) {
                echo "ERROR: No Attachment ID";
                return false;
            }
            
            $attach_id = $_SESSION['view_attach_id'];
            
            // Connects to the database
            $pdo = connect();
            
            try {
                $attach_search = $pdo->prepare("SELECT attach_msg FROM attachment WHERE attach_id = ?");
                
                $attach_search->bindParam(1, $attach_id);
                
                if (!$attach_search) {
                    echo "Attachment Query Broke";
                }
                
                $attach_search->execute();
                $test = $attach_search->fetchColumn();
                echo "<img class='illustration' src=" . $test . " />";
            } catch(PDOException $e) {
                echo $e;
            }
        ?>
        <br>
        <form id='return_home' action='testViewAttach.php' method='post' accept-charset='UTF-8'>
            <input type='submit' name='home' value='Home'/>
        </form>
    </body>
</html>

<?php
    if (isset($_POST['home']))
        header('Location: home.php');
?>
