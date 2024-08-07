<?php
session_start();
include_once __DIR__ .'/Database.php';

// make sure everything is filled in
if(!isset($_POST['username']) || empty($_POST['username']) || !isset($_POST['password']) || empty($_POST['password'])){
    header('Location: ./views/login.php?error=fill+in+everything');
    exit;
}

// connect to database
$database = new Database('localhost', 'websecuritydb', 'root', '');
if(!$database->connect()){
    exit;
}

// hash the given password
$hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// make the query
$sql = 'SELECT * FROM users WHERE username=:username';
$params = [':username'=>$_POST['username']];
$result = Database::query($sql, $params);

// if user not found
if(empty($result)){
    header('Location: ./views/login.php?error=wrong+username+or+password');
    exit;
}


// Stored hashed password
$storedHashedPassword = $result[0]['password']; // Example stored hashed password

// User input password
$userPassword = $_POST['password']; // Example user input password

// Extract the salt from the stored hashed password
$salt = substr($storedHashedPassword, 0, 29); // Extracting the first 29 characters, which include the prefix, cost, and salt

// Hash the user input password with the extracted salt
$hashedPassword = crypt($userPassword, $salt);

// Compare the hashed user input password with the stored hashed password
if ($hashedPassword !== $storedHashedPassword) {
    header("Location: ./views/login.php?error=wrong+username+or+password");
    exit;
}


$_SESSION['userinfo'] = $result[0];

// RECAPTCHA

$response  = $_POST['g-recaptcha-response'] ; 
$mysecret = "6LekT6opAAAAAB2f126Qzo6lD438OywvWnUAjCOP" ;
$url = 'https://www.google.com/recaptcha/api/siteverify';
$data = ['secret'   => $mysecret,
        'response' => $response];
$options = [
            'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)                      
                ]
            ];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
    
    
$jsonArray = json_decode($result,true);
if(!$jsonArray['success']){
    header('Location: ./views/login.php?error=recaptcha');
    exit;
}

// view page based on role
if ($_SESSION['userinfo']['roleid'] == 1){
    if($_SESSION['userinfo']['verified'] == 1){
        header('Location: ./send-2fa-code.php');
        exit;
    }else{
        header('Location: ./views/user.php');
        exit;
    }
}
else if($_SESSION['userinfo']['roleid'] == 2){
    header('Location: ./views/admin.php');
    exit;
}

// close database connection
$database->close();