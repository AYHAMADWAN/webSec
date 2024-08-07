<?php
session_start();

if(!isset($_SESSION['userinfo']) || empty($_SESSION['userinfo']) || ($_SESSION['userinfo']['verified'] == 1 && $_SESSION['2FA'] != true)){
    header('Location: ./login.php');
    exit;
}

// create CSRF token
$_SESSION['csrf'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
    <link rel='stylesheet' href='./styles.css'>
</head>
<body>
<div class="container">
    <h2> You are a normal user! </h2>
    <p><b>username:</b> <?= $_SESSION['userinfo']['username'] ?></p>
    <p><b>email: </b><?= $_SESSION['userinfo']['email'] ?></p>

    <form action="../change-email.php" method="post">
        <input style="max-width: 250px;" type="email" name="newEmail" placeholder="New email">
        <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
        <button style="padding: 8px 20px;">Change email</button>
    </form>

    <br><hr>
    <?php
        // output your email is unverified if unverified
        if( $_SESSION['userinfo']['verified'] == 1){
            echo '<p style="color: green;">Your email is verified! ✅</p>';

        }else{
            echo '<p><span style="color:red;">⚠️Your email is unverified!⚠️<br></span> <a style="display:inline;" href="../send-verification.php">Verify Email</a> to use 2FA</p>';
        }

    ?>
    <hr>
    <form action="../process-upload.php" method="post" enctype="multipart/form-data">
        <p><b>Upload a picture</b></p>
        <input  type="file" id="fileUpload" name="fileUpload">
        <input style="width: 30%; padding: 6px;" type="submit" value="Submit">  
    </form>

    <br><br>

    <img style = "height: 100px; width: 100px; display:block; margin: 10px auto;" src="../images/<?= $_SESSION['userinfo']['image'] ?? 'default.png'?>">
    <p><a href="./blog.php">Blog!</a></p>

    <form action = "../logout.php">
        <input type="submit" value="logout">
    </form>

</div>
</body>
</html>