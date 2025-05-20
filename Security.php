<!DOCTYPE html>
<html lang="en">
<head>
    <title>Security & Compliance | AltraFi</title>
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
        .security-container {
            margin-left: var(--sidebar-width);
            width: 100%;
        }

        /* Hero Section */
        .security-hero {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .security-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                transparent 0%,
                rgba(0, 255, 204, 0.1) 50%,
                rgba(67, 97, 238, 0.1) 100%
            );
            transform: rotate(30deg);
            z-index: 1;
        }

        .security-hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .security-hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(to right, white, var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: inline-block;
        }

        .security-hero p {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        /* Security Features Grid */
        .security-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 3rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .security-card {
            background: var(--light);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-top: 3px solid var(--accent);
        }

        .security-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(67, 97, 238, 0.1);
        }

        .security-icon {
            font-size: 2.5rem;
            color: var(--accent);
            margin-bottom: 1.5rem;
        }

        .security-card h3 {
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .security-card p {
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Timeline Section */
        .security-timeline {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 3rem 3rem;
        }

        .security-timeline h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary);
            font-size: 1.8rem;
        }

        .timeline-item {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 2rem;
            border-left: 3px solid var(--accent);
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -0.5rem;
            top: 0.3rem;
            width: 1rem;
            height: 1rem;
            background: var(--accent);
            border-radius: 50%;
        }

        .timeline-item h4 {
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }

        /* Certifications */
        .security-certs {
            background: rgba(67, 97, 238, 0.05);
            padding: 3rem;
            text-align: center;
        }

        .security-certs h2 {
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        .certs-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .cert-badge {
            background: var(--light);
            color: var(--primary);
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid var(--border);
        }

        /* Contact Section */
        .security-contact {
            text-align: center;
            padding: 3rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .security-contact h2 {
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .security-contact p {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .contact-btn {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        .contact-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
        }

        .contact-email {
            display: block;
            margin-top: 1rem;
            color: var(--accent);
            font-weight: 600;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .security-container {
                margin-left: 0;
            }
            
            .sidebar-toggle {
                display: flex;
            }
        }

        @media (max-width: 768px) {
            .security-hero {
                padding: 3rem 1.5rem;
            }
            
            .security-hero h1 {
                font-size: 2rem;
            }
            
            .security-grid {
                padding: 2rem;
                grid-template-columns: 1fr;
            }
            
            .security-timeline {
                padding: 0 2rem 2rem;
            }
        }

        @media (max-width: 480px) {
            .security-hero h1 {
                font-size: 1.8rem;
            }
            
            .security-certs {
                padding: 2rem 1rem;
            }
            
            .security-contact {
                padding: 2rem 1rem;
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

    <div class="security-container">
        <!-- Hero Section -->
        <section class="security-hero">
            <div class="security-hero-content">
                <h1>Security & Compliance</h1>
                <p>At AltraFi, we prioritize the safety of your assets and data. Our platform adheres to the highest security standards and regulatory requirements to give you peace of mind with every transaction.</p>
            </div>
        </section>

        <!-- Security Features Grid -->
        <div class="security-grid">
            <div class="security-card">
                <div class="security-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3>CBN Regulatory Compliance</h3>
                <p>Fully licensed by the Central Bank of Nigeria (CBN) and compliant with all financial regulations including the Cybersecurity Framework and Payment Service Provider requirements.</p>
            </div>

            <div class="security-card">
                <div class="security-icon">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <h3>Data Protection (NDPR)</h3>
                <p>Your personal data is protected under Nigeria Data Protection Regulation with strong encryption, minimal data collection, and strict access controls.</p>
            </div>

            <div class="security-card">
                <div class="security-icon">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <h3>AML & KYC Controls</h3>
                <p>Robust Anti-Money Laundering and Know Your Customer protocols including identity verification and transaction monitoring to prevent financial crimes.</p>
            </div>

            <div class="security-card">
                <div class="security-icon">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h3>Advanced Encryption</h3>
                <p>All data is encrypted using 256-bit AES standards both at rest and in transit, ensuring your information remains completely confidential.</p>
            </div>
        </div>

        <!-- Security Architecture Timeline -->
        <section class="security-timeline">
            <h2>Our Security Architecture</h2>
            
            <div class="timeline-item">
                <h4>End-to-End Encryption</h4>
                <p>Military-grade encryption protects all sensitive data from unauthorized access at every stage of processing and storage.</p>
            </div>
            
            <div class="timeline-item">
                <h4>Multi-Factor Authentication</h4>
                <p>Biometric verification and hardware security keys provide additional layers of protection beyond passwords.</p>
            </div>
            
            <div class="timeline-item">
                <h4>24/7 Monitoring</h4>
                <p>AI-driven systems continuously monitor for suspicious activities and potential threats in real-time.</p>
            </div>
            
            <div class="timeline-item">
                <h4>Incident Response</h4>
                <p>Comprehensive response plan ensures rapid action against any security incidents with full regulatory reporting.</p>
            </div>
        </section>

        <!-- Certifications Section -->
        <section class="security-certs">
            <h2>Certifications & Standards</h2>
            <div class="certs-grid">
                <div class="cert-badge">CBN Licensed</div>
                <div class="cert-badge">NDPR Compliant</div>
                <div class="cert-badge">PCI DSS</div>
                <div class="cert-badge">ISO 27001</div>
                <div class="cert-badge">AML/KYC</div>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="security-contact">
            <h2>Have Security Questions?</h2>
            <p>Our dedicated security team is available around the clock to address any concerns or provide additional information about our protection measures.</p>
            <a href="contact.php" class="contact-btn">Contact Security Team</a>
            <span class="contact-email">security@AltraFi.com</span>
        </section>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.querySelector('.sidebar-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>