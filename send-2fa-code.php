<?php

// send 2 factor authentication to email

session_start();
include_once __DIR__ .'/Database.php';

if(!isset($_SESSION['userinfo']) || empty($_SESSION['userinfo'])){
    header('Location: ./views/login.php');
    exit;
}

// connect to database
$database = new Database('localhost', 'websecuritydb', 'root', '');
if(!$database->connect()){
    exit;
}

// get email
$email = $_SESSION['userinfo']['email'];
$username = $_SESSION['userinfo']['username'];


// generate code
$random_number = sprintf('%04d', rand(0, 9999));

// API endpoint URL
$url = "https://api.mailersend.com/v1/email";
// pass 1HRshf7PdT7uSARi
// Request body
$data = array(
    "from" => array(
        "email" => "MS_s4EWRz@trial-yzkq340oje04d796.mlsender.net",
        "name" => "2FA"
    ),
    "to" => array(
        array(
            "email" => $email,
            "name" => $username
        )
    ),
    "subject" => "2 Factor Authentication",
    "text" => "",
    "html" => "Your Code is: <h1>${random_number}</h1>",
);

// Convert data to JSON format
$data_json = json_encode($data);

// Authorization token
$authorization = "Bearer mlsn.bf079a3564aed6b455be9fa9effec9cb93fd3f959d1ee34de7bba7ae698074cd";

// Set up cURL
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: ' . $authorization
));

// Execute cURL request
$response = curl_exec($curl);

// Check for errors
if ($response === false) {
    $error = curl_error($curl);
    echo "Error: " . $error;
} else {
    curl_close($curl);

    // delete any existing code for the user
    $sql = 'DELETE FROM 2fa WHERE username= :username';
    $params = [':username'=>$username];
    Database::query($sql, $params);

    // make token valid for 30mins
    $expiry = date("Y-m-d H:i:s",time() + 60 * 30);

    // insert the code into the database
    $sql = 'INSERT INTO 2fa VALUES(:username, :code, :expire)';
    $params = [':username'=>$username, ':code'=>$random_number, ':expire'=>$expiry];
    Database::query($sql, $params);

    header('Location: ./views/2FA.php');
}







// mlsn.9edac534e24ab5994def44b9c818fe7f74842f56a918de72535a4fa8be7400e8