<?php
// Superadmin User Management
require_once __DIR__ . '/../../includes/auth_helpers.php';
require_superadmin('/admin-dashboard');
include_once __DIR__ . '/../../includes/header_admin.php';

// Handle role/status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $targetId = intval($_POST['user_id'] ?? 0);
    if ($targetId > 0) {
        try {
            if ($action === 'update_role' && in_array($_POST['role'] ?? '', ['bhw','admin','superadmin'])) {
                $stmt = $pdo->prepare('UPDATE bhw_users SET role = ? WHERE bhw_id = ?');
                $stmt->execute([$_POST['role'], $targetId]);
                log_audit('update_role', 'bhw_user', $targetId, ['new_role' => $_POST['role']]);
                $_SESSION['flash_success'] = 'Role updated.';
            }

            if ($action === 'update_status' && in_array($_POST['account_status'] ?? '', ['active','disabled','pending','approved'])) {
                $stmt = $pdo->prepare('UPDATE bhw_users SET account_status = ? WHERE bhw_id = ?');
                $stmt->execute([$_POST['account_status'], $targetId]);
                log_audit('update_status', 'bhw_user', $targetId, ['account_status' => $_POST['account_status']]);
                $_SESSION['flash_success'] = 'Status updated.';
            }
        } catch (Throwable $e) {
            error_log('User management update error: ' . $e->getMessage());
            $_SESSION['flash_error'] = 'Unable to update user.';
        }
    }
    // redirect to avoid resubmit
    header('Location: ' . BASE_URL . 'admin-user-management');
    exit;
}

// Fetch users
$users = [];
try {
    $stmt = $pdo->query('SELECT bhw_id, full_name, username, role, bhw_unique_id, account_status, email_verified, created_at FROM bhw_users ORDER BY full_name ASC');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Fetch users error: ' . $e->getMessage());
}
?>
<div class="container">
    <div class="page-header mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">User Management (Superadmin)</h1>
            <p class="text-secondary mb-0">Manage user roles and account status</p>
        </div>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>

    <div class="glass-card table-container">
        <div class="table-responsive">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Since</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="6"><div class="empty-state">No users found.</div></td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($u['username']); ?></td>
                                <td>
                                    <form method="POST" style="display:inline-flex;gap:8px;align-items:center;">
                                        <input type="hidden" name="action" value="update_role">
                                        <input type="hidden" name="user_id" value="<?php echo (int)$u['bhw_id']; ?>">
                                        <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="bhw" <?php echo ($u['role'] === 'bhw') ? 'selected' : ''; ?>>BHW</option>
                                            <option value="admin" <?php echo ($u['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                                            <option value="superadmin" <?php echo ($u['role'] === 'superadmin') ? 'selected' : ''; ?>>Superadmin</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" style="display:inline-flex;gap:8px;align-items:center;">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="user_id" value="<?php echo (int)$u['bhw_id']; ?>">
                                        <select name="account_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="active" <?php echo ($u['account_status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                            <option value="approved" <?php echo ($u['account_status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                                            <option value="pending" <?php echo ($u['account_status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="disabled" <?php echo ($u['account_status'] === 'disabled') ? 'selected' : ''; ?>>Disabled</option>
                                        </select>
                                    </form>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($u['created_at'] ?? 'now')); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>admin-bhw-edit?id=<?php echo (int)$u['bhw_id']; ?>" class="btn-secondary-glass btn-sm-glass"><i class="fas fa-edit"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>