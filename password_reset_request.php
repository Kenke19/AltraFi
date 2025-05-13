<?php
session_start();
require 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Find user by email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate secure token
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Insert or update token in password_resets table
            $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?")->execute([$user['id']]);
            $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token, $expires_at]);

            // Send reset email
            $resetLink = "https://yourdomain.com/password_reset.php?token=$token";

            $subject = "Password Reset Request";
            $body = "Hello,\n\nClick the link below to reset your password (valid for 1 hour):\n\n$resetLink\n\nIf you did not request this, please ignore this email.";

            // Use your preferred mail function or library
            mail($email, $subject, $body);

            $message = "Password reset link sent to your email.";
        } else {
            $message = "If this email is registered, a reset link will be sent.";
        }
    } else {
        $message = "Please enter a valid email address.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Password Reset Request</title></head>
<body>
    <h2>Request Password Reset</h2>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Your email address" required>
        <button type="submit">Send Reset Link</button>
    </form>
</body>
</html>
