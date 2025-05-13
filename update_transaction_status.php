<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    http_response_code(403);
    exit('Forbidden');
}

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $pdo->prepare("UPDATE transactions SET status = ? WHERE id = ?");
$stmt->execute([$data['status'], $data['transaction_id']]);

echo json_encode(['success' => true]);
