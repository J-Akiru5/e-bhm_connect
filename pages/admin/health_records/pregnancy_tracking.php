<?php
/**
 * Pregnancy Tracking Page
 * E-BHM Connect - Glassmorphism Design
 * CRUD for pregnancy monitoring records
 */
include __DIR__ . '/../../../includes/header_admin.php';

// Handle form action
$action = $_GET['action'] ?? 'list';
$editId = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Fetch patients for dropdown
$patients = [];
try {
    $stmt = $pdo->query("SELECT patient_id, full_name FROM patients WHERE sex = 'Female' OR sex IS NULL ORDER BY full_name ASC");
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching patients: " . $e->getMessage());
}

// Fetch BHWs for dropdown
$bhws = [];
try {
    $stmt = $pdo->query("SELECT bhw_id, full_name FROM bhw_users ORDER BY full_name ASC");
    $bhws = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching BHWs: " . $e->getMessage());
}

// Fetch record for editing
$record = null;
if ($editId && $action === 'edit') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM pregnancy_tracking WHERE pregnancy_id = ?");
        $stmt->execute([$editId]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching record: " . $e->getMessage());
    }
}

// Pagination for list view
$page_num = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$per_page = 15;
$offset = ($page_num - 1) * $per_page;

// Fetch records for list
$records = [];
$total_records = 0;
$search = $_GET['search'] ?? '';

if ($action === 'list') {
    try {
        $whereClause = '';
        $params = [];
        
        if ($search) {
            $whereClause = "WHERE pregnant_woman_name LIKE ? OR husband_name LIKE ? OR phone_number LIKE ?";
            $searchParam = "%$search%";
            $params = [$searchParam, $searchParam, $searchParam];
        }
        
        // Get total count
        $countSql = "SELECT COUNT(*) FROM pregnancy_tracking $whereClause";
        $stmt = $pdo->prepare($countSql);
        $stmt->execute($params);
        $total_records = (int)$stmt->fetchColumn();
        
        // Get records
        $sql = "SELECT pt.*, p.full_name as linked_patient_name, b.full_name as bhw_name 
                FROM pregnancy_tracking pt
                LEFT JOIN patients p ON pt.patient_id = p.patient_id
                LEFT JOIN bhw_users b ON pt.bhw_id = b.bhw_id
                $whereClause
                ORDER BY pt.created_at DESC
                LIMIT $per_page OFFSET $offset";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching records: " . $e->getMessage());
    }
}

$total_pages = ceil($total_records / $per_page);
?>

