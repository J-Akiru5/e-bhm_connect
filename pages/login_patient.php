<?php
// Public Patient Login Page
// Start session so we can read flash messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Login - E-BHM Connect</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Poppins Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    
    <style>
        /* Full-Screen Glassmorphism Auth Layout */
        .auth-fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 24px;
        }
        
        /* Animated Floating Orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
            animation: float 20s ease-in-out infinite;
            pointer-events: none;
        }
        
        .orb-1 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(32, 201, 151, 0.8) 0%, transparent 70%);
            top: -10%;
            left: -10%;
        }
        
        .orb-2 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.7) 0%, transparent 70%);
            bottom: -15%;
            right: -15%;
            animation-delay: -5s;
        }
        
        .orb-3 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.5) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -10s;
        }
        
        @keyframes float {
            0%, 100% { 
                transform: translate(0, 0) scale(1); 
            }
            33% { 
                transform: translate(30px, -30px) scale(1.1); 
            }
            66% { 
                transform: translate(-20px, 20px) scale(0.9); 
            }
        }
        
        /* Glass Card Container */
        .auth-glass-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 480px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.20);
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Logo & Header */
        .auth-glass-card .logo-container {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .auth-glass-card .logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 16px;
            filter: drop-shadow(0 4px 16px rgba(32, 201, 151, 0.35));
        }
        
        .auth-glass-card h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
            text-align: center;
        }
        
        .auth-glass-card .subtitle {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            margin-bottom: 32px;
        }
        
        /* Form Styling Following Design System Border Rules */
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-label {
            display: block;
            font-weight: 500;
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 8px;
        }
        
        .glass-input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: #ffffff;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            transition: all 250ms ease;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        
        .glass-input:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.25);
        }
        
        .glass-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(32, 201, 151, 0.8);
            box-shadow: 0 0 0 4px rgba(32, 201, 151, 0.15);
            padding: 11px 15px; /* Compensate for 2px border */
        }
        
        .glass-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }
        
        /* Primary Button */
        .btn-glass-primary {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, #20c997, #0f5132);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 250ms ease;
            box-shadow: 0 4px 16px rgba(32, 201, 151, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-glass-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(32, 201, 151, 0.45);
        }
        
        .btn-glass-primary:active {
            transform: translateY(0);
        }
        
        /* Footer Links */
        .auth-footer {
            margin-top: 32px;
            text-align: center;
        }
        
        .auth-footer p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
            margin-bottom: 16px;
        }
        
        .auth-footer a {
            color: #20c997;
            text-decoration: none;
            font-weight: 600;
            transition: all 250ms ease;
        }
        
        .auth-footer a:hover {
            color: #ffffff;
            text-decoration: underline;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 250ms ease;
        }
        
        .back-link:hover {
            color: #ffffff;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .auth-glass-card {
                padding: 32px 24px;
                border-radius: 16px;
            }
            
            .auth-glass-card h2 {
                font-size: 1.75rem;
            }
            
            .orb-1, .orb-2, .orb-3 {
                filter: blur(60px);
            }
        }
        
        @media (max-width: 480px) {
            .auth-fullscreen {
                padding: 16px;
            }
            
            .auth-glass-card {
                padding: 24px 20px;
            }
            
            .auth-glass-card h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

<?php
// If there is a login error, show it via SweetAlert2 after page load
if (isset($_SESSION['login_error'])) {
    $msg = json_encode($_SESSION['login_error']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'error', title: 'Login failed', text: $msg}); } });</script>";
    unset($_SESSION['login_error']);
}

// If registration just succeeded, show a success message
if (isset($_SESSION['register_success'])) {
    $smsg = json_encode($_SESSION['register_success']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'success', title: 'Registered', text: $smsg}); } });</script>";
    unset($_SESSION['register_success']);
}
?>

<!-- Full-Screen Glassmorphism Layout -->
<div class="auth-fullscreen">
    <!-- Animated Floating Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    
    <!-- Glass Card Container -->
    <div class="auth-glass-card">
        <!-- Logo & Header -->
        <div class="logo-container">
            <a href="<?php echo BASE_URL; ?>?page=home">
                <img src="<?php echo BASE_URL; ?>assets/images/e-logo.png" alt="E-BHM Connect" class="logo">
            </a>
            <h2>Welcome Back</h2>
            <p class="subtitle">Sign in to access your health portal</p>
        </div>

        <!-- Login Form -->
        <form method="post" action="?action=login-patient">
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    class="glass-input" 
                    id="email" 
                    name="email" 
                    placeholder="your.email@example.com" 
                    required 
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    class="glass-input" 
                    id="password" 
                    name="password" 
                    placeholder="Enter your password" 
                    required
                >
            </div>

            <button type="submit" class="btn-glass-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                    <polyline points="10 17 15 12 10 7"></polyline>
                    <line x1="15" y1="12" x2="3" y2="12"></line>
                </svg>
                Sign In
            </button>
        </form>

        <!-- Footer Links -->
        <div class="auth-footer">
            <p>Don't have an account? <a href="<?php echo BASE_URL; ?>register-patient">Register here</a></p>
            <a href="<?php echo BASE_URL; ?>?page=home" class="back-link">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to Home
            </a>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

</body>
</html>
