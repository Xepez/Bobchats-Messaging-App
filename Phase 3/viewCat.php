<?php
session_start();
?>

<html>
    <head>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <form id='return_home' action='viewAttach.php' method='post' accept-charset='UTF-8'>
            <input type='submit' name='home' value='Home'/>
        </form>
        <br>
        Categories:
        <br>
        <?php
        include_once 'test_con.php';

        // Connects to the database
        $pdo = connect();

        try {
            $cat_query = $pdo->prepare("SELECT c_name FROM category ORDER BY cat_id ASC;");
            
            $cat_query->execute();
            
            $result = $cat_query->fetchAll();
        } catch(PDOException $e) {
            echo $e;
        }

        $count = 0;
            
        foreach($result as $cat) {
            echo ($count++) . ". " . $cat["c_name"] . "<br> ---------------------------------------------------- <br>";
        }
        ?>
    </body>
</html>

<?php
    if (isset($_POST['home']))
        header('Location: home.php');
?>

