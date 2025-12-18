<?php
/**
 * User Access Control Panel (Super Admin Only)
 * E-BHM Connect - Glassmorphism Design
 * 
 * Manage user roles and access levels
 */

// Include config first (no output)
include_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth_helpers.php';
require_once __DIR__ . '/../../includes/pagination_helper.php';

// Require superadmin access
if (!is_superadmin()) {
    $_SESSION['flash_error'] = 'You do not have permission to access this page.';
    header('Location: ' . BASE_URL . 'admin-dashboard');
    exit;
}

// Now include header (outputs HTML)
include_once __DIR__ . '/../../includes/header_admin.php';

// Flash messages
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Define available roles
$available_roles = [
    'bhw' => ['label' => 'Health Worker', 'color' => 'info', 'description' => 'Can manage patients, view inventory, create visits'],
    'admin' => ['label' => 'Administrator', 'color' => 'warning', 'description' => 'Can manage inventory, announcements, programs, and view reports'],
    'superadmin' => ['label' => 'Super Admin', 'color' => 'danger', 'description' => 'Full system access including user management and settings']
];

// Pagination
$per_page = 15;
$current_page = isset($_GET['pg']) ? max(1, (int) $_GET['pg']) : 1;

// Search and filter
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';

// Build query
$where = ['1=1'];
$params = [];

if (!empty($search)) {
    $where[] = '(full_name LIKE ? OR username LIKE ? OR email LIKE ?)';
    $search_term = '%' . $search . '%';
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

if (!empty($role_filter)) {
    $where[] = 'role = ?';
    $params[] = $role_filter;
}

$where_clause = implode(' AND ', $where);

// Get total count
$total_records = 0;
try {
    $count_sql = "SELECT COUNT(*) FROM bhw_users WHERE $where_clause";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_records = (int) $count_stmt->fetchColumn();
} catch (Throwable $e) {
    error_log('User role count error: ' . $e->getMessage());
}

$pagination = paginate($total_records, $per_page, $current_page);

// Get users
$users = [];
try {
    $sql = "SELECT bhw_id, full_name, username, email, role, email_verified, created_at, last_login 
            FROM bhw_users 
            WHERE $where_clause 
            ORDER BY role DESC, full_name ASC 
            LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('User role fetch error: ' . $e->getMessage());
}

// Get role counts for stats
$role_counts = [];
try {
    $stmt = $pdo->query("SELECT role, COUNT(*) as count FROM bhw_users GROUP BY role");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $role_counts[$row['role']] = (int) $row['count'];
    }
} catch (Throwable $e) {
    error_log('Role count error: ' . $e->getMessage());
}
?>

