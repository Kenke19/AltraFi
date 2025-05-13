<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $code = $_POST['code'];

    // Validate 4-digit numeric code
    if (strlen($code) !== 4 || !preg_match('/^\d{4}$/', $code)) {
        $_SESSION['error'] = "Please enter a valid 4-digit code.";
        header("Location: index.php");
        exit();
    }

    // Fetch user by username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($code, $user['password'])) {
        // Check if account is deleted
        if ($user['is_deleted']) {
            $_SESSION['error'] = "This account has been deleted.";
            header("Location: index.php");
            exit();
        }

        // Check if account is frozen
        if ($user['is_frozen']) {
            $_SESSION['error'] = "Your account is frozen. Please contact support.";
            header("Location: index.php");
            exit();
        }

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['is_admin'] = (bool)$user['is_admin']; // Add admin flag

        // Redirect based on role
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
