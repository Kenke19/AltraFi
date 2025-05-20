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

    try {
        $pdo->beginTransaction();

        // Get transaction details with user balance
        $stmt = $pdo->prepare("
            SELECT t.*, u.balance, u.id AS user_id 
            FROM transactions t
            JOIN users u ON t.user_id = u.id 
            WHERE t.id = ? AND t.status = 'pending'
        ");
        $stmt->execute([$transaction_id]);
        $transaction = $stmt->fetch();

        if (!$transaction) {
            throw new Exception('Transaction not found or not actionable');
        }

        if ($action === 'approve') {
            // Approval logic
            $new_balance = $transaction['balance'] + $transaction['amount'];
            $update = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $update->execute([$new_balance, $transaction['user_id']]);
            
            $status = 'approved';
        } else {
            // Cancellation (reversal) logic
            $new_balance = $transaction['balance'] - $transaction['amount'];
            $update = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $update->execute([$new_balance, $transaction['user_id']]);
            
            // Create reversal audit record
            $stmt = $pdo->prepare("
                INSERT INTO transactions 
                (user_id, amount, description, status, reversal_ref_id, created_at)
                VALUES (?, ?, ?, 'reversed', ?, NOW())
            ");
            $stmt->execute([
                $transaction['user_id'],
                -$transaction['amount'],
                "Reversal of transaction #$transaction_id",
                $transaction_id
            ]);
            
            $status = 'cancelled';
        }

        // Update original transaction
        $update = $pdo->prepare("
            UPDATE transactions 
            SET status = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $update->execute([$status, $transaction_id]);

        $pdo->commit();
        header('Location: admin_dashboard.php');
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die('Error processing transaction: ' . $e->getMessage());
    }
}
