<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch user data including profile photo
$stmt = $pdo->prepare("SELECT first_name, last_name, profile_photo, last_login FROM users WHERE id = ?");
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

// pie chart 
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



// Balance
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
    <link rel="stylesheet" href="deep-dash.css">
    <link rel="stylesheet" href="./css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
</head>
<body>
<div class="app-container">
    <button class="sidebar-toggle">
        <i class="fa-solid fa-bars"></i>
    </button>
    <div class="sidebar">
        <div class="sidebar-header">
            <span class="sidebar-title">AltraFi</span>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="setup_profile.php"><i class="fa-solid fa-user"></i> Profile</a>
            <a href="faq.php"><i class="fa-solid fa-circle-question"></i> FAQs</a>
            <a href="contact.php"><i class="fa-solid fa-address-book"></i> Contact</a>
            <a href="Security.php"><i class="fa-solid fa-lock"></i> Security</a>
        </nav>
        <div class="sidebar-footer">
            <a href="logout.php" class="logout-btn"><i class="fa-solid fa-door-open"></i> Logout</a>
        </div>
    </div>

    <div class="dashboard-container">
        <!-- Header Section -->
        <header class="dashboard-header">
    <h1 class="header-title">Dashboard Overview</h1>
    <div class="profile-info">
        <?php if ($user['profile_photo']): ?>
            <img src="<?= htmlspecialchars($user['profile_photo']) ?>" alt="Profile Photo" class="dashboard-profile-photo">
        <?php else: ?>
            <div class="profile-placeholder"><i class="fa-solid fa-user"></i></div>
        <?php endif; ?>
        <div style="display:flex; flex-direction:column; margin-left:1rem;">
            <span style="font-weight:600; font-size:1.1em;">
                Welcome, <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
            </span>
            <span class="last-login" style="color:rgba(128, 128, 128, 0.5); font-size:0.98em; margin-top:0.2em;">
                <?php if (!empty($user['last_login'])): ?>
                    Last login: <?= date('F j, Y, g:i a', strtotime($user['last_login'])) ?>
                <?php else: ?>
                    Welcome! This is your first login.
                <?php endif; ?>
            </span>
        </div>
    </div>
</header>


        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Balance Overview -->
            <section class="balance-overview card">
                <div class="card-header">
                    <h2 class="card-title">Current Balance</h2>
                    <div class="card-icon">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
                <div class="card-content">
                    <div class="balance-amount">₦ <?= number_format($balance, 2) ?></div>
                    
                </div>
            </section>

            <!-- Chart Section -->
            <section class="expenditure-chart card">
                <div class="card-header">
                    <h2 class="card-title">Income vs Expenses</h2>
                    <div class="card-icon">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                </div>
                <div class="card-content">
                    <canvas id="incomeExpensePieChart" height="200"></canvas>
                </div>
            </section>
        </div>

        <!-- Cards -->
        <div class="dashboard-grid">
            <!-- Fund Wallet Section -->
            <section class="card form-card">
                <h2 class="card-title">Fund Wallet</h2>
                <form action="pay.php" method="POST">
                    <div class="form-group">
                        <input type="number" class="form-control" name="amount" min="50" placeholder="Amount in Naira (min ₦50)" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa-solid fa-money-bill-wave"></i> Fund via Paystack
                    </button>
                </form>
            </section>

            <!-- Send Money Section -->
            <section class="card form-card">
                <h2 class="card-title">Send Money</h2>
                <?php if (isset($_SESSION['send_error'])): ?>
                    <div class="alert alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?= $_SESSION['send_error']; unset($_SESSION['send_error']); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['send_success'])): ?>
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i>
                        <?= $_SESSION['send_success']; unset($_SESSION['send_success']); ?>
                    </div>
                <?php endif; ?>
                <form action="send_money.php" method="POST" autocomplete="off">
                    <div class="form-group">
                        <input type="text" class="form-control" name="recipient" placeholder="Recipient Username" required>
                    </div>
                    <div class="form-group">
                        <input type="number" class="form-control" step="0.01" name="amount" placeholder="Amount" required min="0.01" max="<?= $balance ?>">
                    </div>
                        <div class="password-field">
                            <input type="password" id="password" name="password" placeholder="Password" class="form-control" required>
                            <i class="far fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                        </div>
                    <!-- </div> -->
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa-solid fa-paper-plane"></i> Send Money
                    </button>
                </form>
            </section>
        </div>

        <!-- Transaction History Section -->
        <section class="transaction-card">
            <div class="card-header">
                <h2 class="card-title">Transaction History</h2>
                <div class="card-icon">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>
            <div class="card-content">
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
            </div>
        </section>
    </div>
</div>

<script>
    // Toggle password 
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', () => {
    const type = password.getAttribute('type') === 'password' ? 'text' :  'password';
    password.setAttribute('type', type);
    togglePassword.classList.toggle('fa-eye-slash');
    togglePassword.classList.toggle('fa-eye');
});
    // PIE CHART 
    const ctx = document.getElementById('incomeExpensePieChart').getContext('2d');

    const data = {
        labels: ['Income', 'Expenses'],
        datasets: [{
            data: [<?= $income ?>, <?= $expenses ?>],
            backgroundColor: [
                'green', 
                'red'   
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