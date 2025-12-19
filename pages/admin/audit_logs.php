<?php
/**
 * Audit Logs Page (Admin Only)
 * E-BHM Connect - Glassmorphism Design
 * 
 * View system audit logs
 */
include __DIR__ . '/../../includes/header_admin.php';

// Ensure at least admin role
if (!is_admin()) {
    header('Location: ' . BASE_URL . 'admin-dashboard');
    exit;
}

// Pagination
$page_num = isset($_GET['pg']) && is_numeric($_GET['pg']) ? max(1, (int)$_GET['pg']) : 1;
$per_page = 20;
$offset = ($page_num - 1) * $per_page;

// Filters
$filter_action = $_GET['action_type'] ?? '';
$filter_entity = $_GET['entity'] ?? '';
$filter_user = $_GET['user_id'] ?? '';
$filter_date_from = $_GET['date_from'] ?? '';
$filter_date_to = $_GET['date_to'] ?? '';

// Build filters array
$filters = [];
if ($filter_action) $filters['action'] = $filter_action;
if ($filter_entity) $filters['entity_type'] = $filter_entity;
if ($filter_user) $filters['user_id'] = (int)$filter_user;
if ($filter_date_from) $filters['date_from'] = $filter_date_from;
if ($filter_date_to) $filters['date_to'] = $filter_date_to;

// Get total count for pagination
$total_logs = 0;
try {
    $countSql = "SELECT COUNT(*) FROM audit_logs WHERE 1=1";
    $countParams = [];
    
    if ($filter_action) {
        $countSql .= " AND action = ?";
        $countParams[] = $filter_action;
    }
    if ($filter_entity) {
        $countSql .= " AND entity_type = ?";
        $countParams[] = $filter_entity;
    }
    if ($filter_user) {
        $countSql .= " AND user_id = ?";
        $countParams[] = (int)$filter_user;
    }
    if ($filter_date_from) {
        $countSql .= " AND created_at >= ?";
        $countParams[] = $filter_date_from . ' 00:00:00';
    }
    if ($filter_date_to) {
        $countSql .= " AND created_at <= ?";
        $countParams[] = $filter_date_to . ' 23:59:59';
    }
    
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($countParams);
    $total_logs = $stmt->fetchColumn();
} catch (Throwable $e) {
    error_log('Audit log count error: ' . $e->getMessage());
}

$total_pages = max(1, ceil($total_logs / $per_page));

// Get audit logs
$logs = [];
try {
    $logs = get_audit_logs($per_page, $offset, $filters);
} catch (Throwable $e) {
    error_log('Audit logs fetch error: ' . $e->getMessage());
}

// Get unique action types and entities for filters
$actionTypes = [];
$entityTypes = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT action FROM audit_logs ORDER BY action");
    $actionTypes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $stmt = $pdo->query("SELECT DISTINCT entity_type FROM audit_logs WHERE entity_type IS NOT NULL ORDER BY entity_type");
    $entityTypes = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Throwable $e) {
    // Tables may not exist
}

// Get BHW users for filter dropdown
$bhwUsers = [];
try {
    $stmt = $pdo->query("SELECT id, full_name FROM bhw_users ORDER BY full_name");
    $bhwUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    // Handle error
}

/**
 * Format audit log details from JSON to readable text
 */
function format_audit_details($details) {
    if (empty($details)) {
        return '-';
    }
    
    // Try to decode JSON
    $decoded = json_decode($details, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Not JSON, return as-is
        return htmlspecialchars($details);
    }
    
    // Format as readable key-value pairs
    $output = [];
    foreach ($decoded as $key => $value) {
        // Format the key to be more readable
        $readableKey = ucwords(str_replace(['_', '-'], ' ', $key));
        
        // Format the value
        if (is_array($value)) {
            $value = implode(', ', $value);
        } elseif (is_bool($value)) {
            $value = $value ? 'Yes' : 'No';
        } elseif ($value === null) {
            $value = '-';
        }
        
        $output[] = '<span class="detail-label">' . htmlspecialchars($readableKey) . ':</span> <span class="detail-value">' . htmlspecialchars($value) . '</span>';
    }
    
    return implode('<br>', $output);
}
?>

