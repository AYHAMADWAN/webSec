<?php

// send password reset token to email

$username = $_POST['fp-user'];

$token = bin2hex(random_bytes(16));

// make token valid for 30mins
$expiry = date("Y-m-d H:i:s",time() + 60 * 30);

require __DIR__ . "/Database.php";

// connect to database
$database = new Database('localhost', 'websecuritydb', 'root', '');
if(!$database->connect()){
    exit;
}

// make sure user exists
$sql = 'SELECT * FROM users WHERE username=:username';
$params = [':username'=>$username];
$userResult = Database::query($sql, $params);

if(empty($userResult)){
    header('Location: ./views/forgot-pass.php?message=if+username+exits+an+email+has+been+sent');
    exit;
}

// make the query to add the token
$sql = 'INSERT INTO tokens (user, token, expiration) VALUES(:username, :token, :expiration)';
$params = [':username'=>$username, ':token'=>$token, ':expiration'=>$expiry];
$result = Database::query($sql, $params);

// send email
$email = $userResult[0]['email'];

$link = 'http://localhost/websecurity/views/reset-password.php?token=' . $token;

// API endpoint URL
$url = "https://api.mailersend.com/v1/email";

// Request body
$data = array(
    "from" => array(
        "email" => "MS_s4EWRz@trial-yzkq340oje04d796.mlsender.net",
        "name" => "App"
    ),
    "to" => array(
        array(
            "email" => $email,
            "name" => $username
        )
    ),
    "subject" => "Password Reset",
    "text" => "",
    "html" => "To Reset Your Password: <a href={$link}>Reset Password</a>",
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
    header('Location: ./views/forgot-pass.php?message=if+username+exits+an+email+has+been+sent');
}





// mlsn.9edac534e24ab5994def44b9c818fe7f74842f56a918de72535a4fa8be7400e8