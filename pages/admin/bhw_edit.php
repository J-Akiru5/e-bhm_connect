<?php
// pages/admin/bhw_edit.php
// Edit BHW user form
include_once __DIR__ . '/../../includes/header_admin.php';

$bhw_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($bhw_id <= 0) {
    header('Location: ' . BASE_URL . 'admin-bhw-users');
    exit();
}

try {
    $stmt = $pdo->prepare('SELECT * FROM bhw_users WHERE bhw_id = ? LIMIT 1');
    $stmt->execute([$bhw_id]);
    $bhw = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Fetch BHW for edit error: ' . $e->getMessage());
    $bhw = false;
}

if (!$bhw) {
    header('Location: ' . BASE_URL . 'admin-bhw-users');
    exit();
}
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Edit BHW User</h3>
        </div>
        <div class="card-body">
            <form method="post" action="?action=update-bhw">
                <input type="hidden" name="bhw_id" value="<?php echo htmlspecialchars($bhw['bhw_id']); ?>">

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($bhw['full_name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($bhw['username']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">BHW ID Number</label>
                    <input type="text" name="bhw_unique_id" class="form-control" value="<?php echo htmlspecialchars($bhw['bhw_unique_id']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($bhw['address']); ?>">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Birthdate</label>
                        <input type="date" name="birthdate" class="form-control" value="<?php echo htmlspecialchars($bhw['birthdate']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact</label>
                        <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars($bhw['contact']); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Training Certificate</label>
                    <textarea name="training_cert" class="form-control" rows="3"><?php echo htmlspecialchars($bhw['training_cert']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Assigned Area</label>
                    <input type="text" name="assigned_area" class="form-control" value="<?php echo htmlspecialchars($bhw['assigned_area']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Employment Status</label>
                    <input type="text" name="employment_status" class="form-control" value="<?php echo htmlspecialchars($bhw['employment_status']); ?>">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Update BHW</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
