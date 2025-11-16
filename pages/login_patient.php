<?php
// Public Patient Login Page
include_once __DIR__ . '/../includes/header_public.php';

// Start session so we can read flash messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If there is a login error, show it via SweetAlert2 after page load
if (isset($_SESSION['login_error'])) {
    $msg = json_encode($_SESSION['login_error']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'error', title: 'Login failed', text: $msg}); } else { console.error('SweetAlert2 not loaded'); } });</script>";
    unset($_SESSION['login_error']);
}

// If registration just succeeded, show a success message
if (isset($_SESSION['register_success'])) {
    $smsg = json_encode($_SESSION['register_success']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'success', title: 'Registered', text: $smsg}); } else { console.log('Registration: ' + $smsg); } });</script>";
    unset($_SESSION['register_success']);
}

?>

<div class="vh-100 d-flex align-items-center justify-content-center">
    <div class="card shadow-sm" style="max-width:420px; width:100%">
        <div class="card-body p-4">
            <h3 class="card-title mb-3 text-center">Patient Login</h3>

            <form method="post" action="?action=login-patient">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            Don't have an account? <a href="<?php echo BASE_URL; ?>register-patient">Register here</a>
        </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../includes/footer_public.php';
?>
