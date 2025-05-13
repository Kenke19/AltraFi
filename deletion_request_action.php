<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$request_id || !in_array($action, ['approve', 'reject'])) {
        die('Invalid request');
    }

    // Fetch request info
    $stmt = $pdo->prepare("SELECT user_id, status FROM account_deletion_requests WHERE id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch();

    if (!$request || $request['status'] !== 'pending') {
        die('Request not found or already processed');
    }

    $new_status = $action === 'approve' ? 'approved' : 'rejected';

    // Begin transaction for atomicity
    $pdo->beginTransaction();

    try {
        // Update request status
        $update = $pdo->prepare("UPDATE account_deletion_requests SET status = ?, reviewed_at = NOW(), reviewed_by = ? WHERE id = ?");
        $update->execute([$new_status, $_SESSION['user_id'], $request_id]);

        if ($new_status === 'approved') {
            // Soft delete user (do NOT delete transactions)
            $pdo->prepare("UPDATE users SET is_deleted = 1 WHERE id = ?")->execute([$request['user_id']]);
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error processing request: " . $e->getMessage());
    }

    header('Location: admin_dashboard.php');
    exit;
}
