<?php
session_start();
?>

<html>
    <head>
        <title></title>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
    </head>
    <body>
        <form id='search' action='confirmation.php' method='post' accept-charset='UTF-8'>
            <fieldset>
                <legend>ARE YOU SURE YOU WISH TO DELETE YOUR ACCOUNT?</legend>
                ---------------THIS CAN NOT BE UNDONE---------------
                <br>
                <input type='submit' name='yes' value='YES'/>
                <input type='submit' name='no' value='NO'/>
            </fieldset>
        </form>
    </body>
</html>

<?php
    if (isset($_POST['yes'])) {
        header('Location: deleteInfo.php');
    }
    if (isset($_POST['no'])) {
        header('Location: home.php');
    }
?>
