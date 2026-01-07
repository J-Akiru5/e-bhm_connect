<?php
/**
 * Chronic Disease Masterlist (Hypertensive & Diabetic)
 * E-BHM Connect - Glassmorphism Design
 */
include __DIR__ . '/../../../includes/header_admin.php';

$action = $_GET['action'] ?? 'list';
$editId = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Fetch patients
$patients = [];
try {
    $stmt = $pdo->query("SELECT patient_id, full_name FROM patients ORDER BY full_name ASC");
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

// Fetch BHWs
$bhws = [];
try {
    $stmt = $pdo->query("SELECT bhw_id, full_name FROM bhw_users ORDER BY full_name ASC");
    $bhws = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

// Fetch record for editing
$record = null;
if ($editId && in_array($action, ['edit', 'view'])) {
    try {
        $stmt = $pdo->prepare("SELECT c.*, p.full_name as linked_patient, b.full_name as bhw_name 
                               FROM chronic_disease_masterlist c
                               LEFT JOIN patients p ON c.patient_id = p.patient_id
                               LEFT JOIN bhw_users b ON c.bhw_id = b.bhw_id
                               WHERE c.chronic_id = ?");
        $stmt->execute([$editId]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}

// Pagination & search
$page_num = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$per_page = 15;
$offset = ($page_num - 1) * $per_page;

$records = [];
$total_records = 0;
$search = $_GET['search'] ?? '';
$filterCondition = $_GET['condition'] ?? '';

if ($action === 'list') {
    try {
        $whereConditions = [];
        $params = [];
        
        if ($search) {
            $whereConditions[] = "(CONCAT(first_name, ' ', last_name) LIKE ? OR philhealth_no LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($filterCondition === 'hypertensive') {
            $whereConditions[] = "is_hypertensive = 1";
        } elseif ($filterCondition === 'diabetic') {
            $whereConditions[] = "is_diabetic = 1";
        }
        
        $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $countSql = "SELECT COUNT(*) FROM chronic_disease_masterlist $whereClause";
        $stmt = $pdo->prepare($countSql);
        $stmt->execute($params);
        $total_records = (int)$stmt->fetchColumn();
        
        $sql = "SELECT c.*, b.full_name as bhw_name 
                FROM chronic_disease_masterlist c
                LEFT JOIN bhw_users b ON c.bhw_id = b.bhw_id
                $whereClause
                ORDER BY c.date_of_enrollment DESC, c.created_at DESC
                LIMIT $per_page OFFSET $offset";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
    }
}

$total_pages = ceil($total_records / $per_page);

// Stats
$stats = ['total' => 0, 'hypertensive' => 0, 'diabetic' => 0, 'both' => 0];
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total,
        SUM(is_hypertensive = 1 AND is_diabetic = 0) as hypertensive_only,
        SUM(is_diabetic = 1 AND is_hypertensive = 0) as diabetic_only,
        SUM(is_hypertensive = 1 AND is_diabetic = 1) as both_conditions
        FROM chronic_disease_masterlist");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total'] = (int)$row['total'];
    $stats['hypertensive'] = (int)$row['hypertensive_only'];
    $stats['diabetic'] = (int)$row['diabetic_only'];
    $stats['both'] = (int)$row['both_conditions'];
} catch (PDOException $e) {}
?>

<div class="container-fluid py-4 fade-in">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin-health-records">Health Records</a></li>
                    <li class="breadcrumb-item active">Chronic Diseases</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0" style="color: #ef4444;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2" style="vertical-align: -4px;"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                Hypertensive & Diabetic Masterlist
            </h1>
        </div>
        <?php if ($action === 'list'): ?>
        <div class="d-flex gap-2">
            <?php if (has_permission('view_reports')): ?>
            <a href="<?php echo BASE_URL; ?>?action=report-health-records&report=chronic" class="btn btn-glass">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Export PDF
            </a>
            <?php endif; ?>
            <?php if (has_permission('manage_patients')): ?>
            <a href="<?php echo BASE_URL; ?>admin-health-records-chronic?action=add" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Patient
            </a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <a href="<?php echo BASE_URL; ?>admin-health-records-chronic" class="btn btn-glass">← Back to List</a>
        <?php endif; ?>
    </div>

    <?php if ($action === 'list'): ?>
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-card-content text-center">
                    <div class="stat-card-value"><?php echo $stats['total']; ?></div>
                    <div class="stat-card-label small">Total Patients</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-card-content text-center">
                    <div class="stat-card-value" style="color: #ef4444;"><?php echo $stats['hypertensive']; ?></div>
                    <div class="stat-card-label small">Hypertensive Only</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-card-content text-center">
                    <div class="stat-card-value" style="color: #8b5cf6;"><?php echo $stats['diabetic']; ?></div>
                    <div class="stat-card-label small">Diabetic Only</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-card-content text-center">
                    <div class="stat-card-value" style="color: #f59e0b;"><?php echo $stats['both']; ?></div>
                    <div class="stat-card-label small">Both Conditions</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="glass-card mb-4">
        <div class="glass-card-body">
            <form method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="admin-health-records-chronic">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" placeholder="Name, PhilHealth No..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Condition</label>
                    <select class="form-select" name="condition">
                        <option value="">All Conditions</option>
                        <option value="hypertensive" <?php echo $filterCondition === 'hypertensive' ? 'selected' : ''; ?>>Hypertensive</option>
                        <option value="diabetic" <?php echo $filterCondition === 'diabetic' ? 'selected' : ''; ?>>Diabetic</option>
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
            <h5 class="glass-card-title mb-0">Patient Masterlist</h5>
        </div>
        <div class="glass-card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age/Sex</th>
                            <th>Conditions</th>
                            <th>Blood Sugar</th>
                            <th>PhilHealth</th>
                            <th>Enrolled</th>
                            <?php if (is_superadmin()): ?>
                            <th>BHW</th>
                            <?php endif; ?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                        <tr><td colspan="<?php echo is_superadmin() ? '8' : '7'; ?>" class="text-center py-5 text-muted">No records found</td></tr>
                        <?php else: ?>
                        <?php foreach ($records as $rec): ?>
                        <tr>
                            <td>
                                <div class="fw-medium"><?php echo htmlspecialchars($rec['last_name'] . ', ' . $rec['first_name']); ?></div>
                                <?php if ($rec['nhts_member']): ?>
                                <span class="badge badge-success" style="font-size:0.65rem;">NHTS</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $rec['age'] ?? '-'; ?> / <?php echo $rec['sex'] ?? '-'; ?></td>
                            <td>
                                <?php if ($rec['is_hypertensive']): ?>
                                <span class="badge" style="background:#ef444420;color:#ef4444;">Hypertensive</span>
                                <?php endif; ?>
                                <?php if ($rec['is_diabetic']): ?>
                                <span class="badge" style="background:#8b5cf620;color:#8b5cf6;">Diabetic</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($rec['blood_sugar_level']): ?>
                                <?php echo number_format($rec['blood_sugar_level'], 1); ?> mg/dL
                                <small class="text-muted d-block"><?php echo $rec['test_type'] ?? ''; ?></small>
                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($rec['philhealth_no'] ?? '-'); ?></td>
                            <td><?php echo $rec['date_of_enrollment'] ? date('M j, Y', strtotime($rec['date_of_enrollment'])) : '-'; ?></td>
                            <?php if (is_superadmin()): ?>
                            <td><span class="text-muted small"><?php echo htmlspecialchars($rec['bhw_name'] ?? 'Unknown'); ?></span></td>
                            <?php endif; ?>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?php echo BASE_URL; ?>admin-health-records-chronic?action=view&id=<?php echo $rec['chronic_id']; ?>" class="btn btn-sm btn-glass">View</a>
                                    <?php if (has_permission('manage_patients')): ?>
                                    <a href="<?php echo BASE_URL; ?>admin-health-records-chronic?action=edit&id=<?php echo $rec['chronic_id']; ?>" class="btn btn-sm btn-glass">Edit</a>
                                    <form method="POST" action="<?php echo BASE_URL; ?>?action=delete-chronic-disease" class="d-inline delete-form">
                                        <?php echo csrf_input(); ?>
                                        <input type="hidden" name="id" value="<?php echo $rec['chronic_id']; ?>">
                                        <button type="button" class="btn btn-sm btn-glass text-danger delete-btn" title="Delete">
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
    </div>

    <?php elseif ($action === 'add' || $action === 'edit'): ?>
    <!-- Add/Edit Form -->
    <div class="glass-card">
        <div class="glass-card-header">
            <h5 class="glass-card-title mb-0"><?php echo $action === 'edit' ? 'Edit' : 'Add'; ?> Patient Record</h5>
        </div>
        <div class="glass-card-body">
            <form method="POST" action="<?php echo BASE_URL; ?>?action=save-chronic-disease">
                <?php echo csrf_input(); ?>
                <input type="hidden" name="chronic_id" value="<?php echo $record['chronic_id'] ?? ''; ?>">
                
                <div class="row g-3">
                    <!-- Personal Info -->
                    <div class="col-12"><h6 class="form-section-title mb-3">Personal Information</h6></div>
                    <div class="col-md-4">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="last_name" required value="<?php echo htmlspecialchars($record['last_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="first_name" required value="<?php echo htmlspecialchars($record['first_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Middle Name</label>
                        <input type="text" class="form-control" name="middle_name" value="<?php echo htmlspecialchars($record['middle_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sex</label>
                        <select class="form-select" name="sex">
                            <option value="">Select</option>
                            <option value="M" <?php echo ($record['sex'] ?? '') === 'M' ? 'selected' : ''; ?>>Male</option>
                            <option value="F" <?php echo ($record['sex'] ?? '') === 'F' ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Age</label>
                        <input type="number" class="form-control" name="age" min="1" max="150" value="<?php echo htmlspecialchars($record['age'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth" value="<?php echo htmlspecialchars($record['date_of_birth'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">PhilHealth No.</label>
                        <input type="text" class="form-control" name="philhealth_no" value="<?php echo htmlspecialchars($record['philhealth_no'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">NHTS Member</label>
                        <select class="form-select" name="nhts_member">
                            <option value="0" <?php echo ($record['nhts_member'] ?? 0) == 0 ? 'selected' : ''; ?>>No</option>
                            <option value="1" <?php echo ($record['nhts_member'] ?? 0) == 1 ? 'selected' : ''; ?>>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date of Enrollment</label>
                        <input type="date" class="form-control" name="date_of_enrollment" value="<?php echo htmlspecialchars($record['date_of_enrollment'] ?? date('Y-m-d')); ?>">
                    </div>

                    <!-- Medical Conditions -->
                    <div class="col-12 mt-4"><h6 class="form-section-title mb-3">Medical Conditions</h6></div>
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_hypertensive" id="isHypertensive" value="1" <?php echo ($record['is_hypertensive'] ?? 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="isHypertensive">
                                <strong style="color:#ef4444;">Hypertensive</strong>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_diabetic" id="isDiabetic" value="1" <?php echo ($record['is_diabetic'] ?? 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="isDiabetic">
                                <strong style="color:#8b5cf6;">Diabetic</strong>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Test Type</label>
                        <select class="form-select" name="test_type">
                            <option value="">Select</option>
                            <option value="FBS" <?php echo ($record['test_type'] ?? '') === 'FBS' ? 'selected' : ''; ?>>FBS</option>
                            <option value="RBS" <?php echo ($record['test_type'] ?? '') === 'RBS' ? 'selected' : ''; ?>>RBS</option>
                            <option value="HbA1c" <?php echo ($record['test_type'] ?? '') === 'HbA1c' ? 'selected' : ''; ?>>HbA1c</option>
                            <option value="OGTT" <?php echo ($record['test_type'] ?? '') === 'OGTT' ? 'selected' : ''; ?>>OGTT</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Blood Sugar Level (mg/dL)</label>
                        <input type="number" class="form-control" name="blood_sugar_level" step="0.1" value="<?php echo htmlspecialchars($record['blood_sugar_level'] ?? ''); ?>">
                    </div>

                    <!-- Medications -->
                    <div class="col-12 mt-4"><h6 class="form-section-title mb-3">Medications Taken</h6></div>
                    <div class="col-12">
                        <div class="row g-2">
                            <?php
                            $medications = [
                                'med_amlo5' => 'Amlodipine 5mg',
                                'med_amlo10' => 'Amlodipine 10mg',
                                'med_losartan50' => 'Losartan 50mg',
                                'med_losartan100' => 'Losartan 100mg',
                                'med_metoprolol' => 'Metoprolol',
                                'med_simvastatin' => 'Simvastatin',
                                'med_metformin' => 'Metformin',
                                'med_gliclazide' => 'Gliclazide',
                                'med_insulin' => 'Insulin'
                            ];
                            foreach ($medications as $field => $label):
                            ?>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="<?php echo $field; ?>" id="<?php echo $field; ?>" value="1" <?php echo ($record[$field] ?? 0) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="<?php echo $field; ?>"><?php echo $label; ?></label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div class="col-12 mt-4">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" name="remarks" rows="3"><?php echo htmlspecialchars($record['remarks'] ?? ''); ?></textarea>
                    </div>

                    <!-- BHW -->
                    <div class="col-md-6">
                        <label class="form-label">BHW In Charge</label>
                        <select class="form-select" name="bhw_id">
                            <option value="">-- Select --</option>
                            <?php foreach ($bhws as $b): ?>
                            <option value="<?php echo $b['bhw_id']; ?>" <?php echo ($record['bhw_id'] ?? $_SESSION['bhw_id']) == $b['bhw_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($b['full_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="<?php echo BASE_URL; ?>admin-health-records-chronic" class="btn btn-glass">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Record</button>
                </div>
            </form>
        </div>
    </div>

    <?php elseif ($action === 'view' && $record): ?>
    <!-- View -->
    <div class="glass-card">
        <div class="glass-card-header d-flex justify-content-between align-items-center">
            <h5 class="glass-card-title mb-0">Patient Details</h5>
            <?php if (has_permission('manage_patients')): ?>
            <a href="<?php echo BASE_URL; ?>admin-health-records-chronic?action=edit&id=<?php echo $record['chronic_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
            <?php endif; ?>
        </div>
        <div class="glass-card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Personal Information</h6>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Full Name:</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($record['last_name'] . ', ' . $record['first_name'] . ' ' . ($record['middle_name'] ?? '')); ?></dd>
                        <dt class="col-sm-5">Age/Sex:</dt>
                        <dd class="col-sm-7"><?php echo ($record['age'] ?? '-') . ' / ' . ($record['sex'] ?? '-'); ?></dd>
                        <dt class="col-sm-5">Date of Birth:</dt>
                        <dd class="col-sm-7"><?php echo $record['date_of_birth'] ? date('M j, Y', strtotime($record['date_of_birth'])) : '-'; ?></dd>
                        <dt class="col-sm-5">PhilHealth:</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($record['philhealth_no'] ?? '-'); ?></dd>
                        <dt class="col-sm-5">NHTS:</dt>
                        <dd class="col-sm-7"><?php echo $record['nhts_member'] ? 'Yes' : 'No'; ?></dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Medical Information</h6>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Conditions:</dt>
                        <dd class="col-sm-7">
                            <?php if ($record['is_hypertensive']): ?><span class="badge" style="background:#ef444420;color:#ef4444;">Hypertensive</span> <?php endif; ?>
                            <?php if ($record['is_diabetic']): ?><span class="badge" style="background:#8b5cf620;color:#8b5cf6;">Diabetic</span><?php endif; ?>
                        </dd>
                        <dt class="col-sm-5">Blood Sugar:</dt>
                        <dd class="col-sm-7"><?php echo $record['blood_sugar_level'] ? number_format($record['blood_sugar_level'], 1) . ' mg/dL (' . $record['test_type'] . ')' : '-'; ?></dd>
                        <dt class="col-sm-5">Date Enrolled:</dt>
                        <dd class="col-sm-7"><?php echo $record['date_of_enrollment'] ? date('M j, Y', strtotime($record['date_of_enrollment'])) : '-'; ?></dd>
                    </dl>
                </div>
                <div class="col-12">
                    <h6 class="text-muted mb-3">Medications</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php
                        $medLabels = [
                            'med_amlo5' => 'Amlodipine 5mg', 'med_amlo10' => 'Amlodipine 10mg',
                            'med_losartan50' => 'Losartan 50mg', 'med_losartan100' => 'Losartan 100mg',
                            'med_metoprolol' => 'Metoprolol', 'med_simvastatin' => 'Simvastatin',
                            'med_metformin' => 'Metformin', 'med_gliclazide' => 'Gliclazide', 'med_insulin' => 'Insulin'
                        ];
                        $hasMeds = false;
                        foreach ($medLabels as $f => $l):
                            if (!empty($record[$f])):
                                $hasMeds = true;
                        ?>
                        <span class="badge badge-primary"><?php echo $l; ?></span>
                        <?php endif; endforeach;
                        if (!$hasMeds): ?>
                        <span class="text-muted">No medications recorded</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($record['remarks']): ?>
                <div class="col-12">
                    <h6 class="text-muted mb-3">Remarks</h6>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($record['remarks'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

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
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it',
                background: 'rgba(30, 41, 59, 0.95)',
                color: '#ffffff'
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
dl dt { color: var(--text-muted); font-weight: 500; }
dl dd { color: var(--text-primary); }
</style>

<?php include __DIR__ . '/../../../includes/footer_admin.php'; ?>
