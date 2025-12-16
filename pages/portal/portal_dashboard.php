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

<h1 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['patient_full_name'] ?? ''); ?>!</h1>

<a href="<?php echo BASE_URL; ?>?action=report-my-record" class="btn btn-teal mb-3" target="_blank">Download My Records (PDF)</a>

<div class="row g-4">
    <div class="col-lg-6">

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                My Personal Information
            </div>
            <div class="card-body">
                <p><strong>Address:</strong> <?php echo htmlspecialchars($patient['address'] ?? 'N/A'); ?></p>
                <p><strong>Birthdate:</strong> <?php echo htmlspecialchars($patient['birthdate'] ?? 'N/A'); ?></p>
                <p><strong>Sex:</strong> <?php echo htmlspecialchars($patient['sex'] ?? 'N/A'); ?></p>
                <p><strong>Contact:</strong> <?php echo htmlspecialchars($patient['contact'] ?? 'N/A'); ?></p>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                My Vitals History
            </div>
            <div class="card-body">
                <?php if (empty($vitals_history)): ?>
                    <div class="alert alert-light border text-center p-4" style="background-color: #f8f9fa;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#6c757d" class="mb-2" viewBox="0 0 16 16">
                          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                          <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                        <p class="mb-0 text-muted">No vitals recorded yet. Your vitals will appear here once they are recorded by a BHW.</p>
                    </div>
                <?php else: ?>
                    <table class="table table-striped">
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
                                    <td><?php echo htmlspecialchars($vital['blood_pressure']); ?></td>
                                    <td><?php echo htmlspecialchars($vital['heart_rate']); ?></td>
                                    <td><?php echo htmlspecialchars($vital['temperature']); ?>°C</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

    </div> <div class="col-lg-6">

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                My Health Records
            </div>
            <div class="card-body">
                <strong>Medical History:</strong>
                <p><?php echo nl2br(htmlspecialchars($health_records['medical_history'] ?? 'N/A')); ?></p>

                <strong>Immunization Records:</strong>
                <p><?php echo nl2br(htmlspecialchars($health_records['immunization_records'] ?? 'N/A')); ?></p>

                <strong>Medication Records:</strong>
                <p><?php echo nl2br(htmlspecialchars($health_records['medication_records'] ?? 'N/A')); ?></p>

                </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                My Health Visit History
            </div>
            <div class="card-body">
                <?php if (empty($visit_history)): ?>
                    <div class="alert alert-light border text-center p-4" style="background-color: #f8f9fa;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#6c757d" class="mb-2" viewBox="0 0 16 16">
                          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                          <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                        <p class="mb-0 text-muted">No visits recorded yet. Your visit history will appear here once logged by a BHW.</p>
                    </div>
                <?php else: ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Visit Date</th>
                                <th>Type of Visit</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visit_history as $visit): ?>
                                <tr>
                                    <td><?php echo date('M j, Y', strtotime($visit['visit_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($visit['visit_type']); ?></td>
                                    <td><?php echo htmlspecialchars($visit['remarks']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

    </div> </div> <?php
// Include the footer
include_once __DIR__ . '/../../includes/footer_portal.php';
?>

<?php
include_once __DIR__ . '/../../includes/footer_portal.php';
?>
