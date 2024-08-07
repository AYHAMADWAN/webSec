<?php

session_start();
include_once '../Database.php';

if(!isset($_SESSION['userinfo']) || empty($_SESSION['userinfo'])){
    header('Location: ./ login.php');
    exit;
}

if(isset($_POST['code'])){
    
    // connect to database
    $database = new Database('localhost', 'websecuritydb', 'root', '');
    if(!$database->connect()){
        exit;
    }

    $sql = 'SELECT * FROM 2fa WHERE username = :username';
    $params = [':username'=>$_SESSION['userinfo']['username']];
    $result = Database::query($sql, $params);

    if(empty($result)){
        echo 'username not found?';
        exit;
    }
    $code = $result[0]['code'];

    // check expiration
    $expiryDate = DateTime::createFromFormat("Y-m-d H:i:s", $result[0]['expiration']);
    $current = new DateTime();

    if($expiryDate < $current){
        echo "code expired";
        // delete the expired token
        $deleteRow = "DELETE FROM 2fa WHERE username=:username";
        $params = [':username'=>$result[0]['username']];
        Database::query($deleteRow, $params);
        exit;
    }

    if($_POST['code'] == $code){
        // delete code
        $sql = 'DELETE FROM 2fa WHERE username= :username';
        $params = [':username'=>$_SESSION['userinfo']['username']];
        Database::query($sql, $params);

        // give access to user page
        $_SESSION['2FA'] = true;
        header('Location: ./user.php');
        exit;
    }
    else{
        header('Location: ./2FA.php?error=wrong code');
        exit;
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA</title>
    <link rel='stylesheet' href='./styles.css'>
</head>
<body>
    <div class="container">
        <h2>Enter 2FA Code</h2>
    <form method="post">
        <p><?= $_GET['error'] ?? null ?></p>
        <input type="text" name="code" placeholder="enter code">        
        <input type="submit" value="Continue">        
    </form>
    </div>
</body>
</html>