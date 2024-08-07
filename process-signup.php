<?php

include __DIR__ . '/Database.php';

if(!isset($_POST['username']) || empty($_POST['username']) || !isset($_POST['password']) || empty($_POST['password']) ||
 !isset($_POST['confirm_password']) || empty($_POST['confirm_password']) || !isset($_POST['email']) || empty($_POST['email'])){
    header('Location: ./views/signup.php?error=fill+in+everything');
    exit;
}

// connect to database
$database = new Database('localhost', 'websecuritydb', 'root', '');
if(!$database->connect()){
    exit;
}

// make sure username isn't taken
$sql = 'SELECT * FROM users WHERE username=:username';
$params = [':username'=>$_POST['username']];
$username_result = Database::query($sql, $params);

if(!empty($username_result)){
    header('Location: ./views/signup.php?error=username+already+in+use');
    exit;
}

// make sure email isn't taken
$sql = 'SELECT * FROM users WHERE email=:email';
$params = [':email'=>$_POST['email']];
$email_result = Database::query($sql, $params);

if(!empty($email_result)){
    header('Location: ./views/signup.php?error=email+already+in+use');
    exit;
}

// check the complexity of the password
if(!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/", $_POST['password'])){
    header('Location: ./views/signup.php?error=password+should+contain+uppercase,+lowercase,+number+and+be+at+least+8+characters+long');    
    exit;
}

// make sure passwords match
if($_POST['password'] != $_POST['confirm_password']){
    header('Location: ./views/signup.php?error=passwords+don\'t+match');
    exit;
}

// The password to be hashed
$password = $_POST['password'];

// Generate a random salt
$salt = base64_encode(random_bytes(16));

// Prefix the salt with the appropriate bcrypt identifier and cost parameter
$salt = sprintf("$2y$%02d$", 10) . substr(str_replace('+', '.', base64_encode(random_bytes(16))), 0, 22);

// Hash the password with the custom salt
$hashed_password = crypt($password, $salt);



// insert the values into the database
$sql = 'INSERT INTO users(username, password, email) VALUES(:username, :password, :email)';
$params = [':username'=>$_POST['username'], ':email'=>$_POST['email'], ':password'=>$hashed_password];
Database::query($sql, $params);
header('Location: ./views/login.php');


// close database connection
$database->close();