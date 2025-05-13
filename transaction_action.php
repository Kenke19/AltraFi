<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_id = $_POST['transaction_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$transaction_id || !in_array($action, ['approve', 'cancel'])) {
        die('Invalid request');
    }

    // Check transaction exists and is pending
    $stmt = $pdo->prepare("SELECT status FROM transactions WHERE id = ?");
    $stmt->execute([$transaction_id]);
    $transaction = $stmt->fetch();

    if (!$transaction || $transaction['status'] !== 'pending') {
        die('Transaction not found or not pending');
    }

    $new_status = $action === 'approve' ? 'approved' : 'cancelled';

    // Update transaction status
    $update = $pdo->prepare("UPDATE transactions SET status = ? WHERE id = ?");
    $update->execute([$new_status, $transaction_id]);

    // Implement wallet balance update or notification on approval/cancellation

    header('Location: admin_dashboard.php');
    exit;
}
