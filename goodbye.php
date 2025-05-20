<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goodbye from AltraFi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #00c6ff;
            --secondary: #0072ff;
            --accent: #00ffcc;
            --dark: #0f172a;
            --light: #f8fafc;
            --error: #e74c3c;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, var(--dark), #1e293b);
            color: var(--light);
            text-align: center;
            padding: 1rem;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(0,198,255,0.1) 0%, rgba(0,114,255,0.05) 70%, transparent 100%);
            animation: pulse 15s infinite alternate;
            z-index: -1;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.2); opacity: 0.5; }
            100% { transform: scale(1); opacity: 0.3; }
        }

        .goodbye-container {
            max-width: 600px;
            padding: 3rem 2.5rem;
            background: rgba(15, 23, 42, 0.8);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(0, 198, 255, 0.2);
            transform: translateY(20px);
            opacity: 0;
            animation: fadeInUp 0.8s 0.2s forwards;
        }

        @keyframes fadeInUp {
            to { transform: translateY(0); opacity: 1; }
        }

        .goodbye-icon {
            font-size: 5rem;
            color: var(--error);
            margin-bottom: 1.5rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-20px); }
            60% { transform: translateY(-10px); }
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(to right, var(--error), #ff7675);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            line-height: 1.2;
        }

        p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.8);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.8rem;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 198, 255, 0.3);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 198, 255, 0.4);
        }

        .btn i {
            transition: transform 0.3s ease;
        }

        .btn:hover i {
            transform: translateX(3px);
        }

        .floating {
            position: absolute;
            background: rgba(0, 255, 204, 0.1);
            border-radius: 50%;
            pointer-events: none;
            z-index: -1;
        }

        .floating:nth-child(1) {
            width: 100px;
            height: 100px;
            top: 20%;
            left: 10%;
            animation: float 8s ease-in-out infinite;
        }

        .floating:nth-child(2) {
            width: 150px;
            height: 150px;
            bottom: 15%;
            right: 10%;
            animation: float 12s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-20px) translateX(20px); }
        }

        @media (max-width: 768px) {
            .goodbye-container {
                padding: 2rem 1.5rem;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .goodbye-icon {
                font-size: 4rem;
            }
            
            .floating {
                display: none;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 1.5rem;
            }
            
            .goodbye-container {
                padding: 1.5rem 1rem;
            }
            
            h1 {
                font-size: 1.8rem;
            }
            
            p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="floating"></div>
    <div class="floating"></div>
    
    <div class="goodbye-container">
        <div class="goodbye-icon">
            <i class="fas fa-heart-crack"></i>
        </div>
        <h1>We're Sad to See You Go</h1>
        <p>Your request to delete your AltraFi account has been received and is pending admin approval.</p>
        <p>Please note that your account will be permanently deleted only after the admin approves the request, which may take up to 30 days.</p>
        <p>If you change your mind during this period, you can contact our support team to cancel the deletion and retain your account.</p>
        <p>Thank you for being part of our community.</p>
        <a href="index.php" class="btn">
            <span>Sign Up Again</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</body>
</html>