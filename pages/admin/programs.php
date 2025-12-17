<?php
// Health Programs Management (Admin)
// Auth enforced by router; header/footer provide layout and SweetAlert
include_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/pagination_helper.php';
?>
<style>
/* ============================================
   GLASSMORPHISM PROGRAMS PAGE STYLES
   Following design-system.md guidelines
   ============================================ */

/* Glass Card Base */
.glass-card {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.glass-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.16);
}

/* Page Header */
.page-header {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 24px;
}

@media (min-width: 768px) {
    .page-header {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
}

.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
}

.page-subtitle {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.875rem;
    margin-top: 4px;
}

/* Stats Row */
.stats-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

@media (min-width: 768px) {
    .stats-row {
        grid-template-columns: repeat(3, 1fr);
    }
}

.stat-card {
    padding: 20px;
    text-align: center;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    font-size: 1.25rem;
}

.stat-icon.primary { background: rgba(32, 201, 151, 0.2); color: #20c997; }
.stat-icon.warning { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.stat-icon.info { background: rgba(99, 102, 241, 0.2); color: #6366f1; }

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1;
}

.stat-label {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-top: 4px;
}

/* Search & Filter Bar */
.filter-bar {
    padding: 20px;
    margin-bottom: 24px;
}

.filter-row {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

@media (min-width: 768px) {
    .filter-row {
        flex-direction: row;
        align-items: center;
    }
}

/* Glass Input Styling */
.glass-input {
    width: 100%;
    padding: 12px 16px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    color: #ffffff;
    font-size: 1rem;
    transition: all 0.25s ease;
}

.glass-input:hover {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.25);
}

.glass-input:focus {
    outline: none;
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid #20c997;
    box-shadow: 0 0 0 4px rgba(32, 201, 151, 0.15);
}

.glass-input::placeholder {
    color: rgba(255, 255, 255, 0.4);
}

/* Button Styles */
.btn-primary-glass {
    padding: 12px 24px;
    background: linear-gradient(135deg, #20c997, #0f5132);
    border: none;
    border-radius: 12px;
    color: #ffffff;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 4px 16px rgba(32, 201, 151, 0.35);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-primary-glass:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(32, 201, 151, 0.45);
    color: #ffffff;
}

.btn-secondary-glass {
    padding: 12px 24px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    color: #ffffff;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.25s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-secondary-glass:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}

.btn-sm-glass {
    padding: 8px 16px;
    font-size: 0.75rem;
    border-radius: 8px;
}

.btn-danger-glass {
    background: rgba(239, 68, 68, 0.2);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
}

.btn-danger-glass:hover {
    background: rgba(239, 68, 68, 0.3);
}

/* Table Styling */
.table-container {
    padding: 0;
    overflow: hidden;
}

.glass-table {
    width: 100%;
    border-collapse: collapse;
}

.glass-table thead th {
    padding: 16px 20px;
    text-align: left;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: rgba(255, 255, 255, 0.7);
    background: rgba(255, 255, 255, 0.05);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.glass-table tbody td {
    padding: 16px 20px;
    color: #ffffff;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    vertical-align: middle;
}

.glass-table tbody tr {
    transition: background 0.15s ease;
}

.glass-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.05);
}

.glass-table tbody tr:last-child td {
    border-bottom: none;
}

/* Program Cell */
.program-cell {
    display: flex;
    flex-direction: column;
}

.program-name {
    font-weight: 500;
    color: #ffffff;
}

.program-dates {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.5);
    margin-top: 4px;
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: capitalize;
}

.status-badge.active {
    background: rgba(32, 201, 151, 0.2);
    color: #20c997;
}

.status-badge.completed {
    background: rgba(99, 102, 241, 0.2);
    color: #6366f1;
}

.status-badge.planned {
    background: rgba(245, 158, 11, 0.2);
    color: #f59e0b;
}

.status-badge.cancelled {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
}

/* Actions Cell */
.actions-cell {
    display: flex;
    gap: 8px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 48px 24px;
    color: rgba(255, 255, 255, 0.5);
}

.empty-state-icon {
    font-size: 3rem;
    margin-bottom: 16px;
    opacity: 0.5;
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    padding: 16px;
}

.modal-overlay.active {
    opacity: 1;
    visibility: visible;
}

.modal-content {
    background: rgba(30, 41, 59, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 24px;
    width: 100%;
    max-width: 560px;
    max-height: 90vh;
    overflow-y: auto;
    transform: translateY(20px) scale(0.95);
    transition: all 0.3s ease;
    box-shadow: 0 24px 64px rgba(0, 0, 0, 0.4);
}

.modal-overlay.active .modal-content {
    transform: translateY(0) scale(1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 28px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #ffffff;
    margin: 0;
}

.modal-close {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: rgba(255, 255, 255, 0.7);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
}

.modal-body {
    padding: 28px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 20px 28px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

/* Form Styles */
.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.875rem;
    margin-bottom: 8px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
}

@media (min-width: 480px) {
    .form-row {
        grid-template-columns: 1fr 1fr;
    }
}

/* Responsive Table */
@media (max-width: 767px) {
    .glass-table thead {
        display: none;
    }
    
    .glass-table tbody tr {
        display: block;
        padding: 16px;
        margin-bottom: 12px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 12px;
    }
    
    .glass-table tbody td {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .glass-table tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    
    .glass-table tbody td:last-child {
        border-bottom: none;
    }
    
    .actions-cell {
        justify-content: flex-end;
    }
}

/* Pagination Container */
.pagination-container {
    padding: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

/* Description truncation */
.description-cell {
    max-width: 250px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
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
        <button class="btn-primary-glass" onclick="openModal()">
            <i class="fas fa-plus"></i>
            Add New Program
        </button>
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
                                    <button class="btn-primary-glass" onclick="openModal()">Create First Program</button>
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
                                    <div class="actions-cell">
                                        <a href="<?php echo BASE_URL; ?>admin-program-edit?id=<?php echo $p['program_id']; ?>" class="btn-secondary-glass btn-sm-glass">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo BASE_URL; ?>?action=delete-program" method="POST" class="d-inline" onsubmit="return confirmDelete(event);">
                                            <input type="hidden" name="program_id" value="<?php echo htmlspecialchars($p['program_id'] ?? ''); ?>">
                                            <button type="submit" class="btn-danger-glass btn-sm-glass">
                                                <i class="fas fa-trash"></i>
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
            event.target.submit();
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
