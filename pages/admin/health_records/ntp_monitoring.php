<?php
/**
 * NTP (Tuberculosis Program) Client Monitoring
 * E-BHM Connect - Glassmorphism Design
 */
include __DIR__ . '/../../../includes/header_admin.php';

$action = $_GET['action'] ?? 'list';
$editId = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Fetch patients & BHWs
$patients = [];
$bhws = [];
try {
    $stmt = $pdo->query("SELECT patient_id, full_name FROM patients ORDER BY full_name ASC");
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->query("SELECT bhw_id, full_name FROM bhw_users ORDER BY full_name ASC");
    $bhws = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

// Fetch record
$record = null;
if ($editId && in_array($action, ['edit', 'view'])) {
    try {
        $stmt = $pdo->prepare("SELECT n.*, p.full_name as linked_patient, b.full_name as bhw_name 
                               FROM ntp_client_monitoring n
                               LEFT JOIN patients p ON n.patient_id = p.patient_id
                               LEFT JOIN bhw_users b ON n.bhw_id = b.bhw_id
                               WHERE n.ntp_id = ?");
        $stmt->execute([$editId]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}

// Pagination
$page_num = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$per_page = 15;
$offset = ($page_num - 1) * $per_page;

$records = [];
$total_records = 0;
$search = $_GET['search'] ?? '';
$filterStatus = $_GET['status'] ?? '';

if ($action === 'list') {
    try {
        $whereConditions = [];
        $params = [];
        
        if ($search) {
            $whereConditions[] = "(patient_complete_name LIKE ? OR tb_case_no LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($filterStatus === 'ongoing') {
            $whereConditions[] = "outcome IS NULL OR outcome = ''";
        } elseif ($filterStatus === 'completed') {
            $whereConditions[] = "outcome IS NOT NULL AND outcome != ''";
        }
        
        $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ntp_client_monitoring $whereClause");
        $stmt->execute($params);
        $total_records = (int)$stmt->fetchColumn();
        
        $stmt = $pdo->prepare("SELECT n.*, b.full_name as bhw_name FROM ntp_client_monitoring n
                               LEFT JOIN bhw_users b ON n.bhw_id = b.bhw_id
                               $whereClause ORDER BY n.date_tx_started DESC LIMIT $per_page OFFSET $offset");
        $stmt->execute($params);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}

$total_pages = ceil($total_records / $per_page);

// Stats
$stats = ['total' => 0, 'ongoing' => 0, 'completed' => 0, 'new' => 0];
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total,
        SUM(outcome IS NULL OR outcome = '') as ongoing,
        SUM(outcome IS NOT NULL AND outcome != '') as completed,
        SUM(registration_type = 'New') as new_cases
        FROM ntp_client_monitoring");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total'] = (int)$row['total'];
    $stats['ongoing'] = (int)$row['ongoing'];
    $stats['completed'] = (int)$row['completed'];
    $stats['new'] = (int)$row['new_cases'];
} catch (PDOException $e) {}
?>

<div class="container-fluid py-4 fade-in">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin-health-records">Health Records</a></li>
                    <li class="breadcrumb-item active">NTP Monitoring</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0" style="color: #8b5cf6;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2" style="vertical-align: -4px;"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
                NTP Client Monitoring (TB Program)
            </h1>
        </div>
        <?php if ($action === 'list'): ?>
        <div class="d-flex gap-2">
            <?php if (has_permission('view_reports')): ?>
            <a href="<?php echo BASE_URL; ?>?action=report-health-records&report=ntp" class="btn btn-glass">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Export PDF
            </a>
            <?php endif; ?>
            <?php if (has_permission('manage_patients')): ?>
            <a href="<?php echo BASE_URL; ?>admin-health-records-ntp?action=add" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Client
            </a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <a href="<?php echo BASE_URL; ?>admin-health-records-ntp" class="btn btn-glass">← Back to List</a>
        <?php endif; ?>
    </div>

    <?php if ($action === 'list'): ?>
    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card"><div class="stat-card-content text-center">
                <div class="stat-card-value"><?php echo $stats['total']; ?></div>
                <div class="stat-card-label small">Total Clients</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card"><div class="stat-card-content text-center">
                <div class="stat-card-value" style="color: #f59e0b;"><?php echo $stats['ongoing']; ?></div>
                <div class="stat-card-label small">Ongoing Treatment</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card"><div class="stat-card-content text-center">
                <div class="stat-card-value" style="color: #10b981;"><?php echo $stats['completed']; ?></div>
                <div class="stat-card-label small">Treatment Completed</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card"><div class="stat-card-content text-center">
                <div class="stat-card-value" style="color: #3b82f6;"><?php echo $stats['new']; ?></div>
                <div class="stat-card-label small">New Cases</div>
            </div></div>
        </div>
    </div>

    <!-- Filter -->
    <div class="glass-card mb-4">
        <div class="glass-card-body">
            <form method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="admin-health-records-ntp">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" placeholder="Name, TB Case No..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All</option>
                        <option value="ongoing" <?php echo $filterStatus === 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                        <option value="completed" <?php echo $filterStatus === 'completed' ? 'selected' : ''; ?>>Completed</option>
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
        <div class="glass-card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Case No.</th>
                            <th>Patient Name</th>
                            <th>Age/Sex</th>
                            <th>Type</th>
                            <th>TX Started</th>
                            <th>Progress</th>
                            <th>Outcome</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">No records found</td></tr>
                        <?php else: ?>
                        <?php foreach ($records as $rec): ?>
                        <tr>
                            <td><span class="badge badge-secondary"><?php echo htmlspecialchars($rec['tb_case_no'] ?? '-'); ?></span></td>
                            <td><?php echo htmlspecialchars($rec['patient_complete_name']); ?></td>
                            <td><?php echo ($rec['age'] ?? '-') . ' / ' . ($rec['sex'] ?? '-'); ?></td>
                            <td>
                                <span class="badge <?php echo $rec['registration_type'] === 'New' ? 'badge-primary' : 'badge-warning'; ?>">
                                    <?php echo htmlspecialchars($rec['registration_type'] ?? '-'); ?>
                                </span>
                            </td>
                            <td><?php echo $rec['date_tx_started'] ? date('M j, Y', strtotime($rec['date_tx_started'])) : '-'; ?></td>
                            <td>
                                <?php
                                // Calculate progress based on months filled
                                $monthsFilled = 0;
                                for ($i = 1; $i <= 6; $i++) {
                                    if (!empty($rec["weight_month_$i"])) $monthsFilled++;
                                }
                                $progress = round(($monthsFilled / 6) * 100);
                                ?>
                                <div class="progress" style="height: 8px; width: 80px;">
                                    <div class="progress-bar" style="width: <?php echo $progress; ?>%; background: <?php echo $progress >= 100 ? '#10b981' : '#8b5cf6'; ?>;"></div>
                                </div>
                                <small class="text-muted"><?php echo $monthsFilled; ?>/6 months</small>
                            </td>
                            <td>
                                <?php if ($rec['outcome']): ?>
                                <span class="badge badge-success"><?php echo htmlspecialchars($rec['outcome']); ?></span>
                                <?php else: ?>
                                <span class="badge badge-warning">Ongoing</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?php echo BASE_URL; ?>admin-health-records-ntp?action=view&id=<?php echo $rec['ntp_id']; ?>" class="btn btn-sm btn-glass">View</a>
                                    <?php if (has_permission('manage_patients')): ?>
                                    <a href="<?php echo BASE_URL; ?>admin-health-records-ntp?action=edit&id=<?php echo $rec['ntp_id']; ?>" class="btn btn-sm btn-glass">Edit</a>
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
    <!-- Form -->
    <div class="glass-card">
        <div class="glass-card-header">
            <h5 class="glass-card-title mb-0"><?php echo $action === 'edit' ? 'Edit' : 'Add'; ?> NTP Client</h5>
        </div>
        <div class="glass-card-body">
            <form method="POST" action="<?php echo BASE_URL; ?>?action=save-ntp-client">
                <input type="hidden" name="ntp_id" value="<?php echo $record['ntp_id'] ?? ''; ?>">
                
                <div class="row g-3">
                    <!-- Patient Info -->
                    <div class="col-12"><h6 class="form-section-title mb-3">Patient Information</h6></div>
                    <div class="col-md-6">
                        <label class="form-label">Patient Complete Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="patient_complete_name" required value="<?php echo htmlspecialchars($record['patient_complete_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Age</label>
                        <input type="number" class="form-control" name="age" min="1" max="150" value="<?php echo htmlspecialchars($record['age'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sex</label>
                        <select class="form-select" name="sex">
                            <option value="">Select</option>
                            <option value="M" <?php echo ($record['sex'] ?? '') === 'M' ? 'selected' : ''; ?>>Male</option>
                            <option value="F" <?php echo ($record['sex'] ?? '') === 'F' ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Barangay Address</label>
                        <input type="text" class="form-control" name="barangay_address" value="<?php echo htmlspecialchars($record['barangay_address'] ?? ''); ?>">
                    </div>

                    <!-- TB Case Info -->
                    <div class="col-12 mt-4"><h6 class="form-section-title mb-3">TB Case Information</h6></div>
                    <div class="col-md-3">
                        <label class="form-label">TB Case No.</label>
                        <input type="text" class="form-control" name="tb_case_no" value="<?php echo htmlspecialchars($record['tb_case_no'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Registration Type</label>
                        <select class="form-select" name="registration_type">
                            <option value="New" <?php echo ($record['registration_type'] ?? '') === 'New' ? 'selected' : ''; ?>>New</option>
                            <option value="Relapsed" <?php echo ($record['registration_type'] ?? '') === 'Relapsed' ? 'selected' : ''; ?>>Relapsed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date TX Started <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="date_tx_started" required value="<?php echo htmlspecialchars($record['date_tx_started'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date Exam Before TX</label>
                        <input type="date" class="form-control" name="date_exam_before_tx" value="<?php echo htmlspecialchars($record['date_exam_before_tx'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Disease Classification</label>
                        <input type="text" class="form-control" name="disease_classification" placeholder="e.g., Pulmonary TB" value="<?php echo htmlspecialchars($record['disease_classification'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Initial Weight (kg)</label>
                        <input type="number" class="form-control" name="initial_weight" step="0.1" value="<?php echo htmlspecialchars($record['initial_weight'] ?? ''); ?>">
                    </div>

                    <!-- Monthly Weighing -->
                    <div class="col-12 mt-4"><h6 class="form-section-title mb-3">Monthly Weighing Schedule (kg)</h6></div>
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                    <div class="col-md-2">
                        <label class="form-label">Month <?php echo $i; ?></label>
                        <input type="number" class="form-control" name="weight_month_<?php echo $i; ?>" step="0.1" value="<?php echo htmlspecialchars($record["weight_month_$i"] ?? ''); ?>">
                    </div>
                    <?php endfor; ?>

                    <!-- Outcome -->
                    <div class="col-12 mt-4"><h6 class="form-section-title mb-3">Treatment Outcome</h6></div>
                    <div class="col-md-4">
                        <label class="form-label">End of Treatment Date</label>
                        <input type="date" class="form-control" name="end_of_treatment" value="<?php echo htmlspecialchars($record['end_of_treatment'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Outcome</label>
                        <select class="form-select" name="outcome">
                            <option value="">-- Select (Leave blank if ongoing) --</option>
                            <option value="Cured" <?php echo ($record['outcome'] ?? '') === 'Cured' ? 'selected' : ''; ?>>Cured</option>
                            <option value="Treatment Completed" <?php echo ($record['outcome'] ?? '') === 'Treatment Completed' ? 'selected' : ''; ?>>Treatment Completed</option>
                            <option value="Lost to Follow-up" <?php echo ($record['outcome'] ?? '') === 'Lost to Follow-up' ? 'selected' : ''; ?>>Lost to Follow-up</option>
                            <option value="Died" <?php echo ($record['outcome'] ?? '') === 'Died' ? 'selected' : ''; ?>>Died</option>
                            <option value="Treatment Failed" <?php echo ($record['outcome'] ?? '') === 'Treatment Failed' ? 'selected' : ''; ?>>Treatment Failed</option>
                        </select>
                    </div>
                    <div class="col-md-4">
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
                    <a href="<?php echo BASE_URL; ?>admin-health-records-ntp" class="btn btn-glass">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Record</button>
                </div>
            </form>
        </div>
    </div>

    <?php elseif ($action === 'view' && $record): ?>
    <!-- View -->
    <div class="glass-card">
        <div class="glass-card-header d-flex justify-content-between align-items-center">
            <h5 class="glass-card-title mb-0">NTP Client Details</h5>
            <?php if (has_permission('manage_patients')): ?>
            <a href="<?php echo BASE_URL; ?>admin-health-records-ntp?action=edit&id=<?php echo $record['ntp_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
            <?php endif; ?>
        </div>
        <div class="glass-card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Patient Information</h6>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Name:</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($record['patient_complete_name']); ?></dd>
                        <dt class="col-sm-5">Age/Sex:</dt>
                        <dd class="col-sm-7"><?php echo ($record['age'] ?? '-') . ' / ' . ($record['sex'] ?? '-'); ?></dd>
                        <dt class="col-sm-5">Address:</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($record['barangay_address'] ?? '-'); ?></dd>
                        <dt class="col-sm-5">TB Case No:</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($record['tb_case_no'] ?? '-'); ?></dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Treatment Information</h6>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Type:</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($record['registration_type'] ?? '-'); ?></dd>
                        <dt class="col-sm-5">TX Started:</dt>
                        <dd class="col-sm-7"><?php echo $record['date_tx_started'] ? date('M j, Y', strtotime($record['date_tx_started'])) : '-'; ?></dd>
                        <dt class="col-sm-5">Classification:</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($record['disease_classification'] ?? '-'); ?></dd>
                        <dt class="col-sm-5">Outcome:</dt>
                        <dd class="col-sm-7">
                            <?php if ($record['outcome']): ?>
                            <span class="badge badge-success"><?php echo htmlspecialchars($record['outcome']); ?></span>
                            <?php else: ?>
                            <span class="badge badge-warning">Ongoing</span>
                            <?php endif; ?>
                        </dd>
                    </dl>
                </div>
                <div class="col-12">
                    <h6 class="text-muted mb-3">Weight Progress (kg)</h6>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Initial</th>
                                    <?php for ($i = 1; $i <= 6; $i++): ?>
                                    <th>Month <?php echo $i; ?></th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo $record['initial_weight'] ?? '-'; ?></td>
                                    <?php for ($i = 1; $i <= 6; $i++): ?>
                                    <td><?php echo $record["weight_month_$i"] ?? '-'; ?></td>
                                    <?php endfor; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.form-section-title { font-weight: 600; color: var(--text-primary); border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; }
.breadcrumb { background: transparent; padding: 0; margin: 0; font-size: 0.875rem; }
.breadcrumb-item a { color: var(--primary); text-decoration: none; }
dl dt { color: var(--text-muted); font-weight: 500; }
dl dd { color: var(--text-primary); }
</style>

<?php include __DIR__ . '/../../../includes/footer_admin.php'; ?>
