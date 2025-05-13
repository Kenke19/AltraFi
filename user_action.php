<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$user_id || !in_array($action, ['make_admin', 'soft_delete', 'toggle_freeze'])) {
        die('Invalid request');
    }

    if ($user_id == $_SESSION['user_id'] && in_array($action, ['soft_delete', 'toggle_freeze'])) {
        die('You cannot modify your own account status.');
    }

    try {
        switch ($action) {
            case 'make_admin':
                $pdo->prepare("UPDATE users SET is_admin = 1 WHERE id = ?")->execute([$user_id]);
                break;

            case 'soft_delete':
                $pdo->prepare("UPDATE users SET is_deleted = 1 WHERE id = ?")->execute([$user_id]);
                break;

            case 'toggle_freeze':
                $pdo->prepare("UPDATE users SET is_frozen = NOT is_frozen WHERE id = ?")->execute([$user_id]);
                break;
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }

    header('Location: admin_dashboard.php');
    exit;
}
