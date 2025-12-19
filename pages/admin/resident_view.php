<?php
// Resident (Patient) Details - Tabbed Profile
// Admin-only page. Header provides $pdo and auth.
include_once __DIR__ . '/../../includes/header_admin.php';

// Helper to calculate age
function calculate_age($dob) {
    if (empty($dob)) return null;
    try {
        $d1 = new DateTime($dob);
        $d2 = new DateTime();
        return $d1->diff($d2)->y;
    } catch (Throwable $e) {
        return null;
    }
}

$resident_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($resident_id <= 0) {
    echo '<div class="container py-4"><div class="alert alert-danger">Resident ID missing or invalid.</div></div>';
    include_once __DIR__ . '/../../includes/footer_admin.php';
    exit;
}

$resident = null;
$medical_history = [];
$medicine_log = [];
$vitals = [];

try {
    // Fetch resident/patient
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE patient_id = :id LIMIT 1");
    $stmt->execute([':id' => $resident_id]);
    $resident = $stmt->fetch(PDO::FETCH_ASSOC);

    // Medical history - health_visits
    $sqlVisits = "SELECT hv.*, b.full_name as bhw_name FROM health_visits hv LEFT JOIN bhw_users b ON hv.bhw_id = b.bhw_id WHERE hv.patient_id = :id ORDER BY hv.visit_date DESC";
    $stmt2 = $pdo->prepare($sqlVisits);
    $stmt2->execute([':id' => $resident_id]);
    $medical_history = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Medicine dispensing log
    $sqlMed = "SELECT mdl.*, mi.item_name, b.first_name as bhw_first, b.last_name as bhw_last FROM medicine_dispensing_log mdl LEFT JOIN medication_inventory mi ON mdl.item_id = mi.item_id LEFT JOIN bhw_users b ON mdl.bhw_id = b.bhw_id WHERE mdl.resident_id = :id ORDER BY mdl.dispensed_at DESC LIMIT 500";
    $stmt3 = $pdo->prepare($sqlMed);
    $stmt3->execute([':id' => $resident_id]);
    $medicine_log = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    // Load available medication inventory items for the 'Add Dispensing Record' form
    try {
        $stmtItems = $pdo->query("SELECT item_id, item_name FROM medication_inventory ORDER BY item_name ASC");
        $med_items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $med_items = [];
    }

    // Vitals history - try common table names
    $vitalsTables = ['vitals','vital_signs','patient_vitals'];
    foreach ($vitalsTables as $tbl) {
        try {
            $sqlV = "SELECT * FROM `" . $tbl . "` WHERE patient_id = :id ORDER BY recorded_at ASC LIMIT 1000";
            $st = $pdo->prepare($sqlV);
            $st->execute([':id' => $resident_id]);
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $vitals = $rows;
                break;
            }
        } catch (Throwable $e) {
            // table may not exist - continue to next
            continue;
        }
    }

} catch (Throwable $e) {
    error_log('resident_view error: ' . $e->getMessage());
}

if (!$resident) {
    echo '<div class="container py-4"><div class="alert alert-warning">Resident not found.</div></div>';
    include_once __DIR__ . '/../../includes/footer_admin.php';
    exit;
}

// prepare vitals series for charts (if available)
$vitals_dates = [];
$vitals_bp_sys = [];
$vitals_bp_dia = [];
$vitals_weight = [];
$vitals_temp = [];
foreach ($vitals as $r) {
    // try to detect timestamp field
    $ts = $r['recorded_at'] ?? ($r['created_at'] ?? ($r['date'] ?? null));
    $vitals_dates[] = $ts ? date('Y-m-d', strtotime($ts)) : '';
    $vitals_bp_sys[] = isset($r['bp_systolic']) ? (float)$r['bp_systolic'] : (isset($r['systolic']) ? (float)$r['systolic'] : null);
    $vitals_bp_dia[] = isset($r['bp_diastolic']) ? (float)$r['bp_diastolic'] : (isset($r['diastolic']) ? (float)$r['diastolic'] : null);
    $vitals_weight[] = isset($r['weight']) ? (float)$r['weight'] : null;
    $vitals_temp[] = isset($r['temperature']) ? (float)$r['temperature'] : (isset($r['temp']) ? (float)$r['temp'] : null);
}

