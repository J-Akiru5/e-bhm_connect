<?php
/**
 * Registered Residents (Patient Portal Users)
 * Superadmin-only page to manage patient portal accounts
 * E-BHM Connect - Glassmorphism Design
 */
include __DIR__ . '/../../includes/header_admin.php';
require_superadmin();

// Pagination
$page_num = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$per_page = 15;
$offset = ($page_num - 1) * $per_page;

$search = $_GET['search'] ?? '';
$filterStatus = $_GET['status'] ?? '';

// Build query
$whereConditions = [];
$params = [];

if ($search) {
    $whereConditions[] = "(pu.email LIKE ? OR p.full_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($filterStatus === 'active') {
    $whereConditions[] = "pu.status = 'active'";
} elseif ($filterStatus === 'disabled') {
    $whereConditions[] = "pu.status = 'disabled'";
}

$whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get records
$records = [];
$total_records = 0;
try {
    // Count
    $countSql = "SELECT COUNT(*) FROM patient_users pu LEFT JOIN patients p ON pu.patient_id = p.patient_id $whereClause";
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $total_records = (int)$stmt->fetchColumn();

    // Fetch
    $sql = "SELECT pu.*, p.full_name as patient_name, p.contact as patient_contact
            FROM patient_users pu
            LEFT JOIN patients p ON pu.patient_id = p.patient_id
            $whereClause
            ORDER BY pu.created_at DESC
            LIMIT $per_page OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Registered Residents Error: " . $e->getMessage());
}

$total_pages = ceil($total_records / $per_page);

// Stats
$stats = ['total' => 0, 'active' => 0, 'disabled' => 0];
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total,
        SUM(status = 'active') as active,
        SUM(status = 'disabled') as disabled
        FROM patient_users");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total'] = (int)$row['total'];
    $stats['active'] = (int)$row['active'];
    $stats['disabled'] = (int)$row['disabled'];
} catch (PDOException $e) {}
?>