<div class="container-fluid py-4 fade-in">
    <!-- Page Header -->
    <div class="glass-card mb-4">
        <div class="glass-card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h4 class="mb-1"><?php echo __('user_roles.title') ?: 'User Access Control'; ?></h4>
                <p class="text-muted mb-0"><?php echo __('user_roles.description') ?: 'Manage user roles and access levels'; ?></p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge badge-primary"><?php echo number_format($total_records); ?> users</span>
            </div>
        </div>
    </div>

    <?php if ($flash_success): ?>
    <div class="alert alert-success mb-4">
        <?php echo htmlspecialchars($flash_success); ?>
    </div>
    <?php endif; ?>

    <?php if ($flash_error): ?>
    <div class="alert alert-danger mb-4">
        <?php echo htmlspecialchars($flash_error); ?>
    </div>
    <?php endif; ?>

    <!-- Role Stats -->
    <div class="row g-4 mb-4">
        <?php foreach ($available_roles as $role_key => $role_info): ?>
        <div class="col-md-4">
            <div class="glass-card">
                <div class="glass-card-body d-flex align-items-center gap-3">
                    <div class="stat-icon <?php echo $role_info['color']; ?>" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="stat-value"><?php echo $role_counts[$role_key] ?? 0; ?></div>
                        <div class="stat-label"><?php echo $role_info['label']; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Filters -->
    <div class="glass-card mb-4">
        <div class="glass-card-body">
            <form method="GET" action="" class="row g-3">
                <input type="hidden" name="page" value="admin-user-roles">
                
                <div class="col-md-5">
                    <label class="form-label small"><?php echo __('search') ?: 'Search'; ?></label>
                    <input type="text" name="search" class="form-control" placeholder="Search by name, username, or email..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label small"><?php echo __('user_roles.filter_role') ?: 'Filter by Role'; ?></label>
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <?php foreach ($available_roles as $role_key => $role_info): ?>
                        <option value="<?php echo $role_key; ?>" <?php echo $role_filter === $role_key ? 'selected' : ''; ?>>
                            <?php echo $role_info['label']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary"><?php echo __('filter') ?: 'Filter'; ?></button>
                    <a href="<?php echo BASE_URL; ?>admin-user-roles" class="btn btn-glass"><?php echo __('clear') ?: 'Clear'; ?></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="glass-card">
        <div class="glass-card-body p-0">
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th><?php echo __('user_roles.user') ?: 'User'; ?></th>
                            <th><?php echo __('user_roles.email') ?: 'Email'; ?></th>
                            <th><?php echo __('user_roles.current_role') ?: 'Current Role'; ?></th>
                            <th><?php echo __('user_roles.status') ?: 'Status'; ?></th>
                            <th><?php echo __('user_roles.last_login') ?: 'Last Login'; ?></th>
                            <th class="text-end"><?php echo __('user_roles.change_role') ?: 'Change Role'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <?php echo __('no_data') ?: 'No users found'; ?>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar" style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem;">
                                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="fw-medium"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                            <div class="text-muted small">@<?php echo htmlspecialchars($user['username']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php 
                                    $role = $user['role'] ?? 'bhw';
                                    $role_info = $available_roles[$role] ?? $available_roles['bhw'];
                                    ?>
                                    <span class="badge badge-<?php echo $role_info['color']; ?>">
                                        <?php echo $role_info['label']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['email_verified']): ?>
                                    <span class="badge badge-success">Verified</span>
                                    <?php else: ?>
                                    <span class="badge badge-warning">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['last_login']): ?>
                                    <span class="text-muted small"><?php echo date('M d, Y H:i', strtotime($user['last_login'])); ?></span>
                                    <?php else: ?>
                                    <span class="text-muted small">Never</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ((int)$user['bhw_id'] !== (int)$_SESSION['bhw_id']): ?>
                                    <form method="POST" action="<?php echo BASE_URL; ?>actions/user_role_update.php" class="d-inline" onsubmit="return confirmRoleChange(this)">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                        <input type="hidden" name="user_id" value="<?php echo $user['bhw_id']; ?>">
                                        <select name="new_role" class="form-select form-select-sm d-inline-block" style="width: auto;" onchange="this.form.submit()">
                                            <?php foreach ($available_roles as $role_key => $role_info): ?>
                                            <option value="<?php echo $role_key; ?>" <?php echo ($user['role'] ?? 'bhw') === $role_key ? 'selected' : ''; ?>>
                                                <?php echo $role_info['label']; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                    <?php else: ?>
                                    <span class="text-muted small">(You)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="glass-card-footer">
            <?php echo render_pagination($pagination, get_pagination_base_url()); ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Role Legend -->
    <div class="glass-card mt-4">
        <div class="glass-card-header">
            <h6 class="glass-card-title mb-0"><?php echo __('user_roles.role_permissions') ?: 'Role Permissions'; ?></h6>
        </div>
        <div class="glass-card-body">
            <div class="row g-3">
                <?php foreach ($available_roles as $role_key => $role_info): ?>
                <div class="col-md-4">
                    <div class="d-flex align-items-start gap-2">
                        <span class="badge badge-<?php echo $role_info['color']; ?>"><?php echo $role_info['label']; ?></span>
                        <small class="text-muted"><?php echo $role_info['description']; ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
function confirmRoleChange(form) {
    const select = form.querySelector('select[name="new_role"]');
    const newRole = select.options[select.selectedIndex].text;
    return confirm(`Are you sure you want to change this user's role to ${newRole}?`);
}
</script>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
