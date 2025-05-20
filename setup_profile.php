<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch user data
$stmt = $pdo->prepare("SELECT username, first_name, last_name, email, phone, profile_photo FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$success = $error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Validate username is not empty and not taken by another user
    if (empty($username)) {
        $error = "Username cannot be empty.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$username, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $error = "Username already taken.";
        }
    }

    // Handle profile photo upload
    $profile_photo = $user['profile_photo'];
    if (!$error && isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $upload_dir = __DIR__ . '/uploads';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $new_name = "uploads/profile_" . $_SESSION['user_id'] . "_" . time() . "." . $ext;
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $new_name)) {
                $profile_photo = $new_name;
            } else {
                $error = "Failed to upload photo (check folder permissions).";
            }
        } else {
            $error = "Invalid photo format. Use jpg, jpeg, png, or gif.";
        }
    }

    if (!$error) {
        $stmt = $pdo->prepare("UPDATE users SET username=?, phone=?, profile_photo=? WHERE id=?");
        $stmt->execute([$username, $phone, $profile_photo, $_SESSION['user_id']]);
        $success = "Profile updated successfully!";
        // Refresh user data
        $stmt = $pdo->prepare("SELECT username, first_name, last_name, email, phone, profile_photo FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile | AltraFi</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
            --text-muted: #6c757d;
            --glass: rgba(255, 255, 255, 0.1);
            --sidebar-bg: rgba(15, 23, 42, 0.9);
            --sidebar-width: 250px;
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
        }
        /* Sidebar Styles */
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        width: var(--sidebar-width);
        background: var(--sidebar-bg);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: 1.5rem 1rem;
        z-index: 100;
        transition: transform 0.3s ease;
        border-right: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
        overflow-y: auto;
    }

    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: center;
        padding-bottom: 1.5rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-title {
        font-size: 1.3rem;
        font-weight: 600;
        background: linear-gradient(to right, var(--primary), var(--accent));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .sidebar-nav {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .sidebar a {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.8rem 1rem;
        margin-bottom: 0.5rem;
        color: var(--light);
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .sidebar a:hover {
        background: rgba(0, 198, 255, 0.1);
        color: var(--accent);
        transform: translateX(5px);
    }

    .sidebar a i {
        width: 20px;
        text-align: center;
        font-size: 1.1rem;
    }

    .sidebar-footer {
        margin-top: auto;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .logout-btn {
        background: rgba(255, 77, 77, 0.1);
        color: var(--error) !important;
    }

    .logout-btn:hover {
        background: rgba(255, 77, 77, 0.2) !important;
    }

    /* Mobile Sidebar Toggle */
    .sidebar-toggle {
        position: fixed;
        top: 20px;
        left: 20px;
        z-index: 101;
        background: var(--primary);
        backdrop-filter: blur(10px);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--light);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        border: none;
    }

        /* Main Content */
        .profile-container {
            margin-left: var(--sidebar-width);
            padding: 3rem;
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .profile-card {
            background: var(--light);
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
            width: 100%;
            max-width: 600px;
            position: relative;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .profile-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(to right, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .profile-header p {
            color: var(--text-muted);
        }

        /* Profile Photo */
        .profile-photo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .profile-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--accent);
            box-shadow: 0 0 20px rgba(0, 255, 204, 0.2);
        }

        .photo-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(67, 97, 238, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 3rem;
            border: 3px dashed var(--border);
        }

        .photo-upload {
            position: absolute;
            bottom: 0;
            right: calc(50% - 80px);
            background: var(--primary);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(67, 97, 238, 0.3);
        }

        .photo-upload input {
            display: none;
        }

        /* Form Styles */
        .profile-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }

        input, .readonly-field {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(0, 255, 204, 0.2);
        }

        .readonly-field {
            background: rgba(67, 97, 238, 0.05);
            border: none;
            color: var(--text-muted);
        }

        .form-actions {
            grid-column: span 2;
            display: flex;
            justify-content: flex-end;
            margin-top: 1rem;
        }

        button {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        /* Messages */
        .success, .error {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }

        .success {
            background: rgba(77, 255, 136, 0.1);
            color: var(--success);
            border: 1px solid rgba(77, 255, 136, 0.3);
        }

        .error {
            background: rgba(239, 35, 60, 0.1);
            color: var(--error);
            border: 1px solid rgba(239, 35, 60, 0.3);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .profile-container {
                margin-left: 0;
                padding: 2rem;
            }
            
            .sidebar-toggle {
                display: flex;
            }
        }

        @media (max-width: 768px) {
            .profile-form {
                grid-template-columns: 1fr;
            }
            
            .form-group.full-width {
                grid-column: span 1;
            }
            
            .form-actions {
                grid-column: span 1;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .profile-card {
                padding: 1.5rem;
            }
            
            .profile-header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
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

    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <h1>Profile Settings</h1>
                <p>Manage your account information and preferences</p>
            </div>

            <?php if ($success): ?>
                <div class="success"><?= htmlspecialchars($success) ?></div>
            <?php elseif ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form class="profile-form" method="POST" enctype="multipart/form-data">
                <div class="form-group full-width">
                    <div class="profile-photo-container">
                        <?php if ($user['profile_photo']): ?>
                            <img src="<?= htmlspecialchars($user['profile_photo']) ?>" alt="Profile Photo" class="profile-photo">
                        <?php else: ?>
                            <div class="photo-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                        <label class="photo-upload">
                            <i class="fas fa-camera"></i>
                            <input type="file" name="profile_photo" accept="image/*">
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <div class="readonly-field"><?= htmlspecialchars($user['first_name']) ?></div>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <div class="readonly-field"><?= htmlspecialchars($user['last_name']) ?></div>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="readonly-field"><?= htmlspecialchars($user['email']) ?></div>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
                </div>

                <div class="form-actions">
                    <button type="submit">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
                <br>

            </form>
        <form method="POST" action="request_account_deletion.php" onsubmit="return confirm('Are you sure you want to request account deletion? This action requires admin approval.');">
        <button type="submit" class="btn btn-cancel">Request Account Deletion</button>
        </form>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.querySelector('.sidebar-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // Preview profile photo before upload
        document.querySelector('input[name="profile_photo"]').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                const img = document.querySelector('.profile-photo') || document.querySelector('.photo-placeholder');
                
                reader.onload = function(event) {
                    if (img.classList.contains('photo-placeholder')) {
                        img.outerHTML = `<img src="${event.target.result}" alt="Profile Photo" class="profile-photo">`;
                    } else {
                        img.src = event.target.result;
                    }
                }
                
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    </script>
</body>
</html>