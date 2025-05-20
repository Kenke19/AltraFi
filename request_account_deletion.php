<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if already requested
$stmt = $pdo->prepare("SELECT id FROM account_deletion_requests WHERE user_id = ? AND status = 'pending'");
$stmt->execute([$user_id]);
if ($stmt->fetch()) {
    $_SESSION['message'] = "You already have a pending deletion request.";
    header('Location: goodbye.php');
    exit;
}

// Insert new request
$stmt = $pdo->prepare("INSERT INTO account_deletion_requests (user_id) VALUES (?)");
$stmt->execute([$user_id]);

$_SESSION['message'] = "Account deletion request submitted. Await admin approval.";
header('Location: goodbye.php');
exit;
