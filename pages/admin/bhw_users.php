<?php
// pages/admin/bhw_users.php
// BHW User Management list
include_once __DIR__ . '/../../includes/header_admin.php';

$bhw_users = [];
try {
    $stmt = $pdo->query('SELECT bhw_id, full_name, username, bhw_unique_id, assigned_area FROM bhw_users ORDER BY full_name ASC');
    $bhw_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('BHW users fetch error: ' . $e->getMessage());
}
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>BHW User Management</h1>
        <a href="<?php echo BASE_URL; ?>register-bhw" class="btn btn-success mb-3">Add New BHW</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>BHW ID</th>
                            <th>Assigned Area</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bhw_users)): ?>
                            <tr><td colspan="5">No BHW users found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($bhw_users as $bhw): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($bhw['full_name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($bhw['username'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($bhw['bhw_unique_id'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($bhw['assigned_area'] ?? ''); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>admin-bhw-edit?id=<?php echo $bhw['bhw_id']; ?>" class="btn btn-secondary btn-sm">Edit</a>

                                        <form action="<?php echo BASE_URL; ?>?action=delete-bhw" method="POST" class="d-inline" onsubmit="return confirmDelete(event);">
                                            <input type="hidden" name="bhw_id" value="<?php echo htmlspecialchars($bhw['bhw_id'] ?? ''); ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php
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

</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
<?php
// Admin: BHW users management (placeholder)
require_once __DIR__ . '/../../includes/header_admin.php';
?>

<?php require_once __DIR__ . '/../../includes/footer_admin.php';