<div class="container-fluid py-4 fade-in">
    <!-- Page Header -->
    <div class="glass-card mb-4">
        <div class="glass-card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-1"><?php echo __('audit.title'); ?></h1>
                <p class="text-secondary mb-0">Track all system activities and changes</p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge badge-primary"><?php echo number_format($total_logs); ?> records</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="glass-card mb-4">
        <div class="glass-card-body">
            <form method="GET" action="" class="row g-3">
                <input type="hidden" name="page" value="admin-audit-logs">
                
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label small"><?php echo __('audit.action'); ?></label>
                    <select name="action_type" class="form-select">
                        <option value=""><?php echo __('all'); ?></option>
                        <?php foreach ($actionTypes as $type): ?>
                        <option value="<?php echo htmlspecialchars($type); ?>" <?php echo $filter_action === $type ? 'selected' : ''; ?>>
                            <?php echo ucfirst(htmlspecialchars($type)); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label small"><?php echo __('audit.entity'); ?></label>
                    <select name="entity" class="form-select">
                        <option value=""><?php echo __('all'); ?></option>
                        <?php foreach ($entityTypes as $type): ?>
                        <option value="<?php echo htmlspecialchars($type); ?>" <?php echo $filter_entity === $type ? 'selected' : ''; ?>>
                            <?php echo ucfirst(htmlspecialchars($type)); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label small"><?php echo __('audit.user'); ?></label>
                    <select name="user_id" class="form-select">
                        <option value=""><?php echo __('all'); ?></option>
                        <?php foreach ($bhwUsers as $user): ?>
                        <option value="<?php echo $user['id']; ?>" <?php echo $filter_user == $user['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['full_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label small">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($filter_date_from); ?>">
                </div>
                
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label small">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($filter_date_to); ?>">
                </div>
                
                <div class="col-12 col-md-6 col-lg-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary"><?php echo __('filter'); ?></button>
                    <a href="<?php echo BASE_URL; ?>admin-audit-logs" class="btn btn-glass"><?php echo __('clear'); ?></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="glass-card">
        <div class="glass-card-body p-0">
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th><?php echo __('audit.timestamp'); ?></th>
                            <th><?php echo __('audit.user'); ?></th>
                            <th><?php echo __('audit.action'); ?></th>
                            <th><?php echo __('audit.entity'); ?></th>
                            <th><?php echo __('audit.details'); ?></th>
                            <th><?php echo __('audit.ip_address'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5"><?php echo __('no_data'); ?></td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                            <tr>
                                <td>
                                    <div class="small">
                                        <strong><?php echo date('M j, Y', strtotime($log['created_at'])); ?></strong><br>
                                        <span class="text-muted"><?php echo date('g:i:s A', strtotime($log['created_at'])); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm" style="width:32px;height:32px;border-radius:8px;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:600;">
                                            <?php echo strtoupper(substr($log['user_name'] ?? 'SYS', 0, 2)); ?>
                                        </div>
                                        <span><?php echo htmlspecialchars($log['user_name'] ?? 'System'); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $actionClass = match(strtolower($log['action'] ?? '')) {
                                        'login' => 'badge-primary',
                                        'logout' => 'badge-secondary',
                                        'create', 'insert' => 'badge-success',
                                        'update', 'edit' => 'badge-warning',
                                        'delete' => 'badge-danger',
                                        default => 'badge-secondary'
                                    };
                                    ?>
                                    <span class="badge <?php echo $actionClass; ?>"><?php echo ucfirst(htmlspecialchars($log['action'] ?? '')); ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($log['entity_type'])): ?>
                                    <span class="badge badge-glass"><?php echo ucfirst(htmlspecialchars($log['entity_type'])); ?></span>
                                    <?php if (!empty($log['entity_id'])): ?>
                                    <span class="small text-muted">#<?php echo $log['entity_id']; ?></span>
                                    <?php endif; ?>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="details-cell">
                                    <?php echo format_audit_details($log['details']); ?>
                                </td>
                                <td>
                                    <code class="small"><?php echo htmlspecialchars($log['ip_address'] ?? '-'); ?></code>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="glass-card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing <?php echo ($offset + 1); ?>-<?php echo min($offset + $per_page, $total_logs); ?> of <?php echo $total_logs; ?>
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php if ($page_num > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=admin-audit-logs&pg=<?php echo $page_num - 1; ?>&action_type=<?php echo urlencode($filter_action); ?>&entity=<?php echo urlencode($filter_entity); ?>&user_id=<?php echo urlencode($filter_user); ?>&date_from=<?php echo urlencode($filter_date_from); ?>&date_to=<?php echo urlencode($filter_date_to); ?>">‹</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php
                    $start_page = max(1, $page_num - 2);
                    $end_page = min($total_pages, $page_num + 2);
                    
                    for ($i = $start_page; $i <= $end_page; $i++):
                    ?>
                    <li class="page-item <?php echo $i === $page_num ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=admin-audit-logs&pg=<?php echo $i; ?>&action_type=<?php echo urlencode($filter_action); ?>&entity=<?php echo urlencode($filter_entity); ?>&user_id=<?php echo urlencode($filter_user); ?>&date_from=<?php echo urlencode($filter_date_from); ?>&date_to=<?php echo urlencode($filter_date_to); ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page_num < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=admin-audit-logs&pg=<?php echo $page_num + 1; ?>&action_type=<?php echo urlencode($filter_action); ?>&entity=<?php echo urlencode($filter_entity); ?>&user_id=<?php echo urlencode($filter_user); ?>&date_from=<?php echo urlencode($filter_date_from); ?>&date_to=<?php echo urlencode($filter_date_to); ?>">›</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.badge-glass {
    background: rgba(var(--primary-rgb), 0.15);
    color: var(--primary);
}
.badge-success {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
}
.badge-warning {
    background: rgba(245, 158, 11, 0.15);
    color: #f59e0b;
}
.badge-danger {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
}
.pagination .page-link {
    background: rgba(var(--glass-bg-rgb), 0.5);
    border: 1px solid var(--border-color);
    color: var(--text-color);
}
.pagination .page-item.active .page-link {
    background: var(--primary);
    border-color: var(--primary);
    color: #fff;
}
/* Audit details formatting */
.details-cell {
    font-size: 0.85rem;
    max-width: 280px;
}
.details-cell .detail-label {
    color: #64748b;
    font-weight: 600;
}
.details-cell .detail-value {
    color: #1e293b;
}
/* Light mode styles */
[data-theme="light"] .details-cell .detail-label {
    color: #64748b;
}
[data-theme="light"] .details-cell .detail-value {
    color: #1e293b;
}
/* Dark mode styles */
[data-theme="dark"] .details-cell .detail-label {
    color: #94a3b8;
}
[data-theme="dark"] .details-cell .detail-value {
    color: #e2e8f0;
}
</style>

<?php include __DIR__ . '/../../includes/footer_admin.php'; ?>
