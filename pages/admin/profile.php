<?php
// pages/admin/profile.php
// Profile management for logged-in BHW
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';

$bhw_id = $_SESSION['bhw_id'] ?? null;
if (!$bhw_id) {
    // Not logged in; redirect to login
    header('Location: ' . BASE_URL . 'login-bhw');
    exit();
}

$stmt = $pdo->prepare("SELECT full_name, username FROM bhw_users WHERE bhw_id = ?");
$stmt->execute([$bhw_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

include_once __DIR__ . '/../../includes/header_admin.php';
?>

<div class="container">
    <h1 class="mb-4">My Profile</h1>

    <?php
    // Flash messages via SweetAlert2
    if (isset($_SESSION['form_success'])) {
        $msg = json_encode($_SESSION['form_success']);
        echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'success', title: 'Success', text: $msg}); } });</script>";
        unset($_SESSION['form_success']);
    }
    if (isset($_SESSION['form_error'])) {
        $emsg = json_encode($_SESSION['form_error']);
        echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'error', title: 'Error', text: $emsg}); } });</script>";
        unset($_SESSION['form_error']);
    }
    ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Update Information</div>
                <div class="card-body">
                    <form method="POST" action="?action=update-profile">
                        <input type="hidden" name="bhw_id" value="<?php echo htmlspecialchars($bhw_id); ?>">

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" required value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>">
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-primary" type="submit">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Change Password</div>
                <div class="card-body">
                    <form method="POST" action="?action=change-password">
                        <input type="hidden" name="bhw_id" value="<?php echo htmlspecialchars($bhw_id); ?>">

                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="old_password" class="form-control" placeholder="Current Password" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" placeholder="New Password" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_new_password" class="form-control" placeholder="Confirm New Password" required>
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-success" type="submit">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Profile Picture</div>
                <div class="card-body">
                    <p>Profile picture upload functionality will be added here.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
