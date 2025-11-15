<?php
// Public BHW Registration Page
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
?>

<div class="vh-100 d-flex align-items-center justify-content-center">
    <div class="card shadow-sm" style="max-width:540px; width:100%">
        <div class="card-body p-4">
            <h3 class="card-title mb-3 text-center">BHW Registration</h3>

            <form method="post" action="/e-bmw_connect/actions/register_bhw_action.php">
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
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

                <div class="mb-3">
                    <label for="bhw_unique_id" class="form-label">BHW ID Number</label>
                    <input type="text" class="form-control" id="bhw_unique_id" name="bhw_unique_id" required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Register</button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            Already have an account? <a href="/e-bmw_connect/login-bhw">Login here</a>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../includes/footer_public.php';
?>
