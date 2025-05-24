<?php 
// Start session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in to show different navigation
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found | AltraFi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
    </style>
</head>
<body>
  <div class="container">
        <h1 class="display-1">404</h1>
        <p class="lead">The page you're looking for doesn't exist.</p>
        <div class="mt-4">
            <?php if ($isLoggedIn): ?>
                <a href="dashboard.php" class="btn btn-primary btn-lg">Back to Dashboard</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary btn-lg">Go to Login</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
