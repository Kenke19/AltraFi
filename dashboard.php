<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch user data including profile photo
$stmt = $pdo->prepare("SELECT first_name, last_name, profile_photo FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    // User not found, logout for safety
    session_destroy();
    header("Location: index.php");
    exit();
}

// Fetch transactions ordered by created_at descending
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$transactions = $stmt->fetchAll();
// chart 
// Fetch total income and total expenses
$stmt = $pdo->prepare("
    SELECT 
        SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) AS income,
        ABS(SUM(CASE WHEN amount < 0 THEN amount ELSE 0 END)) AS expenses
    FROM transactions
    WHERE user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$data = $stmt->fetch();

$income = floatval($data['income']);
$expenses = floatval($data['expenses']);



// Function to get user balance by summing all transactions
function getUserBalance($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT SUM(amount) as balance FROM transactions WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch();
    return $row['balance'] ?? 0;
}

$balance = getUserBalance($pdo, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AltraFi</title>
    <link rel="stylesheet" href="dash.css">
    <link rel="stylesheet" href="./css/all.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
</head>
<body>
<div class="sidebar-toggle">
    <i class="fa-solid fa-bars"></i>
</div>
<div class="left sidebar">
    <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="setup_profile.php"><i class="fa-solid fa-user"></i> Profile</a>
    <a href="faq.php"><i class="fa-solid fa-circle-question"></i> FAQs</a>
    <a href="contact.php"><i class="fa-solid fa-address-book"></i> Contact</a>
    <a href="Security.php"><i class="fa-solid fa-lock"></i> Security & Compliance</a>
    <a href="logout.php" class="logout-btn"><i class="fa-solid fa-door-open"></i> Logout</a>
</div>

<div class="dashboard-container">
    <!-- Header Section -->
    <header class="dashboard-header">
        <div class="profile-info">
            <?php if ($user['profile_photo']): ?>
                <img src="<?= htmlspecialchars($user['profile_photo']) ?>" alt="Profile Photo" class="dashboard-profile-photo">
            <?php else: ?>
                <div class="profile-placeholder"><i class="fa-solid fa-user"></i></div>
            <?php endif; ?>
            <h1>Welcome, <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>!</h1>
        </div>
    </header>

    <!-- Balance Overview -->
    <section class="balance-overview">
        <h2>Your Current Balance</h2>
        <div class="balance-card">
            <p>₦ <?= number_format($balance, 2) ?></p>
        </div>
    </section>
    
    <!-- Chart  -->
    <section class="expenditure-chart card">
    <h2>Income vs Expenses</h2>
    <canvas id="incomeExpensePieChart" width="400" height="400"></canvas>
</section>



    <!-- Fund wallet section -->
    <h2>Fund wallet</h2>
    <form action="pay.php" method="POST">
        <input type="number" name="amount" min="50" placeholder="Amount in Naira (min ₦50)" required>
        <button type="submit"><i class="fa-solid fa-money-bill-wave"></i>Fund via Paystack</button>
    </form>

    <!-- Send Money Section -->
    <section class="send-money-section">
        <h2>Send money to users</h2>
        <?php if (isset($_SESSION['send_error'])): ?>
            <div class="error"><?= $_SESSION['send_error']; unset($_SESSION['send_error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['send_success'])): ?>
            <div class="success"><?= $_SESSION['send_success']; unset($_SESSION['send_success']); ?></div>
        <?php endif; ?>
        <form action="send_money.php" method="POST" autocomplete="off">
            <input type="text" name="recipient" placeholder="Recipient Username" required>
            <input type="number" step="0.01" name="amount" placeholder="Amount" required min="0.01" max="<?= $balance ?>">
            <input type="text" name="code" placeholder="Enter 4-digit code" maxlength="4" pattern="\d{4}" title="Enter exactly 4 digits" required>
            <button type="submit"> <i class="fa-solid fa-paper-plane"></i>Send Money</button>
        </form>
    </section>
</div>

<!-- Transaction History Section -->
<section class="transaction-history">
    <h2>Transaction History</h2>
    <table class="transaction-table">
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Receipt</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($transactions) === 0): ?>
                <tr><td colspan="4" style="text-align:center;">No transactions found.</td></tr>
            <?php else: ?>
                <?php foreach ($transactions as $t): ?>
                <tr>
                    <td><?= date('Y-m-d H:i:s', strtotime($t['created_at'])) ?></td>
                    <td><?= $t['amount'] < 0 
                        ? '<span style="color:red;">-₦' . number_format(abs($t['amount']), 2) . '</span>' 
                        : '<span style="color:green;">₦' . number_format($t['amount'], 2) . '</span>' ?></td>
                    <td><?= htmlspecialchars($t['description'] ?? 'N/A') ?></td>
                    <td><a href="receipt.php?id=<?= $t['id'] ?>">View</a></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>
<script>
const ctx = document.getElementById('incomeExpensePieChart').getContext('2d');

const data = {
    labels: ['Income', 'Expenses'],
    datasets: [{
        data: [<?= $income ?>, <?= $expenses ?>],
        backgroundColor: [
            'green',  // Teal green for income
            'red'   // Pink/red for expenses
        ],
        borderColor: [
            'green',
            'red'
        ],
        borderWidth: 2,
        hoverOffset: 30
    }]
};

const options = {
    responsive: true,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                color: 'var(--light)',
                font: {
                    size: 16,
                    weight: '600'
                }
            }
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    const label = context.label || '';
                    const value = context.parsed || 0;
                    return `${label}: ₦${value.toLocaleString()}`;
                }
            }
        }
    }
};

const incomeExpensePieChart = new Chart(ctx, {
    type: 'pie',
    data: data,
    options: options
});
</script>
<script src="script.js"></script>


</body>
</html>