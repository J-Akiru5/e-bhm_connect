<?php
// Health Programs Management (Admin)
// Auth enforced by router; header/footer provide layout and SweetAlert
include_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/pagination_helper.php';
?>
<style>
</style>

<?php
$programs = [];
$pagination = ['current_page' => 1, 'total_pages' => 1, 'total_records' => 0];
$per_page = 10;

// Stats
$total_programs = 0;
$active_programs = 0;
$completed_programs = 0;

try {
    // Get stats
    $statsStmt = $pdo->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN LOWER(status) = 'active' THEN 1 ELSE 0 END) as active,
        SUM(CASE WHEN LOWER(status) = 'completed' THEN 1 ELSE 0 END) as completed
        FROM health_programs");
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    $total_programs = (int) $stats['total'];
    $active_programs = (int) $stats['active'];
    $completed_programs = (int) $stats['completed'];

    // Count total records first
    $count_sql = 'SELECT COUNT(*) FROM health_programs';
    $params = [];

    if (!empty($_GET['search'])) {
        $search_term = '%' . $_GET['search'] . '%';
        $count_sql .= ' WHERE program_name LIKE ?';
        $params[] = $search_term;
    }
    
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_records = (int) $count_stmt->fetchColumn();

    // Calculate pagination
    $current_page = isset($_GET['pg']) ? max(1, (int) $_GET['pg']) : 1;
    $pagination = paginate($total_records, $per_page, $current_page);

    // Base SQL for programs
    $sql = 'SELECT * FROM health_programs';
    $params = [];

    if (!empty($_GET['search'])) {
        $search_term = '%' . $_GET['search'] . '%';
        $sql .= ' WHERE program_name LIKE ?';
        $params[] = $search_term;
    }

    $sql .= ' ORDER BY start_date DESC LIMIT ' . $pagination['per_page'] . ' OFFSET ' . $pagination['offset'];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Programs fetch error: ' . $e->getMessage());
}
?>

<div class="container">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Health Programs</h1>
            <p class="page-subtitle">Monitor and manage health program initiatives</p>
        </div>
        <?php if (has_permission('manage_programs')): ?>
        <button class="btn-primary-glass" onclick="openModal()">
            <i class="fas fa-plus"></i>
            Add New Program
        </button>
        <?php endif; ?>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="glass-card stat-card">
            <div class="stat-icon info">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-value"><?php echo $total_programs; ?></div>
            <div class="stat-label">Total Programs</div>
        </div>
        <div class="glass-card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stat-value"><?php echo $active_programs; ?></div>
            <div class="stat-label">Active</div>
        </div>
        <div class="glass-card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value"><?php echo $completed_programs; ?></div>
            <div class="stat-label">Completed</div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="glass-card filter-bar">
        <form method="GET" action="">
            <div class="filter-row">
                <input type="text" name="search" class="glass-input" style="flex: 1;" placeholder="Search programs by name..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <button type="submit" class="btn-primary-glass">
                    <i class="fas fa-search"></i>
                    Search
                </button>
                <a href="<?php echo BASE_URL; ?>admin-programs" class="btn-secondary-glass">
                    <i class="fas fa-times"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Programs Table -->
    <div class="glass-card table-container">
        <div class="table-responsive">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th>Program</th>
                        <th>Description</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($programs)): ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <p>No health programs found</p>
                                    <?php if (has_permission('manage_programs')): ?>
                                    <button class="btn-primary-glass" onclick="openModal()">Create First Program</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($programs as $p): ?>
                            <?php
                                $status = strtolower($p['status'] ?? 'active');
                                $statusClass = 'active';
                                if ($status === 'completed') $statusClass = 'completed';
                                elseif ($status === 'planned') $statusClass = 'planned';
                                elseif ($status === 'cancelled') $statusClass = 'cancelled';
                            ?>
                            <tr>
                                <td data-label="Program">
                                    <div class="program-cell">
                                        <span class="program-name"><?php echo htmlspecialchars($p['program_name'] ?? ''); ?></span>
                                    </div>
                                </td>
                                <td data-label="Description" class="description-cell" title="<?php echo htmlspecialchars($p['description'] ?? ''); ?>">
                                    <?php echo htmlspecialchars($p['description'] ?? '-'); ?>
                                </td>
                                <td data-label="Start Date"><?php echo htmlspecialchars($p['start_date'] ?? '-'); ?></td>
                                <td data-label="End Date"><?php echo htmlspecialchars($p['end_date'] ?? '-'); ?></td>
                                <td data-label="Status">
                                    <span class="status-badge <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($p['status'] ?? 'Active'); ?>
                                    </span>
                                </td>
                                <td data-label="Actions">
                                    <?php if (has_permission('manage_programs')): ?>
                                    <div class="actions-cell">
                                        <a href="<?php echo BASE_URL; ?>admin-program-edit?id=<?php echo $p['program_id']; ?>" class="btn-secondary-glass btn-sm-glass">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo BASE_URL; ?>?action=delete-program" method="POST" class="d-inline" onsubmit="return confirmDelete(event);">
                                            <?php echo csrf_input(); ?>
                                            <input type="hidden" name="program_id" value="<?php echo htmlspecialchars($p['program_id'] ?? ''); ?>">
                                            <button type="submit" class="btn-danger-glass btn-sm-glass">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <?php else: ?>
                                    <span class="text-muted">View Only</span>
                                    <?php endif; ?>
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

<!-- Add Program Modal -->
<div class="modal-overlay" id="addProgramModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-plus-circle me-2" style="color: #20c997;"></i>
                Add New Health Program
            </h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="post" action="<?php echo BASE_URL; ?>?action=save-program">
            <div class="modal-body">
                <?php echo csrf_input(); ?>
                <div class="form-group">
                    <label class="form-label">Program Name *</label>
                    <input type="text" name="program_name" class="glass-input" required placeholder="Enter program name">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="glass-input" rows="4" placeholder="Enter program description and objectives"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="glass-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="glass-input">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="glass-input">
                        <option value="Active">Active</option>
                        <option value="Planned">Planned</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary-glass" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-primary-glass">
                    <i class="fas fa-save"></i>
                    Save Program
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Modal Functions
function openModal() {
    document.getElementById('addProgramModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('addProgramModal').classList.remove('active');
    document.body.style.overflow = '';
}

// Close modal on outside click
document.getElementById('addProgramModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Delete confirmation
function confirmDelete(event) {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;
    Swal.fire({
        title: 'Delete Program?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
        background: 'rgba(30, 41, 59, 0.95)',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}
</script>

<?php
// Flash messages via SweetAlert2
if (isset($_SESSION['form_success'])) {
    $msg = json_encode($_SESSION['form_success']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'success', title: 'Success', text: $msg, background: 'rgba(30, 41, 59, 0.95)', color: '#ffffff'}); } });</script>";
    unset($_SESSION['form_success']);
}
if (isset($_SESSION['form_error'])) {
    $emsg = json_encode($_SESSION['form_error']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'error', title: 'Error', text: $emsg, background: 'rgba(30, 41, 59, 0.95)', color: '#ffffff'}); } });</script>";
    unset($_SESSION['form_error']);
}

include_once __DIR__ . '/../../includes/footer_admin.php';
?>
