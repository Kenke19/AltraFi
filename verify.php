<?php
session_start();
require 'config.php';

if (!isset($_GET['reference'])) {
    die('No transaction reference supplied');
}

if (!isset($_SESSION['user_id'])) {
    die('User not logged in');
}

$reference = $_GET['reference'];

// Verify transaction with Paystack
$ch = curl_init("https://api.paystack.co/transaction/verify/" . rawurlencode($reference));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
    "Cache-Control: no-cache"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);

if ($result === false) {
    die('Curl error: ' . curl_error($ch));
}

$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode !== 200) {
    die("Paystack API returned HTTP code $httpcode. Response: $result");
}

$response = json_decode($result, true);

if (!$response['status']) {
    die('Transaction verification failed: ' . $response['message']);
}

$transaction = $response['data'];

if ($transaction['status'] === 'success') {
    $amount_naira = $transaction['amount'] / 100;
    $user_id = $_SESSION['user_id'];
    $reference = $transaction['reference'];

    // Check if transaction already exists to avoid duplicates
    $stmt = $pdo->prepare("SELECT id FROM transactions WHERE reference = ?");
    $stmt->execute([$reference]);

    if ($stmt->rowCount() === 0) {
        // Insert transaction record
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, reference, amount, status, description, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $reference, $amount_naira, 'success', 'Wallet funding via Paystack']);
    }

    $_SESSION['send_success'] = "Wallet funded successfully with ₦" . number_format($amount_naira, 2);
    header("Location: dashboard.php");
    exit();
} else {
    echo "<h2>Payment Failed or Incomplete</h2>";
    echo "<p>Status: " . htmlspecialchars($transaction['status']) . "</p>";
    echo "<p><a href='dashboard.php'>Try again</a></p>";
}