?>
<style>
    :root{ --brand-teal: #B2A08F; }
    .card { border-radius:12px; }
    .tab-card { border-radius:12px; padding:0; overflow:hidden; }
    .nav-tabs .nav-link { border:0; color:#495057; }
    .nav-tabs .nav-link.active { background:var(--brand-teal); color:#fff; border-radius:8px; }
    .stat-bubble { width:56px; height:56px; border-radius:12px; display:inline-flex; align-items:center; justify-content:center; background:var(--brand-teal); color:#fff; }
    .table-sm td, .table-sm th { vertical-align:middle; }
    @media (max-width:575px){ .stat-bubble{width:48px;height:48px;} }
</style>

<div class="container-fluid py-4">
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Resident Profile: <?php echo htmlspecialchars($resident['first_name'] . ' ' . ($resident['last_name'] ?? '')); ?></h3>
        <div>
            <a href="<?php echo BASE_URL; ?>admin-patients" class="btn btn-outline-secondary">Back to Residents</a>
            <a href="<?php echo BASE_URL; ?>admin-patient-edit?id=<?php echo $resident['patient_id']; ?>" class="btn btn-primary">Edit</a>
        </div>
    </div>

    <ul class="nav nav-tabs" id="residentTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">Overview</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">Medical History</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="medicine-tab" data-bs-toggle="tab" data-bs-target="#medicine" type="button" role="tab">Medicine Monitoring</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="vitals-tab" data-bs-toggle="tab" data-bs-target="#vitals" type="button" role="tab">Vitals History</button>
      </li>
    </ul>
    <div class="tab-content border p-3 mt-0">
        <!-- Overview -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="row">
                <div class="col-md-3 text-center">
                    <?php
                        $photo = $resident['photo'] ?? '';
                        $photoUrl = $photo && file_exists(__DIR__ . '/../../assets/uploads/' . $photo) ? BASE_URL . 'assets/uploads/' . $photo : BASE_URL . 'assets/images/avatar_placeholder.png';
                    ?>
                    <img src="<?php echo $photoUrl; ?>" class="img-fluid rounded mb-2" alt="Resident photo">
                </div>
                <div class="col-md-9">
                    <table class="table table-borderless">
                        <tr><th>Name</th><td><?php echo htmlspecialchars(($resident['first_name'] ?? '') . ' ' . ($resident['last_name'] ?? '')); ?></td></tr>
                        <tr><th>Age</th><td><?php echo htmlspecialchars(calculate_age($resident['dob']) ?? 'N/A'); ?></td></tr>
                        <tr><th>Contact</th><td><?php echo htmlspecialchars($resident['contact'] ?? ''); ?></td></tr>
                        <tr><th>Address</th><td><?php echo htmlspecialchars($resident['address'] ?? ''); ?></td></tr>
                        <tr><th>Gender</th><td><?php echo htmlspecialchars($resident['gender'] ?? ''); ?></td></tr>
                        <tr><th>Patient ID</th><td><?php echo htmlspecialchars($resident['patient_id']); ?></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Medical History -->
        <div class="tab-pane fade" id="history" role="tabpanel">
            <div class="table-responsive mt-3">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr><th>Date</th><th>Visit Reason</th><th>Diagnosis</th><th>BHW</th><th>Notes</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($medical_history)): ?>
                            <tr><td colspan="5">No medical history found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($medical_history as $h): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($h['visit_date'] ?? $h['created_at'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($h['visit_reason'] ?? $h['reason'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($h['diagnosis'] ?? $h['impression'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($h['bhw_name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($h['notes'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Medicine Monitoring -->
        <div class="tab-pane fade" id="medicine" role="tabpanel">
            <div class="mb-3">
                <form method="post" action="<?php echo BASE_URL; ?>?action=medicine-dispense-save" class="row g-2 align-items-end">
                    <input type="hidden" name="patient_id" value="<?php echo (int)$resident_id; ?>" />
                    <div class="col-md-5">
                        <label class="form-label">Medicine</label>
                        <select name="item_id" class="form-select">
                            <option value="">-- Select medicine (optional) --</option>
                            <?php foreach ($med_items as $it): ?>
                                <option value="<?php echo (int)$it['item_id']; ?>"><?php echo htmlspecialchars($it['item_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" min="1" value="1" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="Optional notes" />
                    </div>
                    <div class="col-md-2 text-end">
                        <button class="btn btn-success">Record Dispensing</button>
                    </div>
                </form>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr><th>Date</th><th>Medicine</th><th>Quantity</th><th>BHW</th><th>Notes</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($medicine_log)): ?>
                            <tr><td colspan="5">No medicine dispensing records found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($medicine_log as $m): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($m['dispensed_at'] ?? $m['created_at'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($m['item_name'] ?? ($m['item_id'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars($m['quantity'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars((($m['bhw_first'] ?? '') . ' ' . ($m['bhw_last'] ?? ''))); ?></td>
                                    <td><?php echo htmlspecialchars($m['notes'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Vitals History -->
        <div class="tab-pane fade" id="vitals" role="tabpanel">
            <div class="row mt-3">
                <div class="col-12">
                    <?php if (empty($vitals)): ?>
                        <div class="alert alert-info">No vitals data available.</div>
                    <?php else: ?>
                        <canvas id="vitalsChart" style="height:360px;"></canvas>
                        <div class="table-responsive mt-3">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr><th>Date</th><th>BP (S/D)</th><th>Weight</th><th>Temp</th><th>Notes</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($vitals as $vv): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($vv['recorded_at'] ?? $vv['created_at'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars((isset($vv['bp_systolic'])?$vv['bp_systolic']:'').(isset($vv['bp_diastolic'])?'/'.$vv['bp_diastolic']:'')); ?></td>
                                            <td><?php echo htmlspecialchars($vv['weight'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($vv['temperature'] ?? ($vv['temp'] ?? '')); ?></td>
                                            <td><?php echo htmlspecialchars($vv['notes'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    (function(){
        const dates = <?php echo json_encode($vitals_dates); ?>;
        const bpSys = <?php echo json_encode($vitals_bp_sys); ?>;
        const bpDia = <?php echo json_encode($vitals_bp_dia); ?>;
        const weight = <?php echo json_encode($vitals_weight); ?>;
        const temp = <?php echo json_encode($vitals_temp); ?>;

        if (dates.length && document.getElementById('vitalsChart')) {
            const ctx = document.getElementById('vitalsChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        { label: 'BP Systolic', data: bpSys, borderColor: '#fd7e14', tension:0.3, fill:false },
                        { label: 'BP Diastolic', data: bpDia, borderColor: '#dc3545', tension:0.3, fill:false },
                        { label: 'Weight', data: weight, borderColor: '#B2A08F', tension:0.3, fill:false, yAxisID: 'y1' },
                        { label: 'Temp', data: temp, borderColor: '#0d6efd', tension:0.3, fill:false, yAxisID: 'y1' }
                    ]
                },
                options: {
                    scales: {
                        x: { display: true },
                        y: { display: true },
                        y1: { position: 'right', grid: { drawOnChartArea: false } }
                    },
                    interaction: { mode: 'index', intersect: false }
                }
            });
        }
    })();
</script>

<?php include_once __DIR__ . '/../../includes/footer_admin.php';
