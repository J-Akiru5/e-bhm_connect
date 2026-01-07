<?php
/**
 * Natality (Birth) Records Page
 * E-BHM Connect - Glassmorphism Design
 */
include __DIR__ . '/../../../includes/header_admin.php';

$action = $_GET['action'] ?? 'list';
$editId = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Fetch mothers (female patients) for dropdown
$mothers = [];
try {
    $stmt = $pdo->query("SELECT patient_id, full_name FROM patients WHERE sex = 'Female' OR sex IS NULL ORDER BY full_name ASC");
    $mothers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching patients: " . $e->getMessage());
}

// Fetch BHWs
$bhws = [];
try {
    $stmt = $pdo->query("SELECT bhw_id, full_name FROM bhw_users ORDER BY full_name ASC");
    $bhws = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching BHWs: " . $e->getMessage());
}

// Fetch record for editing
$record = null;
if ($editId && in_array($action, ['edit', 'view'])) {
    try {
        $stmt = $pdo->prepare("SELECT nr.*, p.full_name as mother_name, b.full_name as bhw_name 
                               FROM natality_records nr
                               LEFT JOIN patients p ON nr.mother_patient_id = p.patient_id
                               LEFT JOIN bhw_users b ON nr.bhw_id = b.bhw_id
                               WHERE nr.natality_id = ?");
        $stmt->execute([$editId]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching record: " . $e->getMessage());
    }
}

// Pagination
$page_num = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$per_page = 15;
$offset = ($page_num - 1) * $per_page;

$records = [];
$total_records = 0;
$search = $_GET['search'] ?? '';
$filterYear = $_GET['year'] ?? '';

if ($action === 'list') {
    try {
        $whereConditions = [];
        $params = [];
        
        if ($search) {
            $whereConditions[] = "(baby_complete_name LIKE ? OR mother_complete_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($filterYear) {
            $whereConditions[] = "YEAR(date_of_birth) = ?";
            $params[] = $filterYear;
        }
        
        $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $countSql = "SELECT COUNT(*) FROM natality_records $whereClause";
        $stmt = $pdo->prepare($countSql);
        $stmt->execute($params);
        $total_records = (int)$stmt->fetchColumn();
        
        $sql = "SELECT nr.*, b.full_name as bhw_name 
                FROM natality_records nr
                LEFT JOIN bhw_users b ON nr.bhw_id = b.bhw_id
                $whereClause
                ORDER BY nr.date_of_birth DESC
                LIMIT $per_page OFFSET $offset";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching records: " . $e->getMessage());
    }
}

$total_pages = ceil($total_records / $per_page);

// Get available years for filter
$years = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT YEAR(date_of_birth) as year FROM natality_records ORDER BY year DESC");
    $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {}
?>

<div class="container-fluid py-4 fade-in">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin-health-records">Health Records</a></li>
                    <li class="breadcrumb-item active">Natality Records</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0" style="color: #10b981;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2" style="vertical-align: -4px;">
                    <path d="M4 11a9 9 0 0 1 9 9"/><path d="M4 4a16 16 0 0 1 16 16"/><circle cx="5" cy="19" r="1"/>
                </svg>
                Natality (Birth) Records
            </h1>
        </div>
        <?php if ($action === 'list'): ?>
        <div class="d-flex gap-2">
            <?php if (has_permission('view_reports')): ?>
            <a href="<?php echo BASE_URL; ?>?action=report-health-records&report=natality" class="btn btn-glass">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Export PDF
            </a>
            <?php endif; ?>
            <?php if (has_permission('manage_patients')): ?>
            <a href="<?php echo BASE_URL; ?>admin-health-records-natality?action=add" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Register Birth
            </a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <a href="<?php echo BASE_URL; ?>admin-health-records-natality" class="btn btn-glass">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to List
        </a>
        <?php endif; ?>
    </div>

    <?php if ($action === 'list'): ?>
    <!-- Search & Filter -->
    <div class="glass-card mb-4">
        <div class="glass-card-body">
            <form method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="admin-health-records-natality">
                <div class="col-12 col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" placeholder="Baby name, mother name..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label">Year</label>
                    <select class="form-select" name="year">
                        <option value="">All Years</option>
                        <?php foreach ($years as $y): ?>
                        <option value="<?php echo $y; ?>" <?php echo $filterYear == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Records Table -->
    <div class="glass-card">
        <div class="glass-card-header d-flex justify-content-between align-items-center">
            <h5 class="glass-card-title mb-0">Birth Records</h5>
            <span class="badge badge-success"><?php echo number_format($total_records); ?> Births</span>
        </div>
        <div class="glass-card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Baby Name</th>
                            <th>Sex</th>
                            <th>Weight</th>
                            <th>Delivery</th>
                            <th>Mother</th>
                            <?php if (is_superadmin()): ?>
                            <th>BHW</th>
                            <?php endif; ?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="<?php echo is_superadmin() ? '8' : '7'; ?>" class="text-center py-5 text-muted">No birth records found</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($records as $rec): ?>
                        <tr>
                            <td><?php echo date('M j, Y', strtotime($rec['date_of_birth'])); ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm" style="width:32px;height:32px;border-radius:8px;background:<?php echo $rec['sex'] === 'M' ? '#3b82f620' : '#ec489920'; ?>;color:<?php echo $rec['sex'] === 'M' ? '#3b82f6' : '#ec4899'; ?>;display:flex;align-items:center;justify-content:center;">
                                        <?php echo $rec['sex'] === 'M' ? '♂' : '♀'; ?>
                                    </div>
                                    <span><?php echo htmlspecialchars($rec['baby_complete_name']); ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="badge <?php echo $rec['sex'] === 'M' ? 'badge-primary' : 'badge-secondary'; ?>" style="background:<?php echo $rec['sex'] === 'M' ? '#3b82f620' : '#ec489920'; ?>;color:<?php echo $rec['sex'] === 'M' ? '#3b82f6' : '#ec4899'; ?>;">
                                    <?php echo $rec['sex'] === 'M' ? 'Male' : 'Female'; ?>
                                </span>
                            </td>
                            <td><?php echo $rec['weight_kg'] ? number_format($rec['weight_kg'], 2) . ' kg' : '-'; ?></td>
                            <td>
                                <span class="badge <?php echo $rec['delivery_type'] === 'Normal' ? 'badge-success' : 'badge-warning'; ?>">
                                    <?php echo htmlspecialchars($rec['delivery_type'] ?? '-'); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($rec['mother_complete_name'] ?? '-'); ?></td>
                            <?php if (is_superadmin()): ?>
                            <td><span class="text-muted small"><?php echo htmlspecialchars($rec['bhw_name'] ?? 'Unknown'); ?></span></td>
                            <?php endif; ?>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?php echo BASE_URL; ?>admin-health-records-natality?action=view&id=<?php echo $rec['natality_id']; ?>" class="btn btn-sm btn-glass" title="View">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <?php if (has_permission('manage_patients')): ?>
                                    <a href="<?php echo BASE_URL; ?>admin-health-records-natality?action=edit&id=<?php echo $rec['natality_id']; ?>" class="btn btn-sm btn-glass" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    <form method="POST" action="<?php echo BASE_URL; ?>?action=delete-natality-record" class="d-inline delete-form">
                                        <?php echo csrf_input(); ?>
                                        <input type="hidden" name="id" value="<?php echo $rec['natality_id']; ?>">
                                        <button type="button" class="btn btn-sm btn-glass text-danger delete-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                    <?php endif; ?>
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
                        <a class="page-link" href="<?php echo BASE_URL; ?>admin-health-records-natality?p=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&year=<?php echo urlencode($filterYear); ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>

    <?php elseif ($action === 'add' || $action === 'edit'): ?>
    <!-- Add/Edit Form -->
    <div class="glass-card">
        <div class="glass-card-header">
            <h5 class="glass-card-title mb-0"><?php echo $action === 'edit' ? 'Edit' : 'Register New'; ?> Birth Record</h5>
        </div>
        <div class="glass-card-body">
            <form method="POST" action="<?php echo BASE_URL; ?>?action=save-natality-record">
                <?php echo csrf_input(); ?>
                <input type="hidden" name="natality_id" value="<?php echo $record['natality_id'] ?? ''; ?>">
                
                <div class="row g-3">
                    <!-- Baby Information -->
                    <div class="col-12">
                        <h6 class="form-section-title mb-3">Baby Information</h6>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Baby's Complete Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="baby_complete_name" required value="<?php echo htmlspecialchars($record['baby_complete_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sex <span class="text-danger">*</span></label>
                        <select class="form-select" name="sex" required>
                            <option value="">Select</option>
                            <option value="M" <?php echo ($record['sex'] ?? '') === 'M' ? 'selected' : ''; ?>>Male</option>
                            <option value="F" <?php echo ($record['sex'] ?? '') === 'F' ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Weight (kg)</label>
                        <input type="number" class="form-control" name="weight_kg" step="0.01" min="0.5" max="10" value="<?php echo htmlspecialchars($record['weight_kg'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="date_of_birth" required value="<?php echo htmlspecialchars($record['date_of_birth'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Time of Birth</label>
                        <input type="time" class="form-control" name="time_of_birth" value="<?php echo htmlspecialchars($record['time_of_birth'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Delivery Type</label>
                        <select class="form-select" name="delivery_type">
                            <option value="Normal" <?php echo ($record['delivery_type'] ?? '') === 'Normal' ? 'selected' : ''; ?>>Normal</option>
                            <option value="CS" <?php echo ($record['delivery_type'] ?? '') === 'CS' ? 'selected' : ''; ?>>Cesarean Section (CS)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Place of Delivery</label>
                        <input type="text" class="form-control" name="place_of_delivery" placeholder="Hospital name / Home" value="<?php echo htmlspecialchars($record['place_of_delivery'] ?? ''); ?>">
                    </div>

                    <!-- Mother Information -->
                    <div class="col-12 mt-4">
                        <h6 class="form-section-title mb-3">Mother Information</h6>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mother's Complete Name</label>
                        <input type="text" class="form-control" name="mother_complete_name" value="<?php echo htmlspecialchars($record['mother_complete_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Mother's Age</label>
                        <input type="number" class="form-control" name="mother_age" min="10" max="60" value="<?php echo htmlspecialchars($record['mother_age'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Link to Patient</label>
                        <select class="form-select" name="mother_patient_id">
                            <option value="">-- Optional --</option>
                            <?php foreach ($mothers as $m): ?>
                            <option value="<?php echo $m['patient_id']; ?>" <?php echo ($record['mother_patient_id'] ?? '') == $m['patient_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($m['full_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- BHW -->
                    <div class="col-md-6">
                        <label class="form-label">BHW In Charge</label>
                        <select class="form-select" name="bhw_id">
                            <option value="">-- Select BHW --</option>
                            <?php foreach ($bhws as $b): ?>
                            <option value="<?php echo $b['bhw_id']; ?>" <?php echo ($record['bhw_id'] ?? $_SESSION['bhw_id']) == $b['bhw_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($b['full_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="<?php echo BASE_URL; ?>admin-health-records-natality" class="btn btn-glass">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Save Record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php elseif ($action === 'view' && $record): ?>
    <!-- View Record -->
    <div class="glass-card">
        <div class="glass-card-header d-flex justify-content-between align-items-center">
            <h5 class="glass-card-title mb-0">Birth Record Details</h5>
            <?php if (has_permission('manage_patients')): ?>
            <div class="d-flex gap-2">
                <a href="<?php echo BASE_URL; ?>admin-health-records-natality?action=edit&id=<?php echo $record['natality_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
            </div>
            <?php endif; ?>
        </div>
        <div class="glass-card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Baby Information</h6>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Name:</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($record['baby_complete_name']); ?></dd>
                        <dt class="col-sm-5">Sex:</dt>
                        <dd class="col-sm-7"><?php echo $record['sex'] === 'M' ? 'Male' : 'Female'; ?></dd>
                        <dt class="col-sm-5">Weight:</dt>
                        <dd class="col-sm-7"><?php echo $record['weight_kg'] ? number_format($record['weight_kg'], 2) . ' kg' : '-'; ?></dd>
                        <dt class="col-sm-5">Date of Birth:</dt>
                        <dd class="col-sm-7"><?php echo date('F j, Y', strtotime($record['date_of_birth'])); ?></dd>
                        <dt class="col-sm-5">Time of Birth:</dt>
                        <dd class="col-sm-7"><?php echo $record['time_of_birth'] ? date('g:i A', strtotime($record['time_of_birth'])) : '-'; ?></dd>
                        <dt class="col-sm-5">Delivery Type:</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($record['delivery_type'] ?? '-'); ?></dd>
                        <dt class="col-sm-5">Place:</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($record['place_of_delivery'] ?? '-'); ?></dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Mother Information</h6>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Name:</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($record['mother_complete_name'] ?? '-'); ?></dd>
                        <dt class="col-sm-5">Age:</dt>
                        <dd class="col-sm-7"><?php echo $record['mother_age'] ?? '-'; ?></dd>
                        <dt class="col-sm-5">BHW In Charge:</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($record['bhw_name'] ?? '-'); ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Delete confirmation - using POST form for security
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.delete-form');
            Swal.fire({
                title: 'Delete Record?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

<style>
.form-section-title { font-weight: 600; color: var(--text-primary); border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; }
.breadcrumb { background: transparent; padding: 0; margin: 0; font-size: 0.875rem; }
.breadcrumb-item a { color: var(--primary); text-decoration: none; }
.breadcrumb-item.active { color: var(--text-muted); }
dl dt { color: var(--text-muted); font-weight: 500; }
dl dd { color: var(--text-primary); }
</style>

<?php include __DIR__ . '/../../../includes/footer_admin.php'; ?>
