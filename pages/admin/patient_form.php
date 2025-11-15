<?php
// Smart form for adding/editing patients
require_once __DIR__ . '/../../includes/auth_bhw.php';
include_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';

// Initialize variables
$patient_id = null;
$full_name = '';
$address = '';
$birthdate = '';
$sex = '';
$contact = '';
$family_composition_text = '';

$medical_history = '';
$immunization_records = '';
$medication_records = '';
$maternal_child_health = '';
$chronic_disease_mgmt = '';
$referral_information = '';

$form_title = 'Add New Patient';

if (isset($_GET['id'])) {
    $patient_id = (int) $_GET['id'];
    $form_title = 'Edit Patient Record';

    try {
        $stmt = $pdo->prepare('SELECT * FROM patients WHERE patient_id = :id LIMIT 1');
        $stmt->execute([':id' => $patient_id]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($patient) {
            $full_name = $patient['full_name'] ?? '';
            $address = $patient['address'] ?? '';
            $birthdate = $patient['birthdate'] ?? '';
            $sex = $patient['sex'] ?? '';
            $contact = $patient['contact'] ?? '';
            $family_composition_text = $patient['family_composition_text'] ?? '';
        }

        $stmt2 = $pdo->prepare('SELECT * FROM patient_health_records WHERE patient_id = :id LIMIT 1');
        $stmt2->execute([':id' => $patient_id]);
        $health = $stmt2->fetch(PDO::FETCH_ASSOC);
        if ($health) {
            $medical_history = $health['medical_history'] ?? '';
            $immunization_records = $health['immunization_records'] ?? '';
            $medication_records = $health['medication_records'] ?? '';
            $maternal_child_health = $health['maternal_child_health'] ?? '';
            $chronic_disease_mgmt = $health['chronic_disease_mgmt'] ?? '';
            $referral_information = $health['referral_information'] ?? '';
        }
    } catch (Throwable $e) {
        error_log('Patient form load error: ' . $e->getMessage());
    }
}
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><?php echo htmlspecialchars($form_title); ?></h1>
        <a href="/e-bmw_connect/admin-patients" class="btn btn-secondary">Back to Patients</a>
    </div>

    <form method="post" action="/e-bmw_connect/actions/patient_save.php">
        <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($patient_id); ?>">

        <div class="card mb-3">
            <div class="card-header">Personal Information</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($full_name); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($address); ?>">
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Birthdate</label>
                        <input type="date" name="birthdate" class="form-control" value="<?php echo htmlspecialchars($birthdate); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Sex</label>
                        <select name="sex" class="form-select">
                            <option value="">Select</option>
                            <option value="Male" <?php echo $sex === 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo $sex === 'Female' ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact</label>
                        <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars($contact); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Family Composition</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Family Composition</label>
                    <textarea name="family_composition_text" class="form-control" rows="3"><?php echo htmlspecialchars($family_composition_text); ?></textarea>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Health Records</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Medical History</label>
                    <textarea name="medical_history" class="form-control" rows="3"><?php echo htmlspecialchars($medical_history); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Immunization Records</label>
                    <textarea name="immunization_records" class="form-control" rows="3"><?php echo htmlspecialchars($immunization_records); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Medication Records</label>
                    <textarea name="medication_records" class="form-control" rows="3"><?php echo htmlspecialchars($medication_records); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Maternal & Child Health</label>
                    <textarea name="maternal_child_health" class="form-control" rows="3"><?php echo htmlspecialchars($maternal_child_health); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Chronic Disease Management</label>
                    <textarea name="chronic_disease_mgmt" class="form-control" rows="3"><?php echo htmlspecialchars($chronic_disease_mgmt); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Referral Information</label>
                    <textarea name="referral_information" class="form-control" rows="3"><?php echo htmlspecialchars($referral_information); ?></textarea>
                </div>
            </div>
        </div>

        <div class="d-grid mb-5">
            <button type="submit" class="btn btn-primary">Save Patient</button>
        </div>
    </form>
</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php';
