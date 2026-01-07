<?php
/**
 * WRA (Women of Reproductive Age) Tracking
 * E-BHM Connect - Glassmorphism Design
 */
include __DIR__ . '/../../../includes/header_admin.php';

$action = $_GET['action'] ?? 'list';
$editId = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Fetch patients & BHWs
$patients = [];
$bhws = [];
try {
    $stmt = $pdo->query("SELECT patient_id, full_name FROM patients WHERE sex = 'Female' OR sex IS NULL ORDER BY full_name ASC");
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->query("SELECT bhw_id, full_name FROM bhw_users ORDER BY full_name ASC");
    $bhws = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

// Fetch record
$record = null;
if ($editId && in_array($action, ['edit', 'view'])) {
    try {
        $stmt = $pdo->prepare("SELECT w.*, p.full_name as linked_patient, b.full_name as bhw_name 
                               FROM wra_tracking w
                               LEFT JOIN patients p ON w.patient_id = p.patient_id
                               LEFT JOIN bhw_users b ON w.bhw_id = b.bhw_id
                               WHERE w.wra_id = ?");
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
$filterYear = $_GET['year'] ?? date('Y');
$filterMethod = $_GET['method'] ?? '';

if ($action === 'list') {
    try {
        $whereConditions = ["(tracking_year = ? OR tracking_year IS NULL)"];
        $params = [$filterYear];
        
        if ($search) {
            $whereConditions[] = "name LIKE ?";
            $params[] = "%$search%";
        }
        
        if ($filterMethod) {
            $whereConditions[] = "family_planning_method = ?";
            $params[] = $filterMethod;
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM wra_tracking $whereClause");
        $stmt->execute($params);
        $total_records = (int)$stmt->fetchColumn();
        
        $stmt = $pdo->prepare("SELECT w.*, b.full_name as bhw_name FROM wra_tracking w
                               LEFT JOIN bhw_users b ON w.bhw_id = b.bhw_id
                               $whereClause ORDER BY w.name ASC LIMIT $per_page OFFSET $offset");
        $stmt->execute($params);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}

$total_pages = ceil($total_records / $per_page);

// FP Methods for dropdown
$fpMethods = ['Pills', 'IUD', 'Condom', 'Injectable', 'Implant', 'LAM', 'BTL', 'Vasectomy', 'NFP/CMM', 'SDM', 'None'];

// Get available years
$years = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT tracking_year FROM wra_tracking WHERE tracking_year IS NOT NULL ORDER BY tracking_year DESC");
    $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {}
if (!in_array(date('Y'), $years)) array_unshift($years, date('Y'));

// Stats
$stats = ['total' => 0, 'nhts' => 0, 'with_fp' => 0];
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total,
        SUM(is_nhts = 1) as nhts,
        SUM(family_planning_method IS NOT NULL AND family_planning_method != '' AND family_planning_method != 'None') as with_fp
        FROM wra_tracking WHERE tracking_year = ? OR tracking_year IS NULL");
    $stmt->execute([$filterYear]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total'] = (int)$row['total'];
    $stats['nhts'] = (int)$row['nhts'];
    $stats['with_fp'] = (int)$row['with_fp'];
} catch (PDOException $e) {}

$months = ['jan' => 'Jan', 'feb' => 'Feb', 'mar' => 'Mar', 'apr' => 'Apr', 'may' => 'May', 'jun' => 'Jun',
           'jul' => 'Jul', 'aug' => 'Aug', 'sep' => 'Sep', 'oct' => 'Oct', 'nov' => 'Nov', 'dec' => 'Dec'];
?>

<div class="container-fluid py-4 fade-in">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin-health-records">Health Records</a></li>
                    <li class="breadcrumb-item active">WRA Tracking</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0" style="color: #06b6d4;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2" style="vertical-align: -4px;"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
                WRA Tracking Tool <?php echo $filterYear; ?>
            </h1>
        </div>
        <?php if ($action === 'list'): ?>
        <div class="d-flex gap-2">
            <?php if (has_permission('view_reports')): ?>
            <a href="<?php echo BASE_URL; ?>?action=report-health-records&report=wra&year=<?php echo $filterYear ?: date('Y'); ?>" class="btn btn-glass">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Export PDF
            </a>
            <?php endif; ?>
            <?php if (has_permission('manage_patients')): ?>
            <a href="<?php echo BASE_URL; ?>admin-health-records-wra?action=add" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Record
            </a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <a href="<?php echo BASE_URL; ?>admin-health-records-wra" class="btn btn-glass">← Back to List</a>
        <?php endif; ?>
    </div>

    <?php if ($action === 'list'): ?>
    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="stat-card"><div class="stat-card-content text-center">
                <div class="stat-card-value"><?php echo $stats['total']; ?></div>
                <div class="stat-card-label small">Total WRA</div>
            </div></div>
        </div>
        <div class="col-4">
            <div class="stat-card"><div class="stat-card-content text-center">
                <div class="stat-card-value" style="color: #10b981;"><?php echo $stats['nhts']; ?></div>
                <div class="stat-card-label small">NHTS Members</div>
            </div></div>
        </div>
        <div class="col-4">
            <div class="stat-card"><div class="stat-card-content text-center">
                <div class="stat-card-value" style="color: #8b5cf6;"><?php echo $stats['with_fp']; ?></div>
                <div class="stat-card-label small">With FP Method</div>
            </div></div>
        </div>
    </div>

    <!-- Filter -->
    <div class="glass-card mb-4">
        <div class="glass-card-body">
            <form method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="admin-health-records-wra">
                <div class="col-md-4">
                    <label class="form-label">Search Name</label>
                    <input type="text" class="form-control" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Year</label>
                    <select class="form-select" name="year">
                        <?php foreach ($years as $y): ?>
                        <option value="<?php echo $y; ?>" <?php echo $filterYear == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">FP Method</label>
                    <select class="form-select" name="method">
                        <option value="">All Methods</option>
                        <?php foreach ($fpMethods as $m): ?>
                        <option value="<?php echo $m; ?>" <?php echo $filterMethod === $m ? 'selected' : ''; ?>><?php echo $m; ?></option>
                        <?php endforeach; ?>
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
                            <th style="min-width:150px;">Name</th>
                            <th>Age</th>
                            <th>NHTS</th>
                            <th>FP Method</th>
                            <?php foreach ($months as $k => $label): ?>
                            <th class="text-center" style="min-width:45px;"><?php echo $label; ?></th>
                            <?php endforeach; ?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                        <tr><td colspan="17" class="text-center py-5 text-muted">No records found</td></tr>
                        <?php else: ?>
                        <?php foreach ($records as $rec): ?>
                        <tr>
                            <td>
                                <div class="fw-medium"><?php echo htmlspecialchars($rec['name']); ?></div>
                            </td>
                            <td><?php echo $rec['age'] ?? '-'; ?></td>
                            <td>
                                <?php if ($rec['is_nhts']): ?>
                                <span class="badge badge-success">Yes</span>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($rec['family_planning_method'] && $rec['family_planning_method'] !== 'None'): ?>
                                <span class="badge badge-primary"><?php echo htmlspecialchars($rec['family_planning_method']); ?></span>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <?php foreach ($months as $k => $label): ?>
                            <td class="text-center">
                                <?php 
                                $status = $rec["status_$k"] ?? '';
                                if ($status): 
                                    $statusColor = match(strtoupper(substr($status, 0, 1))) {
                                        'P' => '#ec4899', // Pregnant
                                        'A' => '#10b981', // Active
                                        'D' => '#6b7280', // Dropout
                                        default => '#94a3b8'
                                    };
                                ?>
                                <span class="badge" style="background:<?php echo $statusColor; ?>20;color:<?php echo $statusColor; ?>;font-size:0.65rem;padding:2px 4px;">
                                    <?php echo htmlspecialchars(substr($status, 0, 2)); ?>
                                </span>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <?php endforeach; ?>
                            <td>
                                <div class="d-flex gap-1">
                                    <?php if (has_permission('manage_patients')): ?>
                                    <a href="<?php echo BASE_URL; ?>admin-health-records-wra?action=edit&id=<?php echo $rec['wra_id']; ?>" class="btn btn-sm btn-glass" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    <?php else: ?>
                                    <span class="text-muted">View Only</span>
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
            <h5 class="glass-card-title mb-0"><?php echo $action === 'edit' ? 'Edit' : 'Add'; ?> WRA Entry</h5>
        </div>
        <div class="glass-card-body">
            <form method="POST" action="<?php echo BASE_URL; ?>?action=save-wra-tracking">
                <input type="hidden" name="wra_id" value="<?php echo $record['wra_id'] ?? ''; ?>">
                <?php echo csrf_input(); ?>
                
                <div class="row g-3">
                    <!-- Personal Info -->
                    <div class="col-12"><h6 class="form-section-title mb-3">Personal Information</h6></div>
                    <div class="col-md-6">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required value="<?php echo htmlspecialchars($record['name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Age</label>
                        <input type="number" class="form-control" name="age" min="10" max="60" value="<?php echo htmlspecialchars($record['age'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Birthdate</label>
                        <input type="date" class="form-control" name="birthdate" value="<?php echo htmlspecialchars($record['birthdate'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">NHTS</label>
                        <select class="form-select" name="is_nhts">
                            <option value="0" <?php echo ($record['is_nhts'] ?? 0) == 0 ? 'selected' : ''; ?>>No</option>
                            <option value="1" <?php echo ($record['is_nhts'] ?? 0) == 1 ? 'selected' : ''; ?>>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Complete Address</label>
                        <input type="text" class="form-control" name="complete_address" value="<?php echo htmlspecialchars($record['complete_address'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" name="contact_number" value="<?php echo htmlspecialchars($record['contact_number'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tracking Year</label>
                        <select class="form-select" name="tracking_year">
                            <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                            <option value="<?php echo $y; ?>" <?php echo ($record['tracking_year'] ?? date('Y')) == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- FP Method -->
                    <div class="col-12 mt-4"><h6 class="form-section-title mb-3">Family Planning</h6></div>
                    <div class="col-md-6">
                        <label class="form-label">Family Planning Method</label>
                        <select class="form-select" name="family_planning_method">
                            <option value="">-- Select --</option>
                            <?php foreach ($fpMethods as $m): ?>
                            <option value="<?php echo $m; ?>" <?php echo ($record['family_planning_method'] ?? '') === $m ? 'selected' : ''; ?>><?php echo $m; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">BHW Assigned</label>
                        <select class="form-select" name="bhw_id">
                            <option value="">-- Select --</option>
                            <?php foreach ($bhws as $b): ?>
                            <option value="<?php echo $b['bhw_id']; ?>" <?php echo ($record['bhw_id'] ?? $_SESSION['bhw_id']) == $b['bhw_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($b['full_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Monthly Status -->
                    <div class="col-12 mt-4"><h6 class="form-section-title mb-3">Monthly Status (P=Pregnant, A=Active, D=Dropout, etc.)</h6></div>
                    <?php foreach ($months as $k => $label): ?>
                    <div class="col-md-2 col-4">
                        <label class="form-label"><?php echo $label; ?></label>
                        <input type="text" class="form-control" name="status_<?php echo $k; ?>" maxlength="10" placeholder="Status" value="<?php echo htmlspecialchars($record["status_$k"] ?? ''); ?>">
                    </div>
                    <?php endforeach; ?>

                    <!-- Remarks -->
                    <div class="col-12 mt-4">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" name="remarks" rows="2"><?php echo htmlspecialchars($record['remarks'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="<?php echo BASE_URL; ?>admin-health-records-wra" class="btn btn-glass">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Entry</button>
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
dl dt { color: var(--text-muted); font-weight: 500; }
dl dd { color: var(--text-primary); }
</style>

<?php include __DIR__ . '/../../../includes/footer_admin.php'; ?>
