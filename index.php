<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - AltraFi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="./css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="floating"></div>
    <div class="floating"></div>
    
    <div class="login-container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="login.php" autocomplete="off">
            <h2>AltraFi</h2>
            <input type="text" name="username" placeholder="Username" required autocomplete="off">
            <div class="password-field">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class="far fa-eye" id="togglePassword" style="cursor: pointer;"></i>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', () => {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            togglePassword.classList.toggle('fa-eye-slash');
            togglePassword.classList.toggle('fa-eye');
        });
    </script>
</body>
</html>