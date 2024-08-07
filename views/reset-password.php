<?php

include_once '../Database.php';

if(!isset($_GET['token']) || empty($_GET['token'])){
    header('Location: localhost/websecurity');
    exit;
}

// connect to database
$database = new Database('localhost', 'websecuritydb', 'root', '');
if(!$database->connect()){
    exit;
}

// make the query
$sql = 'SELECT * FROM tokens WHERE token=:token';
$params = [':token'=>$_GET['token']];
$result = Database::query($sql, $params);

if(empty($result)){
    echo 'invalid token';
    exit;
}

// check expiration
$expiryDate = DateTime::createFromFormat("Y-m-d H:i:s", $result[0]['expiration']);
$current = new DateTime();

if($expiryDate < $current){
    echo "token expired";
    // delete the expired token
    $deleteRow = "DELETE FROM tokens WHERE token=:token";
    $params = [':token'=>$result[0]['token']];
    Database::query($deleteRow, $params);
    exit;
}

// update password
if(isset($_POST['newPass']) && !empty($_POST['newPass'])){
    $password = $_POST['newPass'];

    // Generate a random salt
    $salt = base64_encode(random_bytes(16));

    // Prefix the salt with the appropriate bcrypt identifier and cost parameter
    $salt = sprintf("$2y$%02d$", 10) . substr(str_replace('+', '.', base64_encode(random_bytes(16))), 0, 22);

    // Hash the password with the custom salt
    $hashed_password = crypt($password, $salt);

    // update pass
    $updatePass = 'UPDATE users SET password=:password WHERE username=:username';
    $params = [':password'=>$hashed_password, ':username'=>$result[0]['user']];
    Database::query($updatePass, $params);
    $message = "Successfully Updated!";

    // delete token
    $deleteRow = "DELETE FROM tokens WHERE token=:token";
    $params = [':token'=>$result[0]['token']];
    Database::query($deleteRow, $params);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel='stylesheet' href='./styles.css'>    
</head>
<body>
    <form action="" method="post">
        <div class="container">
            <h2>Reset Password</h2>
            <p><?= $message ?? null?></p>
            <input type="password" name="newPass" placeholder="New Password">
            <input type="submit" value="Reset">
    </div>
    </form>

</body>
</html>