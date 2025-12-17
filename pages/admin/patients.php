<?php
// Patient Records Management (Admin)
// Auth enforced by router; header/footer provide layout and SweetAlert
include_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../includes/pagination_helper.php';
require_once __DIR__ . '/../../config/database.php';
?>
<style>
/* Glassmorphism Design - Consistent with inventory/programs */
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
.stat-icon.info { background: rgba(99, 102, 241, 0.2); color: #6366f1; }
.stat-icon.warning { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }

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

.btn-info-glass {
    background: rgba(99, 102, 241, 0.2);
    border: 1px solid rgba(99, 102, 241, 0.3);
    color: #6366f1;
}

.btn-info-glass:hover {
    background: rgba(99, 102, 241, 0.3);
}

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

.actions-cell {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

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

.pagination-container {
    padding: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

@media (max-width: 767px) {
    .glass-table thead { display: none; }
    
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
</style>

<?php
$patients = [];
$pagination = ['current_page' => 1, 'total_pages' => 1, 'total_records' => 0];
$per_page = 10;

$total_patients = 0;
$male_patients = 0;
$female_patients = 0;

try {
    // Get stats
    $statsStmt = $pdo->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN LOWER(sex) = 'male' THEN 1 ELSE 0 END) as male,
        SUM(CASE WHEN LOWER(sex) = 'female' THEN 1 ELSE 0 END) as female
        FROM patients");
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    $total_patients = (int) $stats['total'];
    $male_patients = (int) $stats['male'];
    $female_patients = (int) $stats['female'];

    // Build WHERE conditions
    $where_conditions = [];
    $params = [];

    if (!empty($_GET['search'])) {
        $search_term = '%' . $_GET['search'] . '%';
        $where_conditions[] = "full_name LIKE ?";
        $params[] = $search_term;
    }

    if (!empty($_GET['sex_filter'])) {
        $where_conditions[] = "sex = ?";
        $params[] = $_GET['sex_filter'];
    }

    if (!empty($_GET['age_filter'])) {
        $age_filter = $_GET['age_filter'];
        if ($age_filter === '0-17') {
            $where_conditions[] = "TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) < 18";
        } elseif ($age_filter === '18-59') {
            $where_conditions[] = "TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 18 AND 59";
        } elseif ($age_filter === '60+') {
            $where_conditions[] = "TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 60";
        }
    }

    $where_clause = !empty($where_conditions) ? " WHERE " . implode(" AND ", $where_conditions) : "";

    // Count total records first
    $count_sql = "SELECT COUNT(*) FROM patients" . $where_clause;
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_records = (int) $count_stmt->fetchColumn();

    // Calculate pagination
    $current_page = isset($_GET['pg']) ? max(1, (int) $_GET['pg']) : 1;
    $pagination = paginate($total_records, $per_page, $current_page);

    // Base SQL query with LIMIT
    $sql = "SELECT * FROM patients" . $where_clause;
    $sql .= " ORDER BY full_name ASC LIMIT " . $pagination['per_page'] . " OFFSET " . $pagination['offset'];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    error_log('Patients query error: ' . $e->getMessage());
}
?>

<div class="container">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Patient Records</h1>
            <p class="page-subtitle">Manage patient information and medical records</p>
        </div>
        <a href="<?php echo BASE_URL; ?>admin-patient-form" class="btn-primary-glass">
            <i class="fas fa-user-plus"></i>
            Add New Patient
        </a>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="glass-card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-value"><?php echo $total_patients; ?></div>
            <div class="stat-label">Total Patients</div>
        </div>
        <div class="glass-card stat-card">
            <div class="stat-icon info">
                <i class="fas fa-male"></i>
            </div>
            <div class="stat-value"><?php echo $male_patients; ?></div>
            <div class="stat-label">Male</div>
        </div>
        <div class="glass-card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-female"></i>
            </div>
            <div class="stat-value"><?php echo $female_patients; ?></div>
            <div class="stat-label">Female</div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="glass-card filter-bar">
        <form method="GET" action="">
            <input type="hidden" name="page" value="admin-patients">
            <div class="filter-row">
                <input type="text" name="search" class="glass-input" style="flex: 2;" placeholder="🔍 Search patients by name..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                
                <select name="sex_filter" class="glass-input" style="flex: 1;">
                    <option value="">👥 All Genders</option>
                    <option value="Male" <?php echo ($_GET['sex_filter'] ?? '') === 'Male' ? 'selected' : ''; ?>>👨 Male</option>
                    <option value="Female" <?php echo ($_GET['sex_filter'] ?? '') === 'Female' ? 'selected' : ''; ?>>👩 Female</option>
                </select>

                <select name="age_filter" class="glass-input" style="flex: 1;">
                    <option value="">🎂 All Ages</option>
                    <option value="0-17" <?php echo ($_GET['age_filter'] ?? '') === '0-17' ? 'selected' : ''; ?>>👶 0-17 years</option>
                    <option value="18-59" <?php echo ($_GET['age_filter'] ?? '') === '18-59' ? 'selected' : ''; ?>>👤 18-59 years</option>
                    <option value="60+" <?php echo ($_GET['age_filter'] ?? '') === '60+' ? 'selected' : ''; ?>>👴 60+ years (Senior)</option>
                </select>
                
                <button type="submit" class="btn-primary-glass">
                    <i class="fas fa-filter"></i>
                    Filter
                </button>
                <a href="<?php echo BASE_URL; ?>?page=admin-patients" class="btn-secondary-glass">
                    <i class="fas fa-times"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Patients Table -->
    <div class="glass-card table-container">
        <div class="table-responsive">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Birthdate</th>
                        <th>Sex</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($patients)): ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-user-friends"></i>
                                    </div>
                                    <p>No patients found</p>
                                    <a href="<?php echo BASE_URL; ?>admin-patient-form" class="btn-primary-glass">Add First Patient</a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($patients as $patient): ?>
                            <tr>
                                <td data-label="Name"><?php echo htmlspecialchars($patient['full_name'] ?? ''); ?></td>
                                <td data-label="Address"><?php echo htmlspecialchars($patient['address'] ?? ''); ?></td>
                                <td data-label="Birthdate"><?php echo htmlspecialchars($patient['birthdate'] ?? ''); ?></td>
                                <td data-label="Sex"><?php echo htmlspecialchars($patient['sex'] ?? ''); ?></td>
                                <td data-label="Contact"><?php echo htmlspecialchars($patient['contact'] ?? ''); ?></td>
                                <td data-label="Actions">
                                    <div class="actions-cell">
                                        <a href="<?php echo BASE_URL; ?>admin-patient-view?id=<?php echo urlencode($patient['patient_id'] ?? ''); ?>" class="btn-info-glass btn-sm-glass">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>admin-patient-form?id=<?php echo urlencode($patient['patient_id'] ?? ''); ?>" class="btn-secondary-glass btn-sm-glass">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="?action=delete-patient" method="POST" class="d-inline" onsubmit="return confirmDelete(event);">
                                            <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($patient['patient_id'] ?? ''); ?>">
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

<script>
function confirmDelete(event) {
    event.preventDefault();
    Swal.fire({
        title: 'Delete Patient?',
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
// Flash messages
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

