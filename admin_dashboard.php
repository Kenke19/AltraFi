<?php
session_start();
require 'config.php';

// Admin access check
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: dashboard.php');
    exit;
}

// Fetch users
$users = $pdo->query("
    SELECT id, username, first_name, last_name, email, phone, is_admin, is_deleted, is_frozen 
    FROM users 
    ORDER BY id ASC
")->fetchAll();

// Fetch transactions with user info
$transactions = $pdo->query("
    SELECT t.id, t.user_id, t.reference, t.amount, t.description, t.status, t.created_at,
            u.username, u.first_name, u.last_name
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
    LIMIT 50
")->fetchAll();

// Fetch pending account deletion requests
$deletionRequests = $pdo->query("
    SELECT r.id, r.user_id, r.requested_at, r.status, u.username, u.first_name, u.last_name, u.email
    FROM account_deletion_requests r
    JOIN users u ON r.user_id = u.id
    WHERE r.status = 'pending'
    ORDER BY r.requested_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        :root {
            --primary: #4a6bff;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray: #6c757d;
            --border: #dee2e6;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        body {
            background-color: #f5f7fb;
            color: #333;
            line-height: 1.6;
            padding: 0;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: white;
            padding: 15px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        h1 {
            font-size: 1.8rem;
            color: var(--dark);
            font-weight: 600;
        }
        
        h2 {
            font-size: 1.4rem;
            color: var(--dark);
            margin: 25px 0 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border);
        }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: var(--dark);
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        
        tr:hover {
            background-color: #f8fafd;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-primary { background: var(--primary); color: white; }
        .badge-success { background: var(--success); color: white; }
        .badge-warning { background: var(--warning); color: var(--dark); }
        .badge-danger { background: var(--danger); color: white; }
        .badge-info { background: var(--info); color: white; }
        .badge-light { background: var(--light); color: var(--dark); }
        
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        
        .btn-sm {
            padding: 4px 8px;
            font-size: 0.8rem;
        }
        
        .btn-primary { background: var(--primary); color: white; }
        .btn-success { background: var(--success); color: white; }
        .btn-warning { background: var(--warning); color: var(--dark); }
        .btn-danger { background: var(--danger); color: white; }
        .btn-info { background: var(--info); color: white; }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        .btn + .btn {
            margin-left: 6px;
        }
        
        .text-muted {
            color: var(--gray);
            font-style: italic;
        }
        
        .empty-state {
            padding: 20px;
            text-align: center;
            color: var(--gray);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .stat-card h3 {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 10px;
        }
        
        .stat-card p {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            th, td {
                padding: 8px 10px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <div>
            <a href="dashboard.php" class="btn btn-primary">User View</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </header>
    
    <div class="container">
        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <p><?= count($users) ?></p>
            </div>
            <div class="stat-card">
                <h3>Active Users</h3>
                <p><?= count(array_filter($users, fn($u) => !$u['is_deleted'] && !$u['is_frozen'])) ?></p>
            </div>
            <div class="stat-card">
                <h3>Pending Transactions</h3>
                <p><?= count(array_filter($transactions, fn($t) => $t['status'] === 'pending')) ?></p>
            </div>
            <div class="stat-card">
                <h3>Deletion Requests</h3>
                <p><?= count($deletionRequests) ?></p>
            </div>
        </div>
        
        <!-- Recent Transactions -->
        <h2>Recent Transactions</h2>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="7" class="empty-state">No transactions found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $t): ?>
                        <tr>
                            <td><?= htmlspecialchars($t['id']) ?></td>
                            <td>
                                <?= htmlspecialchars($t['first_name'] . ' ' . $t['last_name']) ?>
                                <div class="text-muted">@<?= htmlspecialchars($t['username']) ?></div>
                            </td>
                            <td>₦<?= number_format($t['amount'], 2) ?></td>
                            <td><?= htmlspecialchars($t['description']) ?></td>
                            <td>
                                <span class="badge badge-<?= 
                                    $t['status'] === 'completed' ? 'success' : 
                                    ($t['status'] === 'pending' ? 'warning' : 'danger')
                                ?>">
                                    <?= ucfirst(htmlspecialchars($t['status'])) ?>
                                </span>
                            </td>
                            <td><?= date('M j, Y', strtotime($t['created_at'])) ?></td>
                            <td>
                                <?php if ($t['status'] === 'pending'): ?>
                                    <form method="POST" action="transaction_action.php" style="display:inline;">
                                        <input type="hidden" name="transaction_id" value="<?= $t['id'] ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                    <form method="POST" action="transaction_action.php" style="display:inline;">
                                        <input type="hidden" name="transaction_id" value="<?= $t['id'] ?>">
                                        <input type="hidden" name="action" value="cancel">
                                        <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">No actions</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- User Management -->
        <h2>User Management</h2>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['id']) ?></td>
                        <td>
                            <?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?>
                            <div class="text-muted">@<?= htmlspecialchars($u['username']) ?></div>
                        </td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <?php if ($u['is_deleted']): ?>
                                <span class="badge badge-light">Deleted</span>
                            <?php elseif ($u['is_frozen']): ?>
                                <span class="badge badge-warning">Frozen</span>
                            <?php else: ?>
                                <span class="badge badge-success">Active</span>
                            <?php endif; ?>
                            <?php if ($u['is_admin']): ?>
                                <span class="badge badge-primary">Admin</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                <?php if (!$u['is_deleted']): ?>
                                    <?php if (!$u['is_admin']): ?>
                                        <form method="POST" action="user_action.php" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <input type="hidden" name="action" value="toggle_freeze">
                                            <button type="submit" class="btn btn-<?= $u['is_frozen'] ? 'info' : 'warning' ?> btn-sm">
                                                <?= $u['is_frozen'] ? 'Unfreeze' : 'Freeze' ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <form method="POST" action="user_action.php" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <input type="hidden" name="action" value="soft_delete">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    
                                    <?php if (!$u['is_admin']): ?>
                                        <form method="POST" action="user_action.php" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <input type="hidden" name="action" value="make_admin">
                                            <button type="submit" class="btn btn-primary btn-sm">Make Admin</button>
                                        </form>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">No actions</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Deletion Requests -->
        <h2>Account Deletion Requests</h2>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>User</th>
                        <th>Requested At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($deletionRequests)): ?>
                        <tr>
                            <td colspan="4" class="empty-state">No pending deletion requests</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($deletionRequests as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['id']) ?></td>
                            <td>
                                <?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?>
                                <div class="text-muted"><?= htmlspecialchars($r['email']) ?></div>
                            </td>
                            <td><?= date('M j, Y', strtotime($r['requested_at'])) ?></td>
                            <td>
                                <form method="POST" action="deletion_request_action.php" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <form method="POST" action="deletion_request_action.php" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>