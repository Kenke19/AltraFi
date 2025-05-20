<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - AltraFi</title>
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
            --sidebar-width: 250px;
            --glass: rgba(255, 255, 255, 0.1);
            --sidebar-bg: rgba(15, 23, 42, 0.9);
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
        .contact {
            margin-left: var(--sidebar-width);
            padding: 3rem;
            width: 100%;
            max-width: 1200px;
        }

        .contact-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .contact-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(to right, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: inline-block;
        }

        .contact-header p {
            color: var(--text-muted);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Contact Cards */
        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .info-card {
            background: var(--light);
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-top: 3px solid var(--accent);
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(67, 97, 238, 0.1);
        }

        .info-card i {
            font-size: 2.5rem;
            color: var(--accent);
            margin-bottom: 1.5rem;
            background: rgba(0, 255, 204, 0.1);
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .info-card h3 {
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .info-card p, .info-card a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .info-card a:hover {
            color: var(--accent);
        }

        /* Contact Form */
        .contact-form {
            background: var(--light);
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            max-width: 800px;
            margin: 0 auto;
        }

        .contact-form h2 {
            margin-bottom: 1.5rem;
            color: var(--primary);
            font-size: 1.8rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(0, 255, 204, 0.2);
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .btn-submit {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .contact {
                margin-left: 0;
                padding: 2rem;
            }
            
            .sidebar-toggle {
                display: flex;
            }
        }

        @media (max-width: 768px) {
            .contact-header h1 {
                font-size: 2rem;
            }
            
            .contact-form {
                padding: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .contact-header h1 {
                font-size: 1.8rem;
            }
            
            .info-card {
                padding: 1.5rem;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

    <!-- Contact Section -->
    <main class="contact">
        <header class="contact-header">
            <h1>Contact Us</h1>
            <p>We're here to help and answer any questions you might have. Reach out to us through any of these channels or send us a message directly.</p>
        </header>

        <!-- Contact Information -->
        <div class="contact-info">
            <div class="info-card">
                <i class="fa-solid fa-phone"></i>
                <h3>Phone Support</h3>
                <p>+234 555 1234</p>
                <p>Mon-Fri: 9am - 5pm WAT</p>
            </div>
            
            <div class="info-card">
                <i class="fa-solid fa-envelope"></i>
                <h3>Email Us</h3>
                <p><a href="mailto:support@AltraFi.com">support@AltraFi.com</a></p>
                <p>Average response time: 24 hours</p>
            </div>
            
            <div class="info-card">
                <i class="fa-solid fa-map-marker-alt"></i>
                <h3>Visit Us</h3>
                <p>123 Finance Street</p>
                <p>Lagos, Nigeria</p>
            </div>
        </div>

        <!-- Contact Form -->
        <form action="#" method="post" class="contact-form">
            <h2>Send Us a Message</h2>
            
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address" required>
            </div>
            
            <div class="form-group">
                <label for="message">Your Message</label>
                <textarea id="message" name="message" class="form-control" placeholder="How can we help you?" required></textarea>
            </div>
            
            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-paper-plane"></i> Send Message
            </button>
        </form>
    </main>

    <script src="script.js"></script>
</body>
</html>