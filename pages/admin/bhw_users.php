<?php
// BHW User Management (Admin)
include_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../includes/pagination_helper.php';
?>
<style>
/* Copy all glassmorphism styles inline */
.glass-card { background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 16px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); transition: transform 0.25s ease, box-shadow 0.25s ease; }
.glass-card:hover { transform: translateY(-4px); box-shadow: 0 16px 48px rgba(0, 0, 0, 0.16); }
.page-header { display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px; }
@media (min-width: 768px) { .page-header { flex-direction: row; justify-content: space-between; align-items: center; } }
.page-title { font-size: 1.75rem; font-weight: 700; color: #ffffff; margin: 0; }
.page-subtitle { color: rgba(255, 255, 255, 0.6); font-size: 0.875rem; margin-top: 4px; }
.stats-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 24px; }
@media (min-width: 768px) { .stats-row { grid-template-columns: repeat(3, 1fr); } }
.stat-card { padding: 20px; text-align: center; }
.stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 1.25rem; }
.stat-icon.primary { background: rgba(32, 201, 151, 0.2); color: #20c997; }
.stat-icon.info { background: rgba(99, 102, 241, 0.2); color: #6366f1; }
.stat-icon.warning { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.stat-value { font-size: 1.75rem; font-weight: 700; color: #ffffff; line-height: 1; }
.stat-label { color: rgba(255, 255, 255, 0.6); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px; }
.filter-bar { padding: 20px; margin-bottom: 24px; }
.filter-row { display: flex; flex-direction: column; gap: 12px; }
@media (min-width: 768px) { .filter-row { flex-direction: row; align-items: center; } }
.glass-input { width: 100%; padding: 12px 16px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 12px; color: #ffffff; font-size: 1rem; transition: all 0.25s ease; }
.glass-input:hover { background: rgba(255, 255, 255, 0.12); border-color: rgba(255, 255, 255, 0.25); }
.glass-input:focus { outline: none; background: rgba(255, 255, 255, 0.15); border: 2px solid #20c997; box-shadow: 0 0 0 4px rgba(32, 201, 151, 0.15); }
.glass-input::placeholder { color: rgba(255, 255, 255, 0.4); }
.btn-primary-glass { padding: 12px 24px; background: linear-gradient(135deg, #20c997, #0f5132); border: none; border-radius: 12px; color: #ffffff; font-weight: 600; font-size: 0.875rem; cursor: pointer; transition: all 0.25s ease; box-shadow: 0 4px 16px rgba(32, 201, 151, 0.35); display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
.btn-primary-glass:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(32, 201, 151, 0.45); color: #ffffff; }
.btn-secondary-glass { padding: 12px 24px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 12px; color: #ffffff; font-weight: 500; cursor: pointer; transition: all 0.25s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
.btn-secondary-glass:hover { background: rgba(255, 255, 255, 0.15); border-color: rgba(255, 255, 255, 0.25); color: #ffffff; }
.btn-sm-glass { padding: 8px 16px; font-size: 0.75rem; border-radius: 8px; }
.table-container { padding: 0; overflow: hidden; }
.glass-table { width: 100%; border-collapse: collapse; }
.glass-table thead th { padding: 16px 20px; text-align: left; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: rgba(255, 255, 255, 0.7); background: rgba(255, 255, 255, 0.05); border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
.glass-table tbody td { padding: 16px 20px; color: #ffffff; border-bottom: 1px solid rgba(255, 255, 255, 0.05); vertical-align: middle; }
.glass-table tbody tr { transition: background 0.15s ease; }
.glass-table tbody tr:hover { background: rgba(255, 255, 255, 0.05); }
.glass-table tbody tr:last-child td { border-bottom: none; }
.actions-cell { display: flex; gap: 8px; flex-wrap: wrap; }
.empty-state { text-align: center; padding: 48px 24px; color: rgba(255, 255, 255, 0.5); }
.empty-state-icon { font-size: 3rem; margin-bottom: 16px; opacity: 0.5; }
.pagination-container { padding: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1); }
.status-badge { display: inline-flex; align-items: center; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.75rem; text-transform: capitalize; }
.status-badge.active { background: rgba(32, 201, 151, 0.2); color: #20c997; }
.status-badge.planned { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
@media (max-width: 767px) { .glass-table thead { display: none; } .glass-table tbody tr { display: block; padding: 16px; margin-bottom: 12px; background: rgba(255, 255, 255, 0.03); border-radius: 12px; } .glass-table tbody td { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.05); } .glass-table tbody td::before { content: attr(data-label); font-weight: 600; color: rgba(255, 255, 255, 0.6); font-size: 0.75rem; text-transform: uppercase; } .glass-table tbody td:last-child { border-bottom: none; } .actions-cell { justify-content: flex-end; } }
</style>
<?php

$bhw_users = [];
$pagination = ['current_page' => 1, 'total_pages' => 1, 'total_records' => 0];
$per_page = 10;

$total_bhws = 0;
$verified_bhws = 0;
$pending_bhws = 0;


try {
    // Get stats
    $statsStmt = $pdo->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN email_verified = 1 THEN 1 ELSE 0 END) as verified,
        SUM(CASE WHEN email_verified = 0 THEN 1 ELSE 0 END) as pending
        FROM bhw_users");
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    $total_bhws = (int) $stats['total'];
    $verified_bhws = (int) $stats['verified'];
    $pending_bhws = (int) $stats['pending'];

    // Count total records
    $count_sql = 'SELECT COUNT(*) FROM bhw_users';
    $params = [];

    if (!empty($_GET['search'])) {
        $search_term = '%' . $_GET['search'] . '%';
        $count_sql .= ' WHERE full_name LIKE ?';
        $params[] = $search_term;
    }
    
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_records = (int) $count_stmt->fetchColumn();

    // Calculate pagination
    $current_page = isset($_GET['pg']) ? max(1, (int) $_GET['pg']) : 1;
    $pagination = paginate($total_records, $per_page, $current_page);

    // Base SQL
    $sql = 'SELECT bhw_id, full_name, username, bhw_unique_id, assigned_area, email_verified FROM bhw_users';
    $params = [];

    if (!empty($_GET['search'])) {
        $search_term = '%' . $_GET['search'] . '%';
        $sql .= ' WHERE full_name LIKE ?';
        $params[] = $search_term;
    }

    $sql .= ' ORDER BY full_name ASC LIMIT ' . $pagination['per_page'] . ' OFFSET ' . $pagination['offset'];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $bhw_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('BHW users fetch error: ' . $e->getMessage());
}
?>

<div class="container">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">BHW User Management</h1>
            <p class="page-subtitle">Manage Barangay Health Worker accounts</p>
        </div>
        <a href="<?php echo BASE_URL; ?>register-bhw" class="btn-primary-glass">
            <i class="fas fa-user-plus"></i>
            Add New BHW
        </a>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="glass-card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="stat-value"><?php echo $total_bhws; ?></div>
            <div class="stat-label">Total BHWs</div>
        </div>
        <div class="glass-card stat-card">
            <div class="stat-icon info">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value"><?php echo $verified_bhws; ?></div>
            <div class="stat-label">Verified</div>
        </div>
        <div class="glass-card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value"><?php echo $pending_bhws; ?></div>
            <div class="stat-label">Pending</div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="glass-card filter-bar">
        <form method="GET" action="">
            <div class="filter-row">
                <input type="text" name="search" class="glass-input" style="flex: 1;" placeholder="Search BHW by name..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <button type="submit" class="btn-primary-glass">
                    <i class="fas fa-search"></i>
                    Search
                </button>
                <a href="<?php echo BASE_URL; ?>admin-bhw-users" class="btn-secondary-glass">
                    <i class="fas fa-times"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- BHW Users Table -->
    <div class="glass-card table-container">
        <div class="table-responsive">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>BHW ID</th>
                        <th>Assigned Area</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bhw_users)): ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-user-md"></i>
                                    </div>
                                    <p>No BHW users found</p>
                                    <a href="<?php echo BASE_URL; ?>register-bhw" class="btn-primary-glass">Add First BHW</a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bhw_users as $bhw): ?>
                            <tr>
                                <td data-label="Full Name"><?php echo htmlspecialchars($bhw['full_name'] ?? ''); ?></td>
                                <td data-label="Username"><?php echo htmlspecialchars($bhw['username'] ?? ''); ?></td>
                                <td data-label="BHW ID"><?php echo htmlspecialchars($bhw['bhw_unique_id'] ?? ''); ?></td>
                                <td data-label="Assigned Area"><?php echo htmlspecialchars($bhw['assigned_area'] ?? ''); ?></td>
                                <td data-label="Status">
                                    <?php if ($bhw['email_verified'] == 1): ?>
                                        <span class="status-badge active">Verified</span>
                                    <?php else: ?>
                                        <span class="status-badge planned">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Actions">
                                    <div class="actions-cell">
                                        <a href="<?php echo BASE_URL; ?>admin-bhw-edit?id=<?php echo $bhw['bhw_id']; ?>" class="btn-secondary-glass btn-sm-glass">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="pagination-container">
            <?php echo render_pagination($pagination, get_pagination_base_url()); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
