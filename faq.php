<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQs | AltraFi</title>
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
            width: var(--sidebar-width);
            background: var(--dark);
            color: white;
            height: 100vh;
            position: fixed;
            padding: 1.5rem 1rem;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1rem;
            margin-bottom: 0.5rem;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sidebar a:hover {
            background: rgba(0, 255, 204, 0.1);
            color: var(--accent);
        }

        .sidebar a i {
            width: 20px;
            text-align: center;
        }

        .logout-btn {
            margin-top: 2rem;
            background: rgba(239, 35, 60, 0.1);
            color: var(--error) !important;
        }

        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 100;
            background: var(--primary);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        /* Main Content */
        .faq-container {
            margin-left: var(--sidebar-width);
            padding: 3rem;
            width: 100%;
            max-width: 900px;
        }

        .faq-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .faq-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(to right, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: inline-block;
        }

        .faq-header p {
            color: var(--text-muted);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* FAQ Category */
        .faq-category {
            margin-bottom: 3rem;
            background: var(--light);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .faq-category h2 {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(67, 97, 238, 0.2);
        }

        /* FAQ List */
        .faq-list {
            list-style: none;
        }

        /* FAQ Item */
        .faq-item {
            border-bottom: 1px solid var(--border);
            margin-bottom: 0.5rem;
        }

        /* Question */
        .faq-question {
            cursor: pointer;
            font-weight: 500;
            font-size: 1.1rem;
            color: var(--dark);
            position: relative;
            padding: 1rem 2.5rem 1rem 0;
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            transition: all 0.3s ease;
        }

        .faq-question:hover {
            color: var(--primary);
        }

        /* Arrow icon */
        .faq-question::after {
            content: '+';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5rem;
            color: var(--accent);
            transition: all 0.3s ease;
        }

        .faq-item.active .faq-question::after {
            content: '-';
            color: var(--primary);
        }

        /* Answer */
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            color: var(--text-muted);
            padding-left: 0;
            line-height: 1.7;
            transition: max-height 0.4s ease, padding 0.4s ease;
        }

        .faq-item.active .faq-answer {
            max-height: 500px;
            padding-bottom: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .faq-container {
                margin-left: 0;
                padding: 2rem;
            }
            
            .sidebar-toggle {
                display: flex;
            }
        }

        @media (max-width: 768px) {
            .faq-header h1 {
                font-size: 2rem;
            }
            
            .faq-category {
                padding: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .faq-header h1 {
                font-size: 1.8rem;
            }
            
            .faq-question {
                font-size: 1rem;
                padding-right: 1.8rem;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="sidebar-toggle">
        <i class="fa-solid fa-bars"></i>
    </div>
    <div class="sidebar">
        <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="setup_profile.php"><i class="fa-solid fa-user"></i> Profile</a>
        <a href="faq.php"><i class="fa-solid fa-circle-question"></i> FAQs</a>
        <a href="contact.php"><i class="fa-solid fa-address-book"></i> Contact</a>
        <a href="Security.php"><i class="fa-solid fa-lock"></i> Security & Compliance</a>
        <a href="logout.php" class="logout-btn"><i class="fa-solid fa-door-open"></i> Logout</a>
    </div>

    <main class="faq-container">
        <header class="faq-header">
            <h1>Frequently Asked Questions</h1>
            <p>Find quick answers to common questions about AltraFi services and features</p>
        </header>

        <section class="faq-category" aria-labelledby="general-faqs">
            <h2 id="general-faqs">General Questions</h2>
            <ul class="faq-list">
                <li class="faq-item">
                    <button class="faq-question" aria-expanded="false" aria-controls="faq1" id="faq1-btn">
                        What is AltraFi and how does it work?
                    </button>
                    <div class="faq-answer" id="faq1" role="region" aria-labelledby="faq1-btn" hidden>
                        AltraFi is a modern financial platform that helps you manage your money, investments, and financial goals all in one place. Our system connects to your accounts and provides tools for budgeting, saving, and growing your wealth.
                    </div>
                </li>
                <li class="faq-item">
                    <button class="faq-question" aria-expanded="false" aria-controls="faq2" id="faq2-btn">
                        Is my money safe with AltraFi?
                    </button>
                    <div class="faq-answer" id="faq2" role="region" aria-labelledby="faq2-btn" hidden>
                        Absolutely. We use bank-level security including 256-bit encryption and two-factor authentication. Your funds are held with our partner banks and are protected by all applicable regulations.
                    </div>
                </li>
                <li class="faq-item">
                    <button class="faq-question" aria-expanded="false" aria-controls="faq3" id="faq3-btn">
                        How do I get started with AltraFi?
                    </button>
                    <div class="faq-answer" id="faq3" role="region" aria-labelledby="faq3-btn" hidden>
                        Simply download our app or sign up on our website. You'll be guided through a quick setup process where you can connect your accounts and set up your financial profile.
                    </div>
                </li>
            </ul>
        </section>

        <section class="faq-category" aria-labelledby="accounts-faqs">
            <h2 id="accounts-faqs">Accounts & Services</h2>
            <ul class="faq-list">
                <li class="faq-item">
                    <button class="faq-question" aria-expanded="false" aria-controls="faq4" id="faq4-btn">
                        What types of accounts can I open with AltraFi?
                    </button>
                    <div class="faq-answer" id="faq4" role="region" aria-labelledby="faq4-btn" hidden>
                        We offer checking accounts, savings accounts, investment accounts, and retirement accounts. All accounts can be managed through our unified dashboard.
                    </div>
                </li>
                <li class="faq-item">
                    <button class="faq-question" aria-expanded="false" aria-controls="faq5" id="faq5-btn">
                        Are there any fees for using AltraFi?
                    </button>
                    <div class="faq-answer" id="faq5" role="region" aria-labelledby="faq5-btn" hidden>
                        Our basic account has no monthly fees. Premium features like advanced investment tools and personalized financial planning have competitive monthly subscriptions.
                    </div>
                </li>
                <li class="faq-item">
                    <button class="faq-question" aria-expanded="false" aria-controls="faq6" id="faq6-btn">
                        Can I link my existing bank accounts?
                    </button>
                    <div class="faq-answer" id="faq6" role="region" aria-labelledby="faq6-btn" hidden>
                        Yes, you can securely link most bank accounts from other institutions to get a complete view of your finances in one place.
                    </div>
                </li>
            </ul>
        </section>

        <section class="faq-category" aria-labelledby="security-faqs">
            <h2 id="security-faqs">Security & Privacy</h2>
            <ul class="faq-list">
                <li class="faq-item">
                    <button class="faq-question" aria-expanded="false" aria-controls="faq7" id="faq7-btn">
                        How does AltraFi protect my personal information?
                    </button>
                    <div class="faq-answer" id="faq7" role="region" aria-labelledby="faq7-btn" hidden>
                        We use military-grade encryption, regular security audits, and strict access controls. Your data is never sold to third parties.
                    </div>
                </li>
                <li class="faq-item">
                    <button class="faq-question" aria-expanded="false" aria-controls="faq8" id="faq8-btn">
                        What should I do if I suspect unauthorized activity?
                    </button>
                    <div class="faq-answer" id="faq8" role="region" aria-labelledby="faq8-btn" hidden>
                        Immediately contact our 24/7 support team through the app or website. We'll help secure your account and investigate any suspicious activity.
                    </div>
                </li>
            </ul>
        </section>
    </main>

    <script>
        // Toggle sidebar on mobile
        document.querySelector('.sidebar-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // FAQ Accordion functionality
        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', () => {
                const expanded = button.getAttribute('aria-expanded') === 'true';
                button.setAttribute('aria-expanded', !expanded);
                const answer = document.getElementById(button.getAttribute('aria-controls'));
                const parentItem = button.closest('.faq-item');
                
                if (!expanded) {
                    answer.hidden = false;
                    parentItem.classList.add('active');
                } else {
                    answer.hidden = true;
                    parentItem.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>