<div class="container-fluid py-4 fade-in">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin-health-records">Health Records</a></li>
                    <li class="breadcrumb-item active">Pregnancy Tracking</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0" style="color: #ec4899;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2" style="vertical-align: -4px;">
                    <circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/><path d="M12 13v8"/>
                </svg>
                Pregnancy Tracking
            </h1>
        </div>
        <?php if ($action === 'list'): ?>
        <div class="d-flex gap-2">
            <a href="<?php echo BASE_URL; ?>?action=report-health-records&report=pregnancy" class="btn btn-glass">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Export PDF
            </a>
            <a href="<?php echo BASE_URL; ?>admin-health-records-pregnancy?action=add" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add New Record
            </a>
        </div>
        <?php else: ?>
        <a href="<?php echo BASE_URL; ?>admin-health-records-pregnancy" class="btn btn-glass">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Back to List
        </a>
        <?php endif; ?>
    </div>

    <?php if ($action === 'list'): ?>
    <!-- Search & Filter Bar -->
    <div class="glass-card mb-4">
        <div class="glass-card-body">
            <form method="GET" action="<?php echo BASE_URL; ?>admin-health-records-pregnancy" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="admin-health-records-pregnancy">
                <div class="col-12 col-md-8">
                    <label class="form-label">Search Records</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </span>
                        <input type="text" class="form-control" name="search" placeholder="Search by name, husband, phone..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Records Table -->
    <div class="glass-card">
        <div class="glass-card-header d-flex justify-content-between align-items-center">
            <h5 class="glass-card-title mb-0">All Pregnancy Records</h5>
            <span class="badge badge-primary"><?php echo number_format($total_records); ?> Records</span>
        </div>
        <div class="glass-card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>LMP</th>
                            <th>EDC</th>
                            <th>NHTS</th>
                            <th>G-P</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3 opacity-50">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="15" x2="15" y2="15"/>
                                </svg>
                                <p class="mb-0">No pregnancy records found</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($records as $rec): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm" style="width:36px;height:36px;border-radius:10px;background:#ec489920;color:#ec4899;display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:600;">
                                        <?php echo strtoupper(substr($rec['pregnant_woman_name'], 0, 2)); ?>
                                    </div>
                                    <div>
                                        <div class="fw-medium"><?php echo htmlspecialchars($rec['pregnant_woman_name']); ?></div>
                                        <?php if ($rec['phone_number']): ?>
                                        <small class="text-muted"><?php echo htmlspecialchars($rec['phone_number']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $rec['age'] ?? '-'; ?></td>
                            <td><?php echo $rec['lmp'] ? date('M j, Y', strtotime($rec['lmp'])) : '-'; ?></td>
                            <td>
                                <?php if ($rec['edc']): ?>
                                <span class="<?php echo strtotime($rec['edc']) < time() ? 'text-danger' : ''; ?>">
                                    <?php echo date('M j, Y', strtotime($rec['edc'])); ?>
                                </span>
                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?php echo $rec['nhts_status'] === 'NHTS' ? 'badge-success' : 'badge-secondary'; ?>">
                                    <?php echo htmlspecialchars($rec['nhts_status'] ?? 'N/A'); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($rec['gravida_para'] ?? '-'); ?></td>
                            <td>
                                <?php if ($rec['outcome_date_of_delivery']): ?>
                                <span class="badge badge-success">Delivered</span>
                                <?php elseif ($rec['edc'] && strtotime($rec['edc']) < time()): ?>
                                <span class="badge badge-warning">Overdue</span>
                                <?php else: ?>
                                <span class="badge badge-primary">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?php echo BASE_URL; ?>admin-health-records-pregnancy?action=view&id=<?php echo $rec['pregnancy_id']; ?>" class="btn btn-sm btn-glass" title="View">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>admin-health-records-pregnancy?action=edit&id=<?php echo $rec['pregnancy_id']; ?>" class="btn btn-sm btn-glass" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-glass text-danger" onclick="confirmDelete(<?php echo $rec['pregnancy_id']; ?>)" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
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
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mb-0">
                    <?php if ($page_num > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo BASE_URL; ?>admin-health-records-pregnancy?p=<?php echo $page_num - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page_num - 2); $i <= min($total_pages, $page_num + 2); $i++): ?>
                    <li class="page-item <?php echo $i === $page_num ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo BASE_URL; ?>admin-health-records-pregnancy?p=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page_num < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo BASE_URL; ?>admin-health-records-pregnancy?p=<?php echo $page_num + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>

    <?php elseif ($action === 'add' || $action === 'edit'): ?>
    <!-- Add/Edit Form -->
    <div class="glass-card">
        <div class="glass-card-header">
            <h5 class="glass-card-title mb-0"><?php echo $action === 'edit' ? 'Edit' : 'Add New'; ?> Pregnancy Record</h5>
        </div>
        <div class="glass-card-body">
            <form method="POST" action="<?php echo BASE_URL; ?>?action=save-pregnancy-tracking" id="pregnancyForm">
                <input type="hidden" name="pregnancy_id" value="<?php echo $record['pregnancy_id'] ?? ''; ?>">
                
                <!-- Personal Information -->
                <div class="form-section mb-4">
                    <h6 class="form-section-title mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Personal Information
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Pregnant Woman's Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="pregnant_woman_name" required value="<?php echo htmlspecialchars($record['pregnant_woman_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Age</label>
                            <input type="number" class="form-control" name="age" min="10" max="60" value="<?php echo htmlspecialchars($record['age'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Birth Date</label>
                            <input type="date" class="form-control" name="birth_date" value="<?php echo htmlspecialchars($record['birth_date'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Husband's Name</label>
                            <input type="text" class="form-control" name="husband_name" value="<?php echo htmlspecialchars($record['husband_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" name="phone_number" value="<?php echo htmlspecialchars($record['phone_number'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Link to Patient Record</label>
                            <select class="form-select" name="patient_id">
                                <option value="">-- Select Patient (Optional) --</option>
                                <?php foreach ($patients as $p): ?>
                                <option value="<?php echo $p['patient_id']; ?>" <?php echo ($record['patient_id'] ?? '') == $p['patient_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['full_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
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
                </div>

                <!-- Pregnancy Details -->
                <div class="form-section mb-4">
                    <h6 class="form-section-title mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Pregnancy Details
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Date of Identification <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date_of_identification" required value="<?php echo htmlspecialchars($record['date_of_identification'] ?? date('Y-m-d')); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">LMP (Last Menstrual Period)</label>
                            <input type="date" class="form-control" name="lmp" id="lmpDate" value="<?php echo htmlspecialchars($record['lmp'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">EDC (Estimated Date of Confinement)</label>
                            <input type="date" class="form-control" name="edc" id="edcDate" value="<?php echo htmlspecialchars($record['edc'] ?? ''); ?>">
                            <small class="text-muted">Auto-calculated from LMP</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">TT Status</label>
                            <select class="form-select" name="tt_status">
                                <option value="">Select Status</option>
                                <option value="TT1" <?php echo ($record['tt_status'] ?? '') === 'TT1' ? 'selected' : ''; ?>>TT1</option>
                                <option value="TT2" <?php echo ($record['tt_status'] ?? '') === 'TT2' ? 'selected' : ''; ?>>TT2</option>
                                <option value="TT3" <?php echo ($record['tt_status'] ?? '') === 'TT3' ? 'selected' : ''; ?>>TT3</option>
                                <option value="TT4" <?php echo ($record['tt_status'] ?? '') === 'TT4' ? 'selected' : ''; ?>>TT4</option>
                                <option value="TT5" <?php echo ($record['tt_status'] ?? '') === 'TT5' ? 'selected' : ''; ?>>TT5</option>
                                <option value="Fully Immunized" <?php echo ($record['tt_status'] ?? '') === 'Fully Immunized' ? 'selected' : ''; ?>>Fully Immunized</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">NHTS Status</label>
                            <select class="form-select" name="nhts_status">
                                <option value="Non-NHTS" <?php echo ($record['nhts_status'] ?? '') === 'Non-NHTS' ? 'selected' : ''; ?>>Non-NHTS</option>
                                <option value="NHTS" <?php echo ($record['nhts_status'] ?? '') === 'NHTS' ? 'selected' : ''; ?>>NHTS</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gravida-Para (G-P)</label>
                            <input type="text" class="form-control" name="gravida_para" placeholder="e.g., G2-P1" value="<?php echo htmlspecialchars($record['gravida_para'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Outcome Details (Optional) -->
                <div class="form-section mb-4">
                    <h6 class="form-section-title mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2"><path d="M4 11a9 9 0 0 1 9 9"/><path d="M4 4a16 16 0 0 1 16 16"/><circle cx="5" cy="19" r="1"/></svg>
                        Delivery Outcome (if delivered)
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Date of Delivery</label>
                            <input type="date" class="form-control" name="outcome_date_of_delivery" value="<?php echo htmlspecialchars($record['outcome_date_of_delivery'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Place of Delivery</label>
                            <input type="text" class="form-control" name="outcome_place_of_delivery" placeholder="Hospital/Home/etc." value="<?php echo htmlspecialchars($record['outcome_place_of_delivery'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Type of Delivery</label>
                            <select class="form-select" name="outcome_type_of_delivery">
                                <option value="">Select Type</option>
                                <option value="Normal" <?php echo ($record['outcome_type_of_delivery'] ?? '') === 'Normal' ? 'selected' : ''; ?>>Normal</option>
                                <option value="CS" <?php echo ($record['outcome_type_of_delivery'] ?? '') === 'CS' ? 'selected' : ''; ?>>Cesarean Section (CS)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Outcome of Birth</label>
                            <select class="form-select" name="outcome_of_birth">
                                <option value="">Select Outcome</option>
                                <option value="Live Birth" <?php echo ($record['outcome_of_birth'] ?? '') === 'Live Birth' ? 'selected' : ''; ?>>Live Birth</option>
                                <option value="Stillbirth" <?php echo ($record['outcome_of_birth'] ?? '') === 'Stillbirth' ? 'selected' : ''; ?>>Stillbirth</option>
                                <option value="Miscarriage" <?php echo ($record['outcome_of_birth'] ?? '') === 'Miscarriage' ? 'selected' : ''; ?>>Miscarriage</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Remarks -->
                <div class="form-section mb-4">
                    <label class="form-label">Remarks / Notes</label>
                    <textarea class="form-control" name="remarks" rows="3" placeholder="Any additional notes..."><?php echo htmlspecialchars($record['remarks'] ?? ''); ?></textarea>
                </div>

                <!-- Form Actions -->
                <div class="d-flex gap-2 justify-content-end">
                    <a href="<?php echo BASE_URL; ?>admin-health-records-pregnancy" class="btn btn-glass">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                        </svg>
                        <?php echo $action === 'edit' ? 'Update' : 'Save'; ?> Record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php elseif ($action === 'view' && $record): ?>
    <!-- View Record -->
    <div class="glass-card">
        <div class="glass-card-header d-flex justify-content-between align-items-center">
            <h5 class="glass-card-title mb-0">Pregnancy Record Details</h5>
            <div class="d-flex gap-2">
                <a href="<?php echo BASE_URL; ?>admin-health-records-pregnancy?action=edit&id=<?php echo $record['pregnancy_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                <button type="button" class="btn btn-sm btn-glass text-danger" onclick="confirmDelete(<?php echo $record['pregnancy_id']; ?>)">Delete</button>
            </div>
        </div>
        <div class="glass-card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Personal Information</h6>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Name:</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($record['pregnant_woman_name']); ?></dd>
                        <dt class="col-sm-5">Age:</dt>
                        <dd class="col-sm-7"><?php echo $record['age'] ?? '-'; ?></dd>
                        <dt class="col-sm-5">Birth Date:</dt>
                        <dd class="col-sm-7"><?php echo $record['birth_date'] ? date('M j, Y', strtotime($record['birth_date'])) : '-'; ?></dd>
                        <dt class="col-sm-5">Husband:</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($record['husband_name'] ?? '-'); ?></dd>
                        <dt class="col-sm-5">Phone:</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($record['phone_number'] ?? '-'); ?></dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Pregnancy Details</h6>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">LMP:</dt>
                        <dd class="col-sm-7"><?php echo $record['lmp'] ? date('M j, Y', strtotime($record['lmp'])) : '-'; ?></dd>
                        <dt class="col-sm-5">EDC:</dt>
                        <dd class="col-sm-7"><?php echo $record['edc'] ? date('M j, Y', strtotime($record['edc'])) : '-'; ?></dd>
                        <dt class="col-sm-5">TT Status:</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($record['tt_status'] ?? '-'); ?></dd>
                        <dt class="col-sm-5">NHTS:</dt>
                        <dd class="col-sm-7"><span class="badge <?php echo $record['nhts_status'] === 'NHTS' ? 'badge-success' : 'badge-secondary'; ?>"><?php echo htmlspecialchars($record['nhts_status'] ?? '-'); ?></span></dd>
                        <dt class="col-sm-5">G-P:</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($record['gravida_para'] ?? '-'); ?></dd>
                    </dl>
                </div>
                <?php if ($record['outcome_date_of_delivery']): ?>
                <div class="col-12">
                    <h6 class="text-muted mb-3">Delivery Outcome</h6>
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Date of Delivery:</dt>
                        <dd class="col-sm-9"><?php echo date('M j, Y', strtotime($record['outcome_date_of_delivery'])); ?></dd>
                        <dt class="col-sm-3">Place:</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars($record['outcome_place_of_delivery'] ?? '-'); ?></dd>
                        <dt class="col-sm-3">Type:</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars($record['outcome_type_of_delivery'] ?? '-'); ?></dd>
                        <dt class="col-sm-3">Outcome:</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars($record['outcome_of_birth'] ?? '-'); ?></dd>
                    </dl>
                </div>
                <?php endif; ?>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Auto-calculate EDC from LMP (add 280 days / 40 weeks)
document.getElementById('lmpDate')?.addEventListener('change', function() {
    const lmp = new Date(this.value);
    if (!isNaN(lmp.getTime())) {
        lmp.setDate(lmp.getDate() + 280);
        const edc = lmp.toISOString().split('T')[0];
        document.getElementById('edcDate').value = edc;
    }
});

// Delete confirmation
function confirmDelete(id) {
    Swal.fire({
        title: 'Delete Record?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?php echo BASE_URL; ?>?action=delete-pregnancy-tracking&id=' + id;
        }
    });
}
</script>

<style>
.form-section-title {
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border-color);
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin: 0;
    font-size: 0.875rem;
}

.breadcrumb-item a {
    color: var(--primary);
    text-decoration: none;
}

.breadcrumb-item.active {
    color: var(--text-muted);
}

.breadcrumb-item + .breadcrumb-item::before {
    color: var(--text-muted);
}

dl dt {
    color: var(--text-muted);
    font-weight: 500;
}

dl dd {
    color: var(--text-primary);
}
</style>

<?php include __DIR__ . '/../../../includes/footer_admin.php'; ?>
