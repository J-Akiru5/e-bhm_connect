<?php
// pages/portal/portal_dashboard.php
// Patient portal dashboard (read-only)
// (index.php handles security/session)
include_once __DIR__ . '/../../includes/header_portal.php';

// Get all data for the logged-in patient
$patient_id = isset($_SESSION['patient_id']) ? $_SESSION['patient_id'] : null;

// Fetch patient info and health records
if ($patient_id) {
    $stmt1 = $pdo->prepare("SELECT * FROM patients WHERE patient_id = ?");
    $stmt1->execute([$patient_id]);
    $patient = $stmt1->fetch(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare("SELECT * FROM patient_health_records WHERE patient_id = ?");
    $stmt2->execute([$patient_id]);
    $health_records = $stmt2->fetch(PDO::FETCH_ASSOC);
    
    // Query 3: Vitals History
    $stmt3 = $pdo->prepare("SELECT * FROM patient_vitals WHERE patient_id = ? ORDER BY recorded_at DESC");
    $stmt3->execute([$patient_id]);
    $vitals_history = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    // Query 4: Visit History
    $stmt4 = $pdo->prepare("SELECT * FROM health_visits WHERE patient_id = ? ORDER BY visit_date DESC");
    $stmt4->execute([$patient_id]);
    $visit_history = $stmt4->fetchAll(PDO::FETCH_ASSOC);
} else {
    $patient = null;
    $health_records = null;
}

?>

<div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="h3 mb-1" style="color: var(--primary);">Welcome, <?php echo htmlspecialchars($_SESSION['patient_full_name'] ?? ''); ?>!</h1>
        <p class="text-muted mb-0">View your health records and information</p>
    </div>
    <a href="<?php echo BASE_URL; ?>?action=report-my-record" class="btn btn-primary" target="_blank">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        Download My Records
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-6">

        <div class="portal-card mb-4">
            <div class="portal-card-header d-flex align-items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                My Personal Information
            </div>
            <div class="portal-card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-muted small mb-1">Address</div>
                        <div class="fw-medium"><?php echo htmlspecialchars($patient['address'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small mb-1">Birthdate</div>
                        <div class="fw-medium"><?php echo $patient['birthdate'] ? date('M j, Y', strtotime($patient['birthdate'])) : 'N/A'; ?></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small mb-1">Sex</div>
                        <div class="fw-medium"><?php echo htmlspecialchars($patient['sex'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small mb-1">Contact</div>
                        <div class="fw-medium"><?php echo htmlspecialchars($patient['contact'] ?? 'N/A'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="portal-card mb-4">
            <div class="portal-card-header d-flex align-items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
                My Vitals History
            </div>
            <div class="portal-card-body">
                <?php if (empty($vitals_history)): ?>
                    <div class="text-center py-5">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--gray-400)" stroke-width="1.5" class="mb-3">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <p class="text-muted mb-0">No vitals recorded yet. Your vitals will appear here once they are recorded by a BHW.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date Recorded</th>
                                    <th>BP</th>
                                    <th>Heart Rate</th>
                                    <th>Temp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vitals_history as $vital): ?>
                                    <tr>
                                        <td><?php echo date('M j, Y, g:i a', strtotime($vital['recorded_at'])); ?></td>
                                        <td><span class="badge badge-primary"><?php echo htmlspecialchars($vital['blood_pressure']); ?></span></td>
                                        <td><?php echo htmlspecialchars($vital['heart_rate']); ?> bpm</td>
                                        <td><?php echo htmlspecialchars($vital['temperature']); ?>°C</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
    
    <div class="col-lg-6">

        <div class="portal-card mb-4">
            <div class="portal-card-header d-flex align-items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                </svg>
                My Health Records
            </div>
            <div class="portal-card-body">
                <div class="mb-4">
                    <div class="text-muted small mb-2 fw-semibold">Medical History</div>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($health_records['medical_history'] ?? 'No medical history recorded.')); ?></p>
                </div>
                <div class="mb-4">
                    <div class="text-muted small mb-2 fw-semibold">Immunization Records</div>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($health_records['immunization_records'] ?? 'No immunization records.')); ?></p>
                </div>
                <div>
                    <div class="text-muted small mb-2 fw-semibold">Medication Records</div>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($health_records['medication_records'] ?? 'No medication records.')); ?></p>
                </div>
            </div>
        </div>

        <div class="portal-card mb-4">
            <div class="portal-card-header d-flex align-items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                My Health Visit History
            </div>
            <div class="portal-card-body">
                <?php if (empty($visit_history)): ?>
                    <div class="text-center py-5">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--gray-400)" stroke-width="1.5" class="mb-3">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <p class="text-muted mb-0">No visits recorded yet. Your visit history will appear here once logged by a BHW.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Visit Date</th>
                                    <th>Type</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($visit_history as $visit): ?>
                                    <tr>
                                        <td><?php echo date('M j, Y', strtotime($visit['visit_date'])); ?></td>
                                        <td><span class="badge badge-success"><?php echo htmlspecialchars($visit['visit_type']); ?></span></td>
                                        <td><?php echo htmlspecialchars($visit['remarks']); ?></td>
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
include_once __DIR__ . '/../../includes/footer_portal.php';
?>
