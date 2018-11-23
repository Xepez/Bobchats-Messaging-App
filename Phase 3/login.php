<?php
//include 'connect.php';

if(empty($_POST['username'])) {
    $this->HandleError("UserName is empty!");
    return false;
}
if(empty($_POST['password'])) {
    $this->HandleError("Password is empty!");
    return false;
}
$username = trim($_POST['username']);
$password = trim($_POST['password']);

echo $username;
echo $password;    
?>
