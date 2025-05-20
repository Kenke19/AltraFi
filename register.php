<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    if (!$first_name || !$last_name || !$username || !$email || !$phone || !$password || !$confirm_password) {
        $errors[] = "Please fill in all fields.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }
    if (!preg_match('/^0\d{10}$/', $phone)) {
    $errors[] = "Please enter a valid 11-digit phone number '0**********'";
    }
    if (strlen($password) < 7) {
        $errors[] = "Password must be at least 7 characters.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter.";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter.";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number.";
    }
    if (!preg_match('/[^\w]/', $password)) {
        $errors[] = "Password must contain at least one special character.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "Username or email already taken.";
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, username, email, phone, password) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$first_name, $last_name, $username, $email, $phone, $password])) {
            $_SESSION['success'] = "Registration successful! You can now log in.";
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - AltraFi</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="./css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="floating"></div>
    <div class="floating"></div>
<div class="login-container">
    <h2>Register</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php" autocomplete="off">
        <input type="text" name="first_name" placeholder="First Name" required value="<?= htmlspecialchars($first_name ?? '') ?>">
        <input type="text" name="last_name" placeholder="Last Name" required value="<?= htmlspecialchars($last_name ?? '') ?>">
        <input type="text" name="username" placeholder="Username" required value="<?= htmlspecialchars($username ?? '') ?>">
        <input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($email ?? '') ?>">
        <input type="tel" name="phone" placeholder="Phone Number " required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        <div class="password-field">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class="far fa-eye toggle-password" id="togglePassword" style="cursor: pointer;"></i>
        </div>
        <div class="password-field">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                <i class="far fa-eye toggle-password" id="togglePassword" style="cursor: pointer;"></i>
        </div>
        <button type="submit" class="login-btn">Register</button>
    </form>

    <p>Already have an account? <a href="index.php">Login here</a></p>
</div>

<script>
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', () => {
    
            const input = icon.closest('div').querySelector('input');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>

</body>
</html>
