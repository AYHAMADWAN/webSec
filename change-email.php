<?php
session_start();
if($_SESSION['csrf'] != $_POST['csrf']){
    echo '<h3>invalid CSRF token</h3>';
    exit;
}

include __DIR__ . '/Database.php';

$database = new Database('localhost', 'websecuritydb', 'root', '');
if(!$database->connect()){
    exit;
}
$sql = 'SELECT * FROM users WHERE email=:email';
$params = [':email'=>$_POST['newEmail']];
$result = Database::query($sql, $params);
if(!empty($result)){
    echo 'email already exists';
    exit;
}


$sql = 'UPDATE users SET email=:email, verified=0 WHERE username=:username';
$params = [':email'=>$_POST['newEmail'], ':username'=>$_SESSION['userinfo']['username']];
Database::query($sql, $params);

$_SESSION['userinfo']['email'] = $_POST['newEmail'];
$_SESSION['userinfo']['verified'] = 0;
header('Location: ./views/user.php');



?>