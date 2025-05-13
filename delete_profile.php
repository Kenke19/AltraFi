<?php
session_start();
require 'config.php'; // your PDO $pdo connection

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_profile'])) {
    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Delete user transactions
        $stmt = $pdo->prepare("DELETE FROM transactions WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Delete user profile photo file if stored locally (optional)
        $stmt = $pdo->prepare("SELECT profile_photo FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        if ($user && !empty($user['profile_photo']) && file_exists($user['profile_photo'])) {
            unlink($user['profile_photo']);
        }

        // Delete user record
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        // Commit transaction
        $pdo->commit();

        // Destroy session and logout
        session_unset();
        session_destroy();

        // Redirect to goodbye or homepage
        header('Location: goodbye.php');
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        // Log error or show message
        die("Error deleting profile: " . $e->getMessage());
    }
} else {
    header('Location: dashboard.php');
    exit;
}