<div class="container-fluid py-4 fade-in">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin-dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Registered Residents</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0" style="color: #8b5cf6;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2" style="vertical-align: -4px;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Registered Residents (Portal Accounts)
            </h1>
            <p class="text-muted mb-0 small">Manage patient portal login accounts</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="stat-card"><div class="stat-card-content text-center">
                <div class="stat-card-value"><?php echo $stats['total']; ?></div>
                <div class="stat-card-label small">Total Accounts</div>
            </div></div>
        </div>
        <div class="col-4">
            <div class="stat-card"><div class="stat-card-content text-center">
                <div class="stat-card-value" style="color: #10b981;"><?php echo $stats['active']; ?></div>
                <div class="stat-card-label small">Active</div>
            </div></div>
        </div>
        <div class="col-4">
            <div class="stat-card"><div class="stat-card-content text-center">
                <div class="stat-card-value" style="color: #ef4444;"><?php echo $stats['disabled']; ?></div>
                <div class="stat-card-label small">Disabled</div>
            </div></div>
        </div>
    </div>

    <!-- Filter -->
    <div class="glass-card mb-4">
        <div class="glass-card-body">
            <form method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="admin-registered-residents">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" placeholder="Email or patient name..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All</option>
                        <option value="active" <?php echo $filterStatus === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="disabled" <?php echo $filterStatus === 'disabled' ? 'selected' : ''; ?>>Disabled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="glass-card">
        <div class="glass-card-header">
            <h5 class="glass-card-title mb-0">Portal Accounts</h5>
        </div>
        <div class="glass-card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Linked Patient</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">No portal accounts found</td></tr>
                        <?php else: ?>
                        <?php foreach ($records as $rec): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm" style="width:32px;height:32px;border-radius:8px;background:#8b5cf620;color:#8b5cf6;display:flex;align-items:center;justify-content:center;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-4 8"/></svg>
                                    </div>
                                    <span class="fw-medium"><?php echo htmlspecialchars($rec['email']); ?></span>
                                </div>
                            </td>
                            <td>
                                <?php if ($rec['patient_name']): ?>
                                <a href="<?php echo BASE_URL; ?>admin-patient-view?id=<?php echo $rec['patient_id']; ?>" class="text-primary">
                                    <?php echo htmlspecialchars($rec['patient_name']); ?>
                                </a>
                                <?php else: ?>
                                <span class="text-muted">No linked patient</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (($rec['status'] ?? 'active') === 'active'): ?>
                                <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                <span class="badge badge-danger">Disabled</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $rec['last_login'] ? date('M j, Y g:ia', strtotime($rec['last_login'])) : '<span class="text-muted">Never</span>'; ?>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($rec['created_at'])); ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <!-- View Details -->
                                    <button type="button" class="btn btn-sm btn-glass view-details-btn" 
                                            data-id="<?php echo $rec['user_id']; ?>"
                                            data-email="<?php echo htmlspecialchars($rec['email']); ?>"
                                            data-patient="<?php echo htmlspecialchars($rec['patient_name'] ?? 'N/A'); ?>"
                                            data-status="<?php echo htmlspecialchars($rec['status'] ?? 'active'); ?>"
                                            data-lastlogin="<?php echo $rec['last_login'] ? date('M j, Y g:ia', strtotime($rec['last_login'])) : 'Never'; ?>"
                                            data-created="<?php echo date('M j, Y g:ia', strtotime($rec['created_at'])); ?>"
                                            title="View Details">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>

                                    <!-- Toggle Status -->
                                    <?php if (($rec['status'] ?? 'active') === 'active'): ?>
                                    <form method="POST" action="<?php echo BASE_URL; ?>?action=resident-account-action" class="d-inline action-form">
                                        <?php echo csrf_input(); ?>
                                        <input type="hidden" name="action_type" value="disable">
                                        <input type="hidden" name="user_id" value="<?php echo $rec['user_id']; ?>">
                                        <button type="button" class="btn btn-sm btn-glass text-warning disable-btn" title="Disable Account">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m4.9 4.9 14.2 14.2"/></svg>
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <form method="POST" action="<?php echo BASE_URL; ?>?action=resident-account-action" class="d-inline action-form">
                                        <?php echo csrf_input(); ?>
                                        <input type="hidden" name="action_type" value="enable">
                                        <input type="hidden" name="user_id" value="<?php echo $rec['user_id']; ?>">
                                        <button type="button" class="btn btn-sm btn-glass text-success enable-btn" title="Enable Account">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <!-- Reset Password -->
                                    <form method="POST" action="<?php echo BASE_URL; ?>?action=resident-account-action" class="d-inline action-form">
                                        <?php echo csrf_input(); ?>
                                        <input type="hidden" name="action_type" value="reset-password">
                                        <input type="hidden" name="user_id" value="<?php echo $rec['user_id']; ?>">
                                        <button type="button" class="btn btn-sm btn-glass text-primary reset-btn" title="Reset Password">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                        </button>
                                    </form>

                                    <!-- Delete -->
                                    <form method="POST" action="<?php echo BASE_URL; ?>?action=resident-account-action" class="d-inline action-form">
                                        <?php echo csrf_input(); ?>
                                        <input type="hidden" name="action_type" value="delete">
                                        <input type="hidden" name="user_id" value="<?php echo $rec['user_id']; ?>">
                                        <button type="button" class="btn btn-sm btn-glass text-danger delete-btn" title="Delete Account">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php if ($total_pages > 1): ?>
        <div class="glass-card-footer">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i === $page_num ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo BASE_URL; ?>admin-registered-residents?p=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($filterStatus); ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--glass-bg); border: 1px solid var(--border-color);">
            <div class="modal-header">
                <h5 class="modal-title">Account Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Email:</dt>
                    <dd class="col-sm-8" id="detail-email"></dd>
                    <dt class="col-sm-4">Linked Patient:</dt>
                    <dd class="col-sm-8" id="detail-patient"></dd>
                    <dt class="col-sm-4">Status:</dt>
                    <dd class="col-sm-8" id="detail-status"></dd>
                    <dt class="col-sm-4">Last Login:</dt>
                    <dd class="col-sm-8" id="detail-lastlogin"></dd>
                    <dt class="col-sm-4">Registered:</dt>
                    <dd class="col-sm-8" id="detail-created"></dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View Details
    document.querySelectorAll('.view-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('detail-email').textContent = this.dataset.email;
            document.getElementById('detail-patient').textContent = this.dataset.patient;
            document.getElementById('detail-status').innerHTML = this.dataset.status === 'active' 
                ? '<span class="badge badge-success">Active</span>' 
                : '<span class="badge badge-danger">Disabled</span>';
            document.getElementById('detail-lastlogin').textContent = this.dataset.lastlogin;
            document.getElementById('detail-created').textContent = this.dataset.created;
            new bootstrap.Modal(document.getElementById('viewDetailsModal')).show();
        });
    });

    // Disable/Enable confirmation
    document.querySelectorAll('.disable-btn, .enable-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const action = this.classList.contains('disable-btn') ? 'disable' : 'enable';
            Swal.fire({
                title: `${action.charAt(0).toUpperCase() + action.slice(1)} Account?`,
                text: `Are you sure you want to ${action} this account?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'disable' ? '#f59e0b' : '#10b981',
                confirmButtonText: `Yes, ${action} it`,
                background: 'rgba(30, 41, 59, 0.95)',
                color: '#ffffff'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // Reset Password confirmation
    document.querySelectorAll('.reset-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Reset Password?',
                text: 'This will generate a new random password. The new password will be displayed after reset.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'Yes, reset it',
                background: 'rgba(30, 41, 59, 0.95)',
                color: '#ffffff'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // Delete confirmation
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Delete Account?',
                text: 'This will permanently delete the portal account. The linked patient record will NOT be deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it',
                background: 'rgba(30, 41, 59, 0.95)',
                color: '#ffffff'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });
});
</script>

<style>
.breadcrumb { background: transparent; padding: 0; margin: 0; font-size: 0.875rem; }
.breadcrumb-item a { color: var(--primary); text-decoration: none; }
dl dt { color: var(--text-muted); font-weight: 500; }
dl dd { color: var(--text-primary); }
</style>

<?php include __DIR__ . '/../../includes/footer_admin.php'; ?>
