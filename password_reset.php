<?php
session_start();
require 'config.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (!$token) {
    die('Invalid or missing token.');
}

// Check token validity
$stmt = $pdo->prepare("SELECT user_id, expires_at FROM password_resets WHERE token = ?");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset || strtotime($reset['expires_at']) < time()) {
    die('This reset link is invalid or expired.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $password_confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Update user password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$hashedPassword, $reset['user_id']]);

        // Delete used reset token
        $pdo->prepare("DELETE FROM password_resets WHERE token = ?")->execute([$token]);

        $success = 'Password reset successful. You can now <a href="login.php">login</a>.';
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Reset Password</title></head>
<body>
    <h2>Reset Your Password</h2>
    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($success): ?>
        <p style="color:green;"><?= $success ?></p>
    <?php else: ?>
        <form method="POST" action="">
            <input type="password" name="password" placeholder="New Password" required minlength="6">
            <input type="password" name="password_confirm" placeholder="Confirm New Password" required minlength="6">
            <button type="submit">Reset Password</button>
        </form>
    <?php endif; ?>
</body>
</html>
