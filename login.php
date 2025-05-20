<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $_SESSION['error'] = "Please enter both username and password.";
        header("Location: index.php");
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && $password === $user['password']) {
        if ($user['is_deleted']) {
            $_SESSION['error'] = "This account has been deleted.";
            header("Location: index.php");
            exit();
        }
        if ($user['is_frozen']) {
            $_SESSION['error'] = "Your account is frozen. Please contact support.";
            header("Location: index.php");
            exit();
        }
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['is_admin'] = (bool)$user['is_admin'];

        if ($_SESSION['is_admin']) {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        $_SESSION['error'] = "Invalid credentials!";
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
