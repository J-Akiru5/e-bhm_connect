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
} else {
    $patient = null;
    $health_records = null;
}

?>

<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['patient_full_name'] ?? ''); ?>!</h1>
    </div>

    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-success text-white">My Information</div>
            <div class="card-body">
                <?php if ($patient): ?>
                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($patient['full_name']); ?></p>
                    <p><strong>Birthdate:</strong> <?php echo htmlspecialchars($patient['birthdate']); ?></p>
                    <p><strong>Sex:</strong> <?php echo htmlspecialchars($patient['sex']); ?></p>
                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($patient['contact']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($patient['address']); ?></p>
                <?php else: ?>
                    <p class="text-muted">No patient information available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-success text-white">My Health History</div>
            <div class="card-body">
                <?php if ($health_records): ?>
                    <p><strong>Medical History:</strong></p>
                    <div class="border p-2 mb-2" style="white-space:pre-wrap"><?php echo htmlspecialchars($health_records['medical_history']); ?></div>
                    <p><strong>Immunization Records:</strong></p>
                    <div class="border p-2 mb-2" style="white-space:pre-wrap"><?php echo htmlspecialchars($health_records['immunization_records']); ?></div>
                    <p><strong>Medication Records:</strong></p>
                    <div class="border p-2 mb-2" style="white-space:pre-wrap"><?php echo htmlspecialchars($health_records['medication_records']); ?></div>
                <?php else: ?>
                    <p class="text-muted">No health records found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-12 mt-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted">Vitals and Visits will appear here in the next step.</p>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../../includes/footer_portal.php';
?>
