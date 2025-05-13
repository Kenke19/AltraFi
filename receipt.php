<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ? AND user_id = ?");
$stmt->execute([$_GET['id'], $_SESSION['user_id']]);
$transaction = $stmt->fetch();

if (!$transaction) {
    die("Transaction not found!");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaction Receipt - AltraFi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --accent: #00ffcc;
            --dark: #2b2d42;
            --light: #ffffff;
            --background: #f8f9fa;
            --error: #ef233c;
            --success: #4dff88;
            --border: #e9ecef;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', 'Roboto', sans-serif;
        }

        body {
            background-color: var(--background);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .receipt-container {
            background: var(--light);
            border-radius: 16px;
            width: 100%;
            max-width: 500px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(67, 97, 238, 0.1);
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .receipt-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(to right, var(--primary), var(--accent));
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .receipt-header h2 {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .receipt-header p {
            color: var(--secondary);
            font-weight: 500;
        }

        .receipt-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .detail-item {
            margin-bottom: 1rem;
        }

        .detail-label {
            font-size: 0.75rem;
            color: var(--secondary);
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .detail-value {
            font-size: 1rem;
            font-weight: 500;
            color: var(--dark);
        }

        .amount {
            grid-column: span 2;
            text-align: center;
            padding: 1.5rem;
            margin: 1rem 0;
            background: rgba(67, 97, 238, 0.05);
            border-radius: 10px;
            border-left: 4px solid var(--accent);
        }

        .amount .detail-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
        }

        .positive {
            color: var(--success) !important;
        }

        .negative {
            color: var(--error) !important;
        }

        .receipt-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            gap: 1rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        .btn-secondary {
            background: white;
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .btn-secondary:hover {
            background: rgba(67, 97, 238, 0.05);
        }

        .full-width {
            grid-column: span 2;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .receipt-container {
                box-shadow: none;
                border: none;
                max-width: 100%;
                padding: 1rem;
            }
            
            .btn {
                display: none !important;
            }
        }

        @media (max-width: 600px) {
            body {
                padding: 1rem;
            }
            
            .receipt-container {
                padding: 1.5rem;
            }
            
            .receipt-details {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .amount, .full-width {
                grid-column: span 1;
            }
            
            .receipt-footer {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h2>Transaction Receipt</h2>
            <p>AltraFi Financial Services</p>
        </div>
        
        <div class="receipt-details">
            <div class="detail-item">
                <div class="detail-label">Transaction ID</div>
                <div class="detail-value">#<?= $transaction['id'] ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Date & Time</div>
                <div class="detail-value"><?= date('M j, Y \a\t g:i A', strtotime($transaction['created_at'])) ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Transaction Type</div>
                <div class="detail-value"><?= $transaction['amount'] >= 0 ? 'Credit' : 'Debit' ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Status</div>
                <div class="detail-value">Completed</div>
            </div>
            
            <div class="amount">
                <div class="detail-label">Amount</div>
                <div class="detail-value <?= $transaction['amount'] >= 0 ? 'positive' : 'negative' ?>">
                    <?= ($transaction['amount'] >= 0 ? '+' : '') . '₦' . number_format($transaction['amount'], 2) ?>
                </div>
            </div>
            
            <div class="detail-item full-width">
                <div class="detail-label">Description</div>
                <div class="detail-value"><?= htmlspecialchars($transaction['description'] ?? 'N/A') ?></div>
            </div>
        </div>
        
        <div class="receipt-footer">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print Receipt
            </button>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</body>
</html>