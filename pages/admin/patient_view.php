<?php
// pages/admin/patient_view.php
// View a single patient's full information (read-only)
include_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_GET['id']) || trim($_GET['id']) === '') {
    $_SESSION['form_error'] = 'No patient ID provided.';
    header('Location: ' . BASE_URL . 'admin-patients');
    exit();
}

$patient_id = (int) $_GET['id'];

try {
    // Patient info
    $stmt = $pdo->prepare('SELECT * FROM patients WHERE patient_id = :id LIMIT 1');
    $stmt->execute([':id' => $patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        $_SESSION['form_error'] = 'Patient not found.';
        header('Location: ' . BASE_URL . 'admin-patients');
        exit();
    }

    // Health records
    $stmt2 = $pdo->prepare('SELECT * FROM patient_health_records WHERE patient_id = :id LIMIT 1');
    $stmt2->execute([':id' => $patient_id]);
    $health_records = $stmt2->fetch(PDO::FETCH_ASSOC);

    // Vitals history
    $stmt3 = $pdo->prepare('SELECT * FROM patient_vitals WHERE patient_id = :id ORDER BY recorded_at DESC');
    $stmt3->execute([':id' => $patient_id]);
    $vitals = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    // Visits history
    $stmt4 = $pdo->prepare('SELECT * FROM health_visits WHERE patient_id = :id ORDER BY visit_date DESC');
    $stmt4->execute([':id' => $patient_id]);
    $visits = $stmt4->fetchAll(PDO::FETCH_ASSOC);

    // Family composition
    $stmt5 = $pdo->prepare('SELECT * FROM family_composition WHERE head_patient_id = :id');
    $stmt5->execute([':id' => $patient_id]);
    $family = $stmt5->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    error_log('Patient view load error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'An error occurred while loading patient data.';
    header('Location: ' . BASE_URL . 'admin-patients');
    exit();
}
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><?php echo htmlspecialchars($patient['full_name'] ?? 'Patient'); ?></h1>
        <div class="d-flex gap-2">
            <a href="<?php echo BASE_URL; ?>admin-patient-form?id=<?php echo $patient_id; ?>" class="btn btn-primary btn-sm">Edit Patient</a>
            <a href="<?php echo BASE_URL; ?>?action=report-patient-record&id=<?php echo $patient_id; ?>" class="btn btn-info btn-sm" target="_blank">Download PDF</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Personal Information</div>
                <div class="card-body">
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($patient['address'] ?? ''); ?></p>
                    <p><strong>Birthdate:</strong> <?php echo htmlspecialchars($patient['birthdate'] ?? ''); ?></p>
                    <p><strong>Sex:</strong> <?php echo htmlspecialchars($patient['sex'] ?? ''); ?></p>
                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($patient['contact'] ?? ''); ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Health Records</div>
                <div class="card-body">
                    <?php if ($health_records): ?>
                        <dl class="row">
                            <dt class="col-sm-4">Medical History</dt>
                            <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($health_records['medical_history'] ?? '')); ?></dd>

                            <dt class="col-sm-4">Immunization Records</dt>
                            <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($health_records['immunization_records'] ?? '')); ?></dd>

                            <dt class="col-sm-4">Medication Records</dt>
                            <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($health_records['medication_records'] ?? '')); ?></dd>

                            <dt class="col-sm-4">Maternal & Child Health</dt>
                            <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($health_records['maternal_child_health'] ?? '')); ?></dd>

                            <dt class="col-sm-4">Chronic Disease Mgmt</dt>
                            <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($health_records['chronic_disease_mgmt'] ?? '')); ?></dd>

                            <dt class="col-sm-4">Referral Information</dt>
                            <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($health_records['referral_information'] ?? '')); ?></dd>
                        </dl>
                    <?php else: ?>
                        <div class="alert alert-info">No health records available for this patient.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Family Composition</div>
                <div class="card-body">
                    <?php if (empty($family)): ?>
                        <div class="alert alert-info">No family composition records found.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Member Name</th>
                                        <th>Relationship</th>
                                        <th>Health Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($family as $member): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($member['member_name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($member['relationship'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($member['health_status'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Vitals History</div>
                <div class="card-body">
                    <!-- Add Vital Sign Form -->
                    <form method="post" action="<?php echo BASE_URL; ?>?action=save-vital" class="mb-3">
                        <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Blood Pressure</label>
                                <input type="text" name="blood_pressure" class="form-control" placeholder="e.g., 120/80">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Heart Rate</label>
                                <input type="number" name="heart_rate" class="form-control" min="0">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Temperature</label>
                                <input type="number" name="temperature" class="form-control" step="0.1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Notes</label>
                                <input type="text" name="notes" class="form-control" placeholder="Optional notes">
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-success btn-sm">Save Vital</button>
                        </div>
                    </form>

                    <?php if (empty($vitals)): ?>
                        <div class="alert alert-info">No vitals recorded.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Recorded At</th>
                                        <th>Blood Pressure</th>
                                        <th>Heart Rate</th>
                                        <th>Temperature</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($vitals as $v): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($v['recorded_at'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($v['blood_pressure'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($v['heart_rate'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($v['temperature'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($v['notes'] ?? ''); ?></td>
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

    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Health Visit History</div>
                <div class="card-body">
                    <!-- Add Health Visit Form -->
                    <form method="post" action="<?php echo BASE_URL; ?>?action=save-visit" class="mb-3">
                        <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Visit Date</label>
                                <input type="date" name="visit_date" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Visit Type</label>
                                <input type="text" name="visit_type" class="form-control" placeholder="e.g., Home visit">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Remarks</label>
                                <input type="text" name="remarks" class="form-control" placeholder="Optional remarks">
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-success btn-sm">Save Visit</button>
                        </div>
                    </form>

                    <?php if (empty($visits)): ?>
                        <div class="alert alert-info">No visits recorded.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Visit Date</th>
                                        <th>Type</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($visits as $visit): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($visit['visit_date'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($visit['visit_type'] ?? ''); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars($visit['remarks'] ?? '')); ?></td>
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

    <?php
    // Flash messages
    if (isset($_SESSION['form_success'])) {
        $msg = json_encode($_SESSION['form_success']);
        echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'success', title: 'Success', text: $msg}); } });</script>";
        unset($_SESSION['form_success']);
    }
    if (isset($_SESSION['form_error'])) {
        $emsg = json_encode($_SESSION['form_error']);
        echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'error', title: 'Error', text: $emsg}); } });</script>";
        unset($_SESSION['form_error']);
    }
    ?>

</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
<?php
// Admin: View single patient (placeholder)
require_once __DIR__ . '/../../includes/header_admin.php';
?>

<?php require_once __DIR__ . '/../../includes/footer_admin.php';
