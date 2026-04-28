<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>QBCC | Secure Login</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #d4af37; /* Luxury Gold */
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-color: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #1a1a1a url('/images/carpet_login_bg.png') no-repeat center center fixed;
            background-size: cover;
            overflow: hidden;
            position: relative;
        }

        /* Overlay to make text readable */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 100%);
            z-index: 1;
        }

        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 450px;
            padding: 20px;
            animation: fadeInScale 1s ease-out;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
        }

        .logo-box {
            margin-bottom: 30px;
            animation: float 4s ease-in-out infinite;
        }

        .logo-box img {
            width: 120px;
            height: auto;
            filter: drop-shadow(0 0 10px rgba(255,255,255,0.3));
        }

        h2 {
            color: var(--text-color);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        p.subtitle {
            color: rgba(255,255,255,0.7);
            margin-bottom: 40px;
            font-size: 0.95rem;
        }

        .form-group {
            position: relative;
            margin-bottom: 25px;
            text-align: left;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.5);
            transition: color 0.3s;
        }

        .form-control {
            width: 100%;
            padding: 15px 15px 15px 45px;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: #fff;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s;
        }

        .form-control:focus {
            background: rgba(255,255,255,0.1);
            border-color: var(--primary-color);
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
        }

        .form-control:focus + i {
            color: var(--primary-color);
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(90deg, #d4af37, #f1c40f);
            border: none;
            border-radius: 12px;
            color: #000;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2);
        }

        .btn-login:hover {
            transform: scale(1.02);
            box-shadow: 0 15px 30px rgba(212, 175, 55, 0.4);
            filter: brightness(1.1);
        }

        .error-message {
            background: rgba(255, 0, 0, 0.2);
            border: 1px solid rgba(255, 0, 0, 0.3);
            color: #ff9999;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }

        .footer-links {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
        }

        .footer-links a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        /* Animations */
        @keyframes fadeInScale {
            0% { opacity: 0; transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .glass-card {
                padding: 40px 25px;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="glass-card">
            <div class="logo-box">
                <img src="/printStyle/logo.png" alt="QBCC Logo">
            </div>
            
            <h2>Welcome Back</h2>
            <p class="subtitle">Please enter your details to sign in</p>

            @if(Session::has('error'))
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> Invalid credentials. Please try again.
                </div>
            @endif

            <form action="/login" method="post" id="loginForm">
                @csrf
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Email Address" required autofocus>
                    <i class="fas fa-envelope"></i>
                </div>
                
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <i class="fas fa-lock"></i>
                </div>

                <button type="submit" class="btn-login">
                    SIGN IN <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                </button>
            </form>

            <div class="footer-links">
                <label style="color: rgba(255,255,255,0.6); display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="remember" style="accent-color: var(--primary-color);"> Remember me
                </label>
                <a href="#">Forgot Password?</a>
            </div>
        </div>
        
        <p style="text-align: center; margin-top: 30px; color: rgba(255,255,255,0.4); font-size: 0.8rem; letter-spacing: 1px;">
            &copy; {{ date('Y') }} QBCC CARPET ERP SYSTEM
        </p>
    </div>

</body>
</html>
