<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel='stylesheet' href='./styles.css'>
</head>
<body>
<div class="container">
    <form action="../send-token.php" method="post">
    <h2>Reset Password</h2>
    <p><?= $_GET['message'] ?? null?></p>
        <input type="text" name="fp-user" placeholder="Username">
        <input type="submit" value="Send Email">
    </form>
    <a href='./login.php'>Back to login</a>
</div>
</body>
</html>