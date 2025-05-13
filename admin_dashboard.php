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
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
    <style>
        /* Reset & base */
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7fa;
            margin: 0; padding: 20px;
            color: #333;
        }
        h1, h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
        }
        th {
            background: #34495e;
            color: #ecf0f1;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
        tr:hover {
            background: #f1f6fb;
        }
        .btn {
            padding: 6px 12px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background 0.3s ease;
            color: white;
        }
        .btn-approve { background-color: #27ae60; }
        .btn-approve:hover { background-color: #219150; }
        .btn-cancel { background-color: #c0392b; }
        .btn-cancel:hover { background-color: #992d22; }
        .btn-delete { background-color: #e74c3c; }
        .btn-delete:hover { background-color: #b33a2a; }
        .btn-admin { background-color: #2980b9; }
        .btn-admin:hover { background-color: #1f6391; }
        .btn-freeze { background-color: #f39c12; }
        .btn-freeze:hover { background-color: #d68910; }
        .btn-unfreeze { background-color: #2980b9; }
        .btn-unfreeze:hover { background-color: #1f6391; }
        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            color: white;
            display: inline-block;
        }
        .status-pending { background-color: #f39c12; }
        .status-approved { background-color: #27ae60; }
        .status-cancelled, .status-rejected { background-color: #c0392b; }
        .status-admin { background-color: #2980b9; }
        .status-active { background-color: #27ae60; }
        .status-frozen { background-color: #f39c12; }
        .status-deleted { background-color: #7f8c8d; }
        .container { max-width: 1200px; margin: auto; }
    </style>
</head>
<body>
<div class="container">
    <h1>Admin Dashboard</h1>

    <h2>Transactions</h2>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Reference</th>
            <th>User ID</th>
            <th>User</th>
            <th>Amount (₦)</th>
            <th>Description</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($transactions as $t): ?>
            <tr>
                <td><?= htmlspecialchars($t['id']) ?></td>
                <td><?= htmlspecialchars($t['reference']) ?></td>
                <td><?= htmlspecialchars($t['user_id']) ?></td>
                <td><?= htmlspecialchars($t['username']) ?> (<?= htmlspecialchars($t['first_name'] . ' ' . $t['last_name']) ?>)</td>
                <td><?= number_format($t['amount'], 2) ?></td>
                <td><?= htmlspecialchars($t['description']) ?></td>
                <td>
                    <span class="status-badge status-<?= htmlspecialchars($t['status']) ?>">
                        <?= ucfirst(htmlspecialchars($t['status'])) ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($t['created_at']) ?></td>
                <td>
                    <?php if ($t['status'] === 'pending'): ?>
                        <form method="POST" action="transaction_action.php" style="display:inline;">
                            <input type="hidden" name="transaction_id" value="<?= $t['id'] ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-approve">Approve</button>
                        </form>
                        <form method="POST" action="transaction_action.php" style="display:inline;">
                            <input type="hidden" name="transaction_id" value="<?= $t['id'] ?>">
                            <input type="hidden" name="action" value="cancel">
                            <button type="submit" class="btn btn-cancel">Cancel</button>
                        </form>
                    <?php else: ?>
                        <em>No actions</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Users</h2>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['id']) ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['phone'] ?? '-') ?></td>
                <td>
                    <?php if ($u['is_deleted']): ?>
                        <span class="status-badge status-deleted">Deleted</span>
                    <?php elseif ($u['is_frozen']): ?>
                        <span class="status-badge status-frozen">Frozen</span>
                    <?php else: ?>
                        <span class="status-badge status-active">Active</span>
                    <?php endif; ?>
                    <?php if ($u['is_admin']): ?>
                        <span class="status-badge status-admin">Admin</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!$u['is_deleted']): ?>
                        <?php if (!$u['is_admin']): ?>
                            <form method="POST" action="user_action.php" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <input type="hidden" name="action" value="toggle_freeze">
                                <button type="submit" class="btn <?= $u['is_frozen'] ? 'btn-unfreeze' : 'btn-freeze' ?>">
                                    <?= $u['is_frozen'] ? 'Unfreeze' : 'Freeze' ?>
                                </button>
                            </form>
                        <?php endif; ?>
                        <form method="POST" action="user_action.php" style="display:inline;" onsubmit="return confirm('Are you sure? This marks the account as deleted but keeps transactions.')">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <input type="hidden" name="action" value="soft_delete">
                            <button type="submit" class="btn btn-delete">Mark Deleted</button>
                        </form>
                        <?php if (!$u['is_admin']): ?>
                            <form method="POST" action="user_action.php" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <input type="hidden" name="action" value="make_admin">
                                <button type="submit" class="btn btn-admin">Make Admin</button>
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <em>Account deleted</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Account Deletion Requests</h2>
    <table>
        <thead>
        <tr>
            <th>Request ID</th>
            <th>User ID</th>
            <th>User</th>
            <th>Email</th>
            <th>Requested At</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($deletionRequests) === 0): ?>
            <tr><td colspan="7" style="text-align:center; font-style: italic;">No pending requests</td></tr>
        <?php else: ?>
            <?php foreach ($deletionRequests as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['id']) ?></td>
                    <td><?= htmlspecialchars($r['user_id']) ?></td>
                    <td><?= htmlspecialchars($r['username']) ?> (<?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?>)</td>
                    <td><?= htmlspecialchars($r['email']) ?></td>
                    <td><?= htmlspecialchars($r['requested_at']) ?></td>
                    <td>
                        <span class="status-badge status-pending">Pending</span>
                    </td>
                    <td>
                        <form method="POST" action="deletion_request_action.php" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-approve">Approve</button>
                        </form>
                        <form method="POST" action="deletion_request_action.php" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-cancel">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
