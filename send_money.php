<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $sender_id = $_SESSION['user_id'];
    $recipient_username = trim($_POST['recipient']);
    $amount = floatval($_POST['amount']);

    // Validate recipient username not empty
    if (empty($recipient_username)) {
        $_SESSION['send_error'] = "Please enter a recipient username.";
        header("Location: dashboard.php");
        exit();
    }

    // Validate amount
    if ($amount <= 0) {
        $_SESSION['send_error'] = "Amount must be greater than zero.";
        header("Location: dashboard.php");
        exit();
    }

    // Fetch user's hashed code (assuming code is password here)
    $stmt = $pdo->prepare("SELECT password, username FROM users WHERE id = ?");
    $stmt->execute([$sender_id]);
    $user = $stmt->fetch();

    if (!$user || $password !== $user['password']) {
        $_SESSION['send_error'] = "Invalid password";
        header("Location: dashboard.php");
        exit();
    }

    // Get sender balance
    $stmt = $pdo->prepare("SELECT SUM(amount) as balance FROM transactions WHERE user_id = ?");
    $stmt->execute([$sender_id]);
    $sender_balance = $stmt->fetch()['balance'] ?? 0;

    if ($amount > $sender_balance) {
        $_SESSION['send_error'] = "Insufficient balance.";
        header("Location: dashboard.php");
        exit();
    }

    // Find recipient by username
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$recipient_username]);
    $recipient = $stmt->fetch();

    if (!$recipient) {
        $_SESSION['send_error'] = "Recipient not found.";
        header("Location: dashboard.php");
        exit();
    }

    $recipient_id = $recipient['id'];

    if ($recipient_id == $sender_id) {
        $_SESSION['send_error'] = "You cannot send money to yourself.";
        header("Location: dashboard.php");
        exit();
    }

    // Begin transaction
    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, amount, description, created_at) VALUES (?, ?, ?, NOW())");

        // Deduct from sender (negative amount)
        $stmt->execute([$sender_id, -$amount, "Sent to $recipient_username"]);

        // Add to recipient (positive amount)
        $stmt->execute([$recipient_id, $amount, "Received from " . $user['username']]);

        $pdo->commit();

        $_SESSION['send_success'] = "Successfully sent ₦" . number_format($amount, 2) . " to $recipient_username.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['send_error'] = "Transaction failed: " . $e->getMessage();
    }

    header("Location: dashboard.php");
    exit();
}
?>
