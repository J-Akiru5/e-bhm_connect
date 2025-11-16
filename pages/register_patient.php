<?php
// Public Patient Registration Page
include_once __DIR__ . '/../includes/header_public.php';

// Start session to read flash messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Show register_error via SweetAlert2 if present
if (isset($_SESSION['register_error'])) {
    $msg = json_encode($_SESSION['register_error']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'error', title: 'Registration failed', text: $msg}); } else { console.error('SweetAlert2 not loaded'); } });</script>";
    unset($_SESSION['register_error']);
}

// Show register_success via SweetAlert2 if present
if (isset($_SESSION['register_success'])) {
    $smsg = json_encode($_SESSION['register_success']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'success', title: 'Registered', text: $smsg}); } else { console.log('Registration: ' + $smsg); } });</script>";
    unset($_SESSION['register_success']);
}
?>

<div class="vh-100 d-flex align-items-center justify-content-center">
    <div class="card shadow-sm" style="max-width:640px; width:100%">
        <div class="card-body p-4">
            <h3 class="card-title mb-3 text-center">Patient Registration</h3>

            <form method="post" action="?action=register-patient">
                <h5 class="mb-2">Verify Your Identity</h5>
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                </div>
                <div class="mb-3">
                    <label for="birthdate" class="form-label">Birthdate</label>
                    <input type="date" class="form-control" id="birthdate" name="birthdate" required>
                </div>

                <hr>
                <h5 class="mb-2">Create Your Account</h5>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirm" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Register</button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            Already have an account? <a href="<?php echo BASE_URL; ?>login-patient">Login here</a>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../includes/footer_public.php';
?>
