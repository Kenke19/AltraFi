<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - AltraFi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="login-container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="login.php" autocomplete="off">
            <h2>AltraFi</h2>
            <input type="text" name="username" placeholder="Username" required autocomplete="off">
            <input type="password" name="code" placeholder="Enter 4-digit code" maxlength="4" pattern="\d{4}" title="Please enter exactly 4 digits" required>
            <button type="submit" class="login-btn">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>