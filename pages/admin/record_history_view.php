<?php
/**
 * Record Change History Viewer
 * Superadmin-only page to view full modification history
 * E-BHM Connect - Glassmorphism Design
 */
include __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../includes/record_history_helper.php';
require_superadmin();

// Get parameters
$tableName = $_GET['table'] ?? '';
$recordId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$viewMode = $_GET['mode'] ?? 'all'; // 'all' or 'record'

// Get change history
$changes = [];
$recordInfo = null;

if ($viewMode === 'record' && $tableName && $recordId) {
    // View history for a specific record
    $changes = get_record_history($pdo, $tableName, $recordId, 100);
    
    // Get current record info if it exists
    try {
        $pkMap = [
            'pregnancy_tracking' => 'pregnancy_id',
            'child_care_records' => 'child_care_id',
            'natality_records' => 'natality_id',
            'mortality_records' => 'mortality_id',
            'chronic_disease_masterlist' => 'chronic_id',
            'ntp_client_monitoring' => 'ntp_id',
            'wra_tracking' => 'wra_id',
            'patients' => 'patient_id'
        ];
        if (isset($pkMap[$tableName])) {
            $pk = $pkMap[$tableName];
            $stmt = $pdo->prepare("SELECT * FROM `$tableName` WHERE `$pk` = ?");
            $stmt->execute([$recordId]);
            $recordInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {}
} else {
    // View all recent changes
    try {
        $stmt = $pdo->prepare("SELECT rc.*, b.full_name as changed_by_name 
            FROM record_changes rc
            LEFT JOIN bhw_users b ON rc.changed_by = b.bhw_id
            ORDER BY rc.changed_at DESC
            LIMIT 100");
        $stmt->execute();
        $changes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Table may not exist yet
    }
}
?>

<div class="container-fluid py-4 fade-in">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin-dashboard">Dashboard</a></li>
                    <?php if ($viewMode === 'record'): ?>
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin-record-history">Change History</a></li>
                    <li class="breadcrumb-item active">Record Details</li>
                    <?php else: ?>
                    <li class="breadcrumb-item active">Change History</li>
                    <?php endif; ?>
                </ol>
            </nav>
            <h1 class="h3 mb-0" style="color: #8b5cf6;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2" style="vertical-align: -4px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <?php if ($viewMode === 'record' && $tableName): ?>
                    <?php echo get_table_display_name($tableName); ?> History
                <?php else: ?>
                    Record Change History
                <?php endif; ?>
            </h1>
            <p class="text-muted mb-0 small">View all modifications to health records</p>
        </div>
        <?php if ($viewMode === 'record'): ?>
        <a href="<?php echo BASE_URL; ?>admin-record-history" class="btn btn-glass">← All Changes</a>
        <?php endif; ?>
    </div>

    <?php if ($viewMode === 'record' && $recordInfo): ?>
    <!-- Record Summary Card -->
    <div class="glass-card mb-4">
        <div class="glass-card-header">
            <h5 class="glass-card-title mb-0">Current Record Data</h5>
        </div>
        <div class="glass-card-body">
            <div class="row">
                <?php 
                $displayFields = array_slice($recordInfo, 0, 6);
                foreach ($displayFields as $key => $value): 
                    if (in_array($key, ['created_at', 'updated_at', 'password_hash'])) continue;
                ?>
                <div class="col-md-4 mb-2">
                    <small class="text-muted"><?php echo format_field_name($key); ?></small>
                    <div class="fw-medium"><?php echo htmlspecialchars($value ?? '-'); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Changes Table -->
    <div class="glass-card">
        <div class="glass-card-header">
            <h5 class="glass-card-title mb-0">
                <?php echo $viewMode === 'record' ? 'Modification History' : 'Recent Changes (Last 100)'; ?>
            </h5>
        </div>
        <div class="glass-card-body p-0">
            <?php if (empty($changes)): ?>
            <div class="text-center py-5 text-muted">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mb-3 opacity-50"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <p class="mb-0">No change history found</p>
                <small>Changes will appear here once records are modified after running the migration.</small>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <?php if ($viewMode !== 'record'): ?>
                            <th>Record Type</th>
                            <th>Record ID</th>
                            <?php endif; ?>
                            <th>Action</th>
                            <th>Changed By</th>
                            <th>Date/Time</th>
                            <th>Changes</th>
                            <?php if ($viewMode !== 'record'): ?>
                            <th>View</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($changes as $change): ?>
                        <?php $actionDisplay = get_action_display($change['action']); ?>
                        <tr>
                            <?php if ($viewMode !== 'record'): ?>
                            <td><?php echo get_table_display_name($change['table_name']); ?></td>
                            <td><span class="badge badge-secondary">#<?php echo $change['record_id']; ?></span></td>
                            <?php endif; ?>
                            <td>
                                <span class="badge <?php echo $actionDisplay['class']; ?>">
                                    <?php echo $actionDisplay['label']; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($change['changed_by_name'] ?? 'System'); ?></td>
                            <td>
                                <span title="<?php echo $change['changed_at']; ?>">
                                    <?php echo date('M j, Y g:ia', strtotime($change['changed_at'])); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                if ($change['action'] === 'insert'): ?>
                                    <span class="text-success small">New record created</span>
                                <?php elseif ($change['action'] === 'delete'): ?>
                                    <span class="text-danger small">Record deleted</span>
                                <?php else:
                                    $fieldChanges = compare_record_changes($change['old_values'], $change['new_values']);
                                    if (!empty($fieldChanges)):
                                ?>
                                <button type="button" class="btn btn-sm btn-glass view-changes-btn" 
                                        data-changes='<?php echo htmlspecialchars(json_encode($fieldChanges)); ?>'>
                                    <?php echo count($fieldChanges); ?> field(s)
                                </button>
                                <?php else: ?>
                                    <span class="text-muted small">No field changes</span>
                                <?php endif; endif; ?>
                            </td>
                            <?php if ($viewMode !== 'record'): ?>
                            <td>
                                <a href="<?php echo BASE_URL; ?>admin-record-history?mode=record&table=<?php echo urlencode($change['table_name']); ?>&id=<?php echo $change['record_id']; ?>" 
                                   class="btn btn-sm btn-glass" title="View record history">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Changes Modal -->
<div class="modal fade" id="changesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background: var(--glass-bg); border: 1px solid var(--border-color);">
            <div class="modal-header">
                <h5 class="modal-title">Field Changes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Old Value</th>
                                <th>New Value</th>
                            </tr>
                        </thead>
                        <tbody id="changes-tbody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.view-changes-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const changes = JSON.parse(this.dataset.changes);
            const tbody = document.getElementById('changes-tbody');
            tbody.innerHTML = '';
            
            changes.forEach(change => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="fw-medium">${change.field}</td>
                    <td class="text-danger"><del>${change.old || '-'}</del></td>
                    <td class="text-success">${change.new || '-'}</td>
                `;
                tbody.appendChild(row);
            });
            
            new bootstrap.Modal(document.getElementById('changesModal')).show();
        });
    });
});
</script>

<style>
.breadcrumb { background: transparent; padding: 0; margin: 0; font-size: 0.875rem; }
.breadcrumb-item a { color: var(--primary); text-decoration: none; }
</style>

<?php include __DIR__ . '/../../includes/footer_admin.php'; ?>
