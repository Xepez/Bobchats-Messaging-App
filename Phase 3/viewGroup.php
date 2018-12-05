<html>
    <head>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <?php
            try {
                $group_query = $pdo->prepare("SELECT group_id, group_name FROM groups;");

                $messages_query->execute();
                
                $result = $messages_query->fetchAll();
            } catch(PDOException $e) {
                echo $e;
            }
                                             
            while() {
                  try {
                    $messages_query = $pdo->prepare("SELECT group_id, group_name FROM groups;");
                                                 
                    $messages_query->bindParam(1, $user_id);

                    /* execute statement */
                    $messages_query->execute();

                    $result = $messages_query->fetchAll();
                                                 
                    } catch(PDOException $e) {
                        echo $e;
                    }
            }
        ?>
    </body>
</html>
