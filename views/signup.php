<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Signup Page</title>
<link rel='stylesheet' href='./styles.css'>
</head>
<body>
    <div class="container">
        <h2>Signup</h2>
        <p><?= $_GET['error'] ?? null?></p>
        <form action="../process-signup.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <input type="submit" value="Signup">
        </form>
        <a href='./login.php'>Already have an account?</a>
    </div>
</body>
</html>