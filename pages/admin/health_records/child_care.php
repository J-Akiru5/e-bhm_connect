<?php
/**
 * Child Care Records (12-59 Months)
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
        $stmt = $pdo->prepare("SELECT c.*, p.full_name as linked_patient, b.full_name as bhw_name 
                               FROM child_care_records c
                               LEFT JOIN patients p ON c.patient_id = p.patient_id
                               LEFT JOIN bhw_users b ON c.bhw_id = b.bhw_id
                               WHERE c.child_care_id = ?");
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
$filterSex = $_GET['sex'] ?? '';

if ($action === 'list') {
    try {
        $whereConditions = [];
        $params = [];
        
        if ($search) {
            $whereConditions[] = "child_name LIKE ?";
            $params[] = "%$search%";
        }
        
        if ($filterSex) {
            $whereConditions[] = "sex = ?";
            $params[] = $filterSex;
        }
        
        $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM child_care_records $whereClause");
        $stmt->execute($params);
        $total_records = (int)$stmt->fetchColumn();
        
        $stmt = $pdo->prepare("SELECT c.*, b.full_name as bhw_name FROM child_care_records c
                               LEFT JOIN bhw_users b ON c.bhw_id = b.bhw_id
                               $whereClause ORDER BY c.date_of_birth DESC LIMIT $per_page OFFSET $offset");
        $stmt->execute($params);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}

$total_pages = ceil($total_records / $per_page);

// Stats
$stats = ['total' => 0, 'male' => 0, 'female' => 0, 'vit_a' => 0, 'deworm' => 0];
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total,
        SUM(sex = 'Male') as male,
        SUM(sex = 'Female') as female,
        SUM(vitamin_a_date IS NOT NULL) as vit_a,
        SUM(albendazole_date IS NOT NULL) as deworm
        FROM child_care_records");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats = [
        'total' => (int)$row['total'],
        'male' => (int)$row['male'],
        'female' => (int)$row['female'],
        'vit_a' => (int)$row['vit_a'],
        'deworm' => (int)$row['deworm']
    ];
} catch (PDOException $e) {}
?>

<div class="container-fluid py-4 fade-in">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin-health-records">Health Records</a></li>
                    <li class="breadcrumb-item active">Child Care (12-59 Months)</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0" style="color: #f59e0b;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2" style="vertical-align: -4px;"><path d="M9 12h.01"/><path d="M15 12h.01"/><path d="M10 16c.5.3 1.2.5 2 .5s1.5-.2 2-.5"/><path d="M19 6.3a9 9 0 0 1 1.8 3.9 2 2 0 0 1 0 3.6 9 9 0 0 1-17.6 0 2 2 0 0 1 0-3.6A9 9 0 0 1 12 3c2 0 3.5 1.1 3.5 2.5s-.9 2.5-2 2.5c-.8 0-1.5-.4-1.5-1"/></svg>
                Child Care Records (12-59 Months)
            </h1>
        </div>
        <?php if ($action === 'list'): ?>
        <div class="d-flex gap-2">
            <?php if (has_permission('view_reports')): ?>
            <a href="<?php echo BASE_URL; ?>?action=report-health-records&report=childcare" class="btn btn-glass">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Export PDF
            </a>
            <?php endif; ?>
            <?php if (has_permission('manage_patients')): ?>
            <a href="<?php echo BASE_URL; ?>admin-health-records-childcare?action=add" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Child
            </a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <a href="<?php echo BASE_URL; ?>admin-health-records-childcare" class="btn btn-glass">← Back to List</a>
        <?php endif; ?>
    </div>

    <?php if ($action === 'list'): ?>
    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-2">
            <div class="stat-card"><div class="stat-card-content text-center">
                <div class="stat-card-value"><?php echo $stats['total']; ?></div>
                <div class="stat-card-label small">Total Children</div>
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
                <div class="stat-card-value" style="color: #f59e0b;"><?php echo $stats['vit_a']; ?></div>
                <div class="stat-card-label small">Vitamin A Given</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card"><div class="stat-card-content text-center">
                <div class="stat-card-value" style="color: #10b981;"><?php echo $stats['deworm']; ?></div>
                <div class="stat-card-label small">Dewormed</div>
            </div></div>
        </div>
    </div>

    <!-- Filter -->
    <div class="glass-card mb-4">
        <div class="glass-card-body">
            <form method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="admin-health-records-childcare">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" placeholder="Child name..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sex</label>
                    <select class="form-select" name="sex">
                        <option value="">All</option>
                        <option value="Male" <?php echo $filterSex === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $filterSex === 'Female' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <div class="col-md-4">
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
                            <th>Child Name</th>
                            <th>Age (Months)</th>
                            <th>Sex</th>
                            <th>Date of Birth</th>
                            <th>Vitamin A</th>
                            <th>Albendazole</th>
                            <th>BHW</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">No records found</td></tr>
                        <?php else: ?>
                        <?php foreach ($records as $rec): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm" style="width:32px;height:32px;border-radius:8px;background:<?php echo $rec['sex'] === 'Male' ? '#3b82f620' : '#ec489920'; ?>;color:<?php echo $rec['sex'] === 'Male' ? '#3b82f6' : '#ec4899'; ?>;display:flex;align-items:center;justify-content:center;">
                                        <?php echo $rec['sex'] === 'Male' ? '♂' : '♀'; ?>
                                    </div>
                                    <span class="fw-medium"><?php echo htmlspecialchars($rec['child_name']); ?></span>
                                </div>
                            </td>
                            <td><?php echo $rec['age_months'] ?? '-'; ?> mo</td>
                            <td>
                                <span class="badge" style="background:<?php echo $rec['sex'] === 'Male' ? '#3b82f620' : '#ec489920'; ?>;color:<?php echo $rec['sex'] === 'Male' ? '#3b82f6' : '#ec4899'; ?>;">
                                    <?php echo htmlspecialchars($rec['sex'] ?? '-'); ?>
                                </span>
                            </td>
                            <td><?php echo $rec['date_of_birth'] ? date('M j, Y', strtotime($rec['date_of_birth'])) : '-'; ?></td>
                            <td>
                                <?php if ($rec['vitamin_a_date']): ?>
                                <span class="badge badge-success"><?php echo date('M j, Y', strtotime($rec['vitamin_a_date'])); ?></span>
                                <?php else: ?>
                                <span class="badge badge-warning">Not Given</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($rec['albendazole_date']): ?>
                                <span class="badge badge-success"><?php echo date('M j, Y', strtotime($rec['albendazole_date'])); ?></span>
                                <?php else: ?>
                                <span class="badge badge-warning">Not Given</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($rec['bhw_name'] ?? '-'); ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <?php if (has_permission('manage_patients')): ?>
                                    <a href="<?php echo BASE_URL; ?>admin-health-records-childcare?action=edit&id=<?php echo $rec['child_care_id']; ?>" class="btn btn-sm btn-glass">Edit</a>
                                    <button type="button" class="btn btn-sm btn-glass text-danger" onclick="confirmDelete(<?php echo $rec['child_care_id']; ?>)" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
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
            <h5 class="glass-card-title mb-0"><?php echo $action === 'edit' ? 'Edit' : 'Add'; ?> Child Record</h5>
        </div>
        <div class="glass-card-body">
            <form method="POST" action="<?php echo BASE_URL; ?>?action=save-childcare-record">
                <?php echo csrf_input(); ?>
                <input type="hidden" name="child_care_id" value="<?php echo $record['child_care_id'] ?? ''; ?>">
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Child Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="child_name" required value="<?php echo htmlspecialchars($record['child_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Age (Months)</label>
                        <input type="number" class="form-control" name="age_months" min="12" max="59" value="<?php echo htmlspecialchars($record['age_months'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sex</label>
                        <select class="form-select" name="sex">
                            <option value="">Select</option>
                            <option value="Male" <?php echo ($record['sex'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($record['sex'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth" value="<?php echo htmlspecialchars($record['date_of_birth'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Vitamin A (200,000 IU) Date</label>
                        <input type="date" class="form-control" name="vitamin_a_date" value="<?php echo htmlspecialchars($record['vitamin_a_date'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Albendazole (400mg) Date</label>
                        <input type="date" class="form-control" name="albendazole_date" value="<?php echo htmlspecialchars($record['albendazole_date'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
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
                    <a href="<?php echo BASE_URL; ?>admin-health-records-childcare" class="btn btn-glass">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Record</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function confirmDelete(id) {
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
            window.location.href = '<?php echo BASE_URL; ?>?action=delete-childcare-record&id=' + id;
        }
    });
}
</script>

<style>
.form-section-title { font-weight: 600; color: var(--text-primary); border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; }
.breadcrumb { background: transparent; padding: 0; margin: 0; font-size: 0.875rem; }
.breadcrumb-item a { color: var(--primary); text-decoration: none; }
</style>

<?php include __DIR__ . '/../../../includes/footer_admin.php'; ?>
