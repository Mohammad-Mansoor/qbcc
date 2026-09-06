<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>{{ config('company.name', 'QBIC') }} | Secure Login</title>

    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #1d4ed8;
            --primary-hover: #1e40af;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-color: #cbd5e1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        body {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            background: #e2e8f0 url('public/images/carpet_login_bg.png') no-repeat center center;
            background-size: 100% 100%;
            position: relative;
            padding-top: 1.5vh;
        }

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 600px;
            padding: 0 10px;
            animation: fadeInScale 0.6s ease-out;
        }

        .login-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 18px 22px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.2);
            text-align: center;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .logo-box {
            margin-bottom: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 42px;
        }

        .logo-box img {
            max-height: 48px;
            max-width: 180px;
            width: auto;
            object-fit: contain;
        }

        h2.welcome-title {
            color: var(--text-dark);
            font-size: 1.45rem;
            font-weight: 800;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }

        p.subtitle {
            color: var(--text-muted);
            margin-bottom: 14px;
            font-size: 0.82rem;
            font-weight: 600;
            line-height: 1.3;
        }

        .form-group {
            position: relative;
            margin-bottom: 12px;
            text-align: left;
        }

        .form-group .left-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.9rem;
            transition: color 0.3s;
            z-index: 5;
        }

        .form-group .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.95rem;
            cursor: pointer;
            transition: color 0.3s;
            z-index: 10;
            padding: 4px;
        }

        .form-group .toggle-password:hover {
            color: var(--primary-color);
        }

        .form-control {
            width: 100%;
            padding: 9px 38px 9px 35px;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 9px;
            color: var(--text-dark);
            font-size: 0.88rem;
            outline: none;
            transition: all 0.3s;
        }

        .form-control:focus {
            background: #ffffff;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.15);
        }

        .form-control:focus+.left-icon {
            color: var(--primary-color);
        }

        .btn-login {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            border: none;
            border-radius: 9px;
            color: #ffffff;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 4px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
        }

        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 8px 12px;
            border-radius: 8px;
            margin-bottom: 12px;
            font-size: 0.82rem;
            text-align: right;
            direction: rtl;
        }

        .footer-links {
            margin-top: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.78rem;
        }

        .footer-links label {
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        .footer-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .copyright-text {
            text-align: center;
            margin-top: 8px;
            color: var(--text-muted);
            font-size: 0.72rem;
            font-weight: 600;
        }

        /* Animations */
        @keyframes fadeInScale {
            0% {
                opacity: 0;
                transform: scale(0.96);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @media (max-height: 700px) {
            body {
                padding-top: 0.8vh;
            }

            .login-card {
                padding: 14px 18px;
            }

            .logo-box {
                margin-bottom: 4px;
            }

            h2.welcome-title {
                font-size: 1.3rem;
            }

            p.subtitle {
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="login-card">
            <div class="logo-box">
                @php
                    $logoPath = config('company.logo_path', 'images/logos/qasimi_logo.png');
                    $logoUrl = '/' . ltrim($logoPath, '/');
                @endphp
                <img src="public/{{ $logoUrl }}" alt="Company Logo"
                    onerror="this.onerror=null; this.src='/images/logo.png';">
            </div>

            <h2 class="welcome-title">WELCOME</h2>
            <p class="subtitle">{{ config('company.name', 'ورود به سیستم مدیریت مالی') }}</p>

            @if(Session::has('error'))
                <div class="error-message">
                    <i class="fas fa-exclamation-circle ml-1"></i> {{ Session::get('error') }}
                </div>
            @endif

            <form action="/login" method="post" id="loginForm">
                @csrf
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Email Address" required
                        autofocus>
                    <i class="fas fa-envelope left-icon"></i>
                </div>

                <div class="form-group">
                    <input type="password" name="password" id="passwordInput" class="form-control"
                        placeholder="Password" required>
                    <i class="fas fa-lock left-icon"></i>
                    <i class="fas fa-eye toggle-password" id="togglePassword" title="Show/Hide Password"></i>
                </div>

                <button type="submit" class="btn-login">
                    SIGN IN <i class="fas fa-arrow-right" style="margin-left: 6px;"></i>
                </button>
            </form>

            <div class="footer-links">
                <label>
                    <input type="checkbox" name="remember" style="accent-color: var(--primary-color);"> Remember me
                </label>
                <a href="#">Forgot Password?</a>
            </div>
            <p class="copyright-text">
                &copy; {{ date('Y') }} {{ config('company.name', 'QBIC ERP SYSTEM') }}
            </p>
        </div>


    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('passwordInput');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function () {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
</body>

</html>