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
            
            // Connects to the database
            $pdo = connect();
            
            try {
                $attach_search = $pdo->prepare("SELECT attach_msg FROM attachment WHERE attach_id = 3");
                
                if (!$attach_search) {
                    echo "User ID Query Broke";
                }
                
                $attach_search->execute();
                $test = $attach_search->fetchColumn();
                echo "<img class='illustration' src = ../" . $test . " />";
            } catch(PDOException $e) {
                echo $e;
            }
        ?>
    </body>
</html>
