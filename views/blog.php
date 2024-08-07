
<?php
session_start();

if(!isset($_SESSION['userinfo']) || empty($_SESSION['userinfo']) || ($_SESSION['userinfo']['verified'] == 1 && $_SESSION['2FA'] != true)){
    header('Location: ./login.php');
    exit;
}

require __DIR__ . '/../Database.php';

// connect to db
$database = new Database('localhost', 'websecuritydb', 'root', '');
if(!$database->connect()){
    exit;
}

if(isset($_POST['post'])){
    $sql = "INSERT INTO posts(user, post) VALUES(:username, :post)";
    $params = ['username'=>$_SESSION['userinfo']['username'], ':post'=>$_POST['post']];
    Database::query($sql, $params);
    header('Location: ./blog.php');
    exit;
}

$sql = 'SELECT * FROM posts';
$results = Database::query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <link rel='stylesheet' href='./styles.css'>
</head>
<body>
    <style>
        img{
            width: 30px;
            height: 30px;
            display: inline;
        }
        .container{
            width: 90%;
            max-width: 100%;
            margin: 5px auto;
        } 
        form{
            width: 90%;
            max-width: 100%;
            margin: 10px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8); /* Transparent white background */
            border: solid black;
            border-radius: 5px;
        }
    </style>

<?php

foreach($results as $result){
    $sql2 = 'SELECT * FROM users WHERE username=:username';
    $params2 = [':username'=>$result['user']];
    $userResult = Database::query($sql2, $params2);

    $image = $userResult[0]['image'] ?? 'default.png';
    echo '<div class="container">';
    echo '<img src="../images/'. htmlspecialchars($image) . '">';
    echo htmlspecialchars($result['user']) . ':<br>';
    echo 'Post: ' . htmlspecialchars($result['post']) . '<hr><br>';
    echo '</div><br>';

}

?>


<form action="./blog.php" method="post">
    <h3>Post as <?= $_SESSION['userinfo']['username'] ?></h3>
    <textarea style="width: 100%; height: 200px;" placeholder="Make a Post!" name="post"></textarea><br>
    <input type="submit" value="POST">
</form>
<a href=./user.php>Return to user page..</a>
</body>
</html>



<!-- 
    token: mlsn.bf079a3564aed6b455be9fa9effec9cb93fd3f959d1ee34de7bba7ae698074cd
-->
