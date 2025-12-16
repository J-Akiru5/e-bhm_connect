<?php
// Public Patient Registration Page
// Start session to read flash messages
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
    <title>Resident Registration - E-BHM Connect</title>
    
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
// Show register_error via SweetAlert2 if present
if (isset($_SESSION['register_error'])) {
    $msg = json_encode($_SESSION['register_error']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'error', title: 'Registration failed', text: $msg}); } });</script>";
    unset($_SESSION['register_error']);
}

// Show register_success via SweetAlert2 if present
if (isset($_SESSION['register_success'])) {
    $smsg = json_encode($_SESSION['register_success']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'success', title: 'Registered', text: $smsg}).then(() => { window.location.href = '".BASE_URL."?page=login-patient'; }); } });</script>";
    unset($_SESSION['register_success']);
}
?>

<div class="auth-page">
    <div class="auth-card wide" data-aos="fade-up">
        <div class="auth-header">
            <a href="<?php echo BASE_URL; ?>?page=home">
                <img src="<?php echo BASE_URL; ?>assets/images/e-logo.png" alt="E-BHM Connect" class="logo">
            </a>
            <h2>Create Your Account</h2>
            <p>Join E-BHM Connect to access your health records</p>
        </div>

        <form method="post" action="?action=register-patient" class="glass-form">
            <div style="margin-bottom: var(--space-6);">
                <h5 style="color: var(--primary); font-size: var(--font-size-sm); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: var(--space-4);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px; vertical-align: middle;">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Personal Information
                </h5>
                
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Enter your full name" required>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="birthdate" class="form-label">Birthdate</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="sex" class="form-label">Gender</label>
                        <select id="sex" name="sex" class="form-select">
                            <option value="">Select gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="contact" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contact" name="contact" placeholder="09XXXXXXXXX">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" placeholder="Enter your complete address">
                </div>
            </div>

            <div style="margin-bottom: var(--space-6); padding-top: var(--space-4); border-top: 1px solid var(--glass-border);">
                <h5 style="color: var(--primary); font-size: var(--font-size-sm); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: var(--space-4);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px; vertical-align: middle;">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    Account Credentials
                </h5>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirm" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Confirm your password" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
                Create Account
            </button>
        </form>

        <div class="auth-footer">
            <p style="margin: 0;">Already have an account? <a href="<?php echo BASE_URL; ?>?page=login-patient">Sign in here</a></p>
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
