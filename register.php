<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $code = $_POST['code'];
    $confirm_code = $_POST['confirm_code'];

    if (empty($first_name) || empty($last_name) || empty($username) || empty($code) || empty($confirm_code)) {
        $error = "Please fill in all fields.";
    } elseif (!preg_match('/^\d{4}$/', $code)) {
        $error = "Code must be exactly 4 digits.";
    } elseif ($code !== $confirm_code) {
        $error = "Codes do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username already taken.";
        } else {
            $hashed_code = password_hash($code, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, username, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $username, $hashed_code]);
            $_SESSION['success'] = "Registration successful! You can now log in.";
            header("Location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Finance App</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
<div class="login-container">
    <h2>AltraFi</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php" autocomplete="off">
        <input type="text" name="first_name" placeholder="First Name" required value="<?= isset($first_name) ? htmlspecialchars($first_name) : '' ?>">
        <input type="text" name="last_name" placeholder="Last Name" required value="<?= isset($last_name) ? htmlspecialchars($last_name) : '' ?>">
        <input type="text" name="username" placeholder="Username" required value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">
        <input type="text" name="code" placeholder="4-digit Code" maxlength="4" pattern="\d{4}" title="Enter exactly 4 digits" required>
        <input type="text" name="confirm_code" placeholder="Confirm 4-digit Code" maxlength="4" pattern="\d{4}" title="Enter exactly 4 digits" required>
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="index.php">Login here</a></p>
</div>
</body>
</html>
