<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
}

$amount = (int)$_POST['amount'];

if ($amount < 50) {
    die("Minimum funding amount is ₦50");
}

$amount_kobo = $amount * 100; // convert to kobo

$callback_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/verify.php";

$fields = [
    'email' => 'customer@example.com', // Ideally, use user's email if available
    'amount' => $amount_kobo,
    'callback_url' => $callback_url,
    'metadata' => json_encode(['user_id' => $_SESSION['user_id']])
];

$fields_string = http_build_query($fields);

$ch = curl_init('https://api.paystack.co/transaction/initialize');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
    "Cache-Control: no-cache"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);

if ($result === false) {
    die('Curl error: ' . curl_error($ch));
}

curl_close($ch);

$response = json_decode($result, true);

if (!$response['status']) {
    die('API error: ' . $response['message']);
}

// Redirect user to Paystack payment page
header('Location: ' . $response['data']['authorization_url']);
exit();
