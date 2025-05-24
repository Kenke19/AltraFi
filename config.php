<?php

$host = 'your_server';
$dbname = 'your_db_name';
$user = 'your_username';
$pass = 'your_password';

// PDO Connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

// Paystack 
define('PAYSTACK_SECRET_KEY', 'your-paystack-secret-key');
?>
