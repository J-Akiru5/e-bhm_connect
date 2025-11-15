<?php
// Public BHW Login Page
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
?>

<div class="vh-100 d-flex align-items-center justify-content-center">
    <div class="card shadow-sm" style="max-width:420px; width:100%">
        <div class="card-body p-4">
            <h3 class="card-title mb-3 text-center">BHW Login</h3>

            

            <form method="post" action="/e-bmw_connect/actions/login_bhw_action.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required autofocus>
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
    </div>
</div>

<?php
include_once __DIR__ . '/../includes/footer_public.php';
?>
