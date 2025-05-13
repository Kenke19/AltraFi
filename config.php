<?php
// session_start();

$host = 'localhost';
$dbname = 'AltraFi';
$user = 'root';
$pass = '';

// PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

// Paystack secret key
define('PAYSTACK_SECRET_KEY', 'sk_test_accc9dcbc89207840a4600a269e0c47e1b53d122');
