<?php
/**
 * Mortality (Death) Records
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
        $stmt = $pdo->prepare("SELECT m.*, p.full_name as linked_patient, b.full_name as bhw_name 
                               FROM mortality_records m
                               LEFT JOIN patients p ON m.patient_id = p.patient_id
                               LEFT JOIN bhw_users b ON m.bhw_id = b.bhw_id
                               WHERE m.mortality_id = ?");
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
$filterCause = $_GET['cause'] ?? '';
$filterYear = $_GET['year'] ?? date('Y');

if ($action === 'list') {
    try {
        $whereConditions = [];
        $params = [];
        
        if ($search) {
            $whereConditions[] = "deceased_name LIKE ?";
            $params[] = "%$search%";
        }
        
        if ($filterCause) {
            $whereConditions[] = "cause_of_death LIKE ?";
            $params[] = "%$filterCause%";
        }
        
        if ($filterYear) {
            $whereConditions[] = "YEAR(date_of_death) = ?";
            $params[] = $filterYear;
        }
        
        $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM mortality_records $whereClause");
        $stmt->execute($params);
        $total_records = (int)$stmt->fetchColumn();
        
        $stmt = $pdo->prepare("SELECT m.*, b.full_name as bhw_name FROM mortality_records m
                               LEFT JOIN bhw_users b ON m.bhw_id = b.bhw_id
                               $whereClause ORDER BY m.date_of_death DESC LIMIT $per_page OFFSET $offset");
        $stmt->execute($params);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}

$total_pages = ceil($total_records / $per_page);

// Stats
$stats = ['total' => 0, 'male' => 0, 'female' => 0, 'infant' => 0, 'maternal' => 0];
try {
    $yearCond = $filterYear ? "WHERE YEAR(date_of_death) = '$filterYear'" : "";
    $stmt = $pdo->query("SELECT COUNT(*) as total,
        SUM(sex = 'Male') as male,
        SUM(sex = 'Female') as female,
        SUM(age_at_death < 1) as infant,
        SUM(is_maternal_death = 1) as maternal
        FROM mortality_records $yearCond");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats = [
        'total' => (int)$row['total'],
        'male' => (int)$row['male'],
        'female' => (int)$row['female'],
        'infant' => (int)$row['infant'],
        'maternal' => (int)$row['maternal']
    ];
} catch (PDOException $e) {}

// Get available years
$years = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT YEAR(date_of_death) as yr FROM mortality_records ORDER BY yr DESC");
    $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array(date('Y'), $years)) array_unshift($years, date('Y'));
} catch (PDOException $e) { $years = [date('Y')]; }
?>

<div class="container-fluid py-4 fade-in">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin-health-records">Health Records</a></li>
                    <li class="breadcrumb-item active">Mortality Records</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0" style="color: #6b7280;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2" style="vertical-align: -4px;"><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/><path d="M12 8v4l2 2"/></svg>
                Mortality Records
            </h1>
        </div>
        <?php if ($action === 'list'): ?>
        <div class="d-flex gap-2">
            <?php if (has_permission('view_reports')): ?>
            <a href="<?php echo BASE_URL; ?>?action=report-health-records&report=mortality&year=<?php echo $filterYear ?: date('Y'); ?>" class="btn btn-glass">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Export PDF
            </a>
            <?php endif; ?>
            <?php if (has_permission('manage_patients')): ?>
            <a href="<?php echo BASE_URL; ?>admin-health-records-mortality?action=add" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Record
            </a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <a href="<?php echo BASE_URL; ?>admin-health-records-mortality" class="btn btn-glass">← Back to List</a>
        <?php endif; ?>
    </div>

    <?php if ($action === 'list'): ?>
    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-2">
            <div class="stat-card"><div class="stat-card-content text-center">
                <div class="stat-card-value"><?php echo $stats['total']; ?></div>
                <div class="stat-card-label small">Total <?php echo $filterYear; ?></div>
            </div></div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stat-card"><div class="stat-card-content text-center">
                <div class="stat-card-value" style="color: #3b82f6;"><?php echo $stats['male']; ?></div>
                <div class="stat-card-label small">Male</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stat-card"><div class="stat-card-content text-center">
                <div class="stat-card-value" style="color: #ec4899;"><?php echo $stats['female']; ?></div>
                <div class="stat-card-label small">Female</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card"><div class="stat-card-content text-center">
                <div class="stat-card-value" style="color: #f97316;"><?php echo $stats['infant']; ?></div>
                <div class="stat-card-label small">Infant Deaths</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card"><div class="stat-card-content text-center">
                <div class="stat-card-value" style="color: #ef4444;"><?php echo $stats['maternal']; ?></div>
                <div class="stat-card-label small">Maternal Deaths</div>
            </div></div>
        </div>
    </div>

    <!-- Filter -->
    <div class="glass-card mb-4">
        <div class="glass-card-body">
            <form method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="admin-health-records-mortality">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" placeholder="Name..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Year</label>
                    <select class="form-select" name="year">
                        <option value="">All Years</option>
                        <?php foreach ($years as $y): ?>
                        <option value="<?php echo $y; ?>" <?php echo $filterYear == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cause</label>
                    <input type="text" class="form-control" name="cause" placeholder="Cause of death..." value="<?php echo htmlspecialchars($filterCause); ?>">
                </div>
                <div class="col-md-2">
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
                            <th>Deceased Name</th>
                            <th>Date of Death</th>
                            <th>Age</th>
                            <th>Sex</th>
                            <th>Cause</th>
                            <th>Place</th>
                            <th>Flags</th>
                            <?php if (is_superadmin()): ?>
                            <th>BHW</th>
                            <?php endif; ?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                        <tr><td colspan="<?php echo is_superadmin() ? '9' : '8'; ?>" class="text-center py-5 text-muted">No records found</td></tr>
                        <?php else: ?>
                        <?php foreach ($records as $rec): ?>
                        <tr>
                            <td class="fw-medium"><?php echo htmlspecialchars($rec['deceased_name']); ?></td>
                            <td><?php echo $rec['date_of_death'] ? date('M j, Y', strtotime($rec['date_of_death'])) : '-'; ?></td>
                            <td><?php echo $rec['age_at_death'] !== null ? ($rec['age_at_death'] < 1 ? '<1 yr' : $rec['age_at_death'] . ' yrs') : '-'; ?></td>
                            <td>
                                <span class="badge" style="background:<?php echo ($rec['sex'] ?? '') === 'Male' ? '#3b82f620' : '#ec489920'; ?>;color:<?php echo ($rec['sex'] ?? '') === 'Male' ? '#3b82f6' : '#ec4899'; ?>;">
                                    <?php echo htmlspecialchars($rec['sex'] ?? '-'); ?>
                                </span>
                            </td>
                            <td><span class="text-truncate d-inline-block" style="max-width:150px;"><?php echo htmlspecialchars($rec['cause_of_death'] ?? '-'); ?></span></td>
                            <td><?php echo htmlspecialchars($rec['place_of_death'] ?? '-'); ?></td>
                            <td>
                                <?php if ($rec['is_maternal_death']): ?>
                                <span class="badge badge-danger">Maternal</span>
                                <?php endif; ?>
                                <?php if ($rec['age_at_death'] !== null && $rec['age_at_death'] < 1): ?>
                                <span class="badge badge-warning">Infant</span>
                                <?php endif; ?>
                            </td>
                            <?php if (is_superadmin()): ?>
                            <td><span class="text-muted small"><?php echo htmlspecialchars($rec['bhw_name'] ?? 'Unknown'); ?></span></td>
                            <?php endif; ?>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?php echo BASE_URL; ?>admin-health-records-mortality?action=view&id=<?php echo $rec['mortality_id']; ?>" class="btn btn-sm btn-glass">View</a>
                                    <?php if (has_permission('manage_patients')): ?>
                                    <a href="<?php echo BASE_URL; ?>admin-health-records-mortality?action=edit&id=<?php echo $rec['mortality_id']; ?>" class="btn btn-sm btn-glass">Edit</a>
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

    <?php elseif ($action === 'view' && $record): ?>
    <!-- View Details -->
    <div class="glass-card">
        <div class="glass-card-header d-flex justify-content-between align-items-center">
            <h5 class="glass-card-title mb-0">Death Record Details</h5>
            <?php if (has_permission('manage_patients')): ?>
            <a href="<?php echo BASE_URL; ?>admin-health-records-mortality?action=edit&id=<?php echo $record['mortality_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
            <?php endif; ?>
        </div>
        <div class="glass-card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted small mb-2">Deceased Information</h6>
                    <div class="p-3 rounded" style="background: var(--glass-bg);">
                        <p class="mb-2"><strong>Name:</strong> <?php echo htmlspecialchars($record['deceased_name']); ?></p>
                        <p class="mb-2"><strong>Sex:</strong> <?php echo htmlspecialchars($record['sex'] ?? '-'); ?></p>
                        <p class="mb-2"><strong>Age at Death:</strong> <?php echo $record['age_at_death'] ?? '-'; ?> years</p>
                        <p class="mb-0"><strong>Date of Birth:</strong> <?php echo $record['date_of_birth'] ? date('F j, Y', strtotime($record['date_of_birth'])) : '-'; ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small mb-2">Death Details</h6>
                    <div class="p-3 rounded" style="background: var(--glass-bg);">
                        <p class="mb-2"><strong>Date of Death:</strong> <?php echo $record['date_of_death'] ? date('F j, Y', strtotime($record['date_of_death'])) : '-'; ?></p>
                        <p class="mb-2"><strong>Place of Death:</strong> <?php echo htmlspecialchars($record['place_of_death'] ?? '-'); ?></p>
                        <p class="mb-2"><strong>Cause of Death:</strong> <?php echo htmlspecialchars($record['cause_of_death'] ?? '-'); ?></p>
                        <p class="mb-0">
                            <?php if ($record['is_maternal_death']): ?>
                            <span class="badge badge-danger">Maternal Death</span>
                            <?php endif; ?>
                            <?php if ($record['age_at_death'] !== null && $record['age_at_death'] < 1): ?>
                            <span class="badge badge-warning">Infant Death</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <?php if ($record['remarks']): ?>
                <div class="col-12">
                    <h6 class="text-muted small mb-2">Remarks</h6>
                    <div class="p-3 rounded" style="background: var(--glass-bg);">
                        <?php echo nl2br(htmlspecialchars($record['remarks'])); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php elseif ($action === 'add' || $action === 'edit'): ?>
    <!-- Form -->
    <div class="glass-card">
        <div class="glass-card-header">
            <h5 class="glass-card-title mb-0"><?php echo $action === 'edit' ? 'Edit' : 'Add'; ?> Mortality Record</h5>
        </div>
        <div class="glass-card-body">
            <form method="POST" action="<?php echo BASE_URL; ?>?action=save-mortality-record">
                <input type="hidden" name="mortality_id" value="<?php echo $record['mortality_id'] ?? ''; ?>">
                
                <h6 class="form-section-title mb-3">Deceased Information</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="deceased_name" required value="<?php echo htmlspecialchars($record['deceased_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sex</label>
                        <select class="form-select" name="sex">
                            <option value="">Select</option>
                            <option value="Male" <?php echo ($record['sex'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($record['sex'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Age at Death</label>
                        <input type="number" class="form-control" name="age_at_death" min="0" step="0.1" value="<?php echo htmlspecialchars($record['age_at_death'] ?? ''); ?>" placeholder="Years">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth" value="<?php echo htmlspecialchars($record['date_of_birth'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Link to Patient</label>
                        <select class="form-select" name="patient_id">
                            <option value="">-- Optional --</option>
                            <?php foreach ($patients as $p): ?>
                            <option value="<?php echo $p['patient_id']; ?>" <?php echo ($record['patient_id'] ?? '') == $p['patient_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['full_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <h6 class="form-section-title mb-3">Death Details</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Date of Death <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="date_of_death" required value="<?php echo htmlspecialchars($record['date_of_death'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Place of Death</label>
                        <input type="text" class="form-control" name="place_of_death" value="<?php echo htmlspecialchars($record['place_of_death'] ?? ''); ?>" placeholder="Hospital, Home, etc.">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">BHW Who Recorded</label>
                        <select class="form-select" name="bhw_id">
                            <option value="">-- Select --</option>
                            <?php foreach ($bhws as $b): ?>
                            <option value="<?php echo $b['bhw_id']; ?>" <?php echo ($record['bhw_id'] ?? $_SESSION['bhw_id']) == $b['bhw_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($b['full_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Cause of Death</label>
                        <input type="text" class="form-control" name="cause_of_death" value="<?php echo htmlspecialchars($record['cause_of_death'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Classification</label>
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" name="is_maternal_death" id="maternalDeath" value="1" <?php echo ($record['is_maternal_death'] ?? 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="maternalDeath">Maternal Death</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" name="remarks" rows="3"><?php echo htmlspecialchars($record['remarks'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="<?php echo BASE_URL; ?>admin-health-records-mortality" class="btn btn-glass">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Record</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.form-section-title { font-weight: 600; color: var(--text-primary); border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; }
.breadcrumb { background: transparent; padding: 0; margin: 0; font-size: 0.875rem; }
.breadcrumb-item a { color: var(--primary); text-decoration: none; }
</style>

<?php include __DIR__ . '/../../../includes/footer_admin.php'; ?>
