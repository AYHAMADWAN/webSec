


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Page</title>
<link rel='stylesheet' href='./styles.css'>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <p><?= $_GET['error'] ?? null?></p>
        <form action="../process-login.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <div class="g-recaptcha" data-sitekey="6LekT6opAAAAADHeUYqrebQdD6obFC-yPs40RoUK" style="margin-bottom: 10px;"></div>
            <input type="submit" value="Login">
        </form>
        
        
        <a href='./signup.php'>Create an account</a>
        <a href='./forgot-pass.php'>Forgot password</a>
    </div>
    
</body>
</html>