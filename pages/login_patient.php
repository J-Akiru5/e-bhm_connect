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

<div class="auth-page">
    <div class="auth-card" data-aos="fade-up">
        <div class="auth-header">
            <a href="<?php echo BASE_URL; ?>?page=home">
                <img src="<?php echo BASE_URL; ?>assets/images/e-logo.png" alt="E-BHM Connect" class="logo">
            </a>
            <h2>Welcome Back</h2>
            <p>Sign in to access your health portal</p>
        </div>

        <form method="post" action="?action=login-patient" class="glass-form">
            <div class="mb-4">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required autofocus>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                    <polyline points="10 17 15 12 10 7"></polyline>
                    <line x1="15" y1="12" x2="3" y2="12"></line>
                </svg>
                Sign In
            </button>
        </form>

        <div class="auth-footer">
            <p style="margin: 0;">Don't have an account? <a href="<?php echo BASE_URL; ?>register-patient">Register here</a></p>
        </div>
        
        <div style="text-align: center; margin-top: var(--space-4);">
            <a href="<?php echo BASE_URL; ?>?page=home" style="color: var(--gray-500); font-size: var(--font-size-sm);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px; vertical-align: middle;">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
if (typeof AOS !== 'undefined') {
    AOS.init({ duration: 600, once: true });
}
</script>

</body>
</html>
