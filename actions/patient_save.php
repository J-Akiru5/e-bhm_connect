<?php
// actions/patient_save.php
// Handle insert/update for patient and patient_health_records

// Auth and DB are bootstrapped by the central router (index.php)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-patients');
    exit();
}

// Collect POST data
$patient_id = isset($_POST['patient_id']) && $_POST['patient_id'] !== '' ? (int) $_POST['patient_id'] : null;
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';
$birthdate = isset($_POST['birthdate']) ? trim($_POST['birthdate']) : null;
$sex = isset($_POST['sex']) ? trim($_POST['sex']) : '';
$contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
$family_composition_text = isset($_POST['family_composition_text']) ? trim($_POST['family_composition_text']) : '';

$medical_history = isset($_POST['medical_history']) ? trim($_POST['medical_history']) : '';
$immunization_records = isset($_POST['immunization_records']) ? trim($_POST['immunization_records']) : '';
$medication_records = isset($_POST['medication_records']) ? trim($_POST['medication_records']) : '';
$maternal_child_health = isset($_POST['maternal_child_health']) ? trim($_POST['maternal_child_health']) : '';
$chronic_disease_mgmt = isset($_POST['chronic_disease_mgmt']) ? trim($_POST['chronic_disease_mgmt']) : '';
$referral_information = isset($_POST['referral_information']) ? trim($_POST['referral_information']) : '';

try {
    $pdo->beginTransaction();

    if (empty($patient_id)) {
        // INSERT patient
        $insertPatient = $pdo->prepare('INSERT INTO patients (full_name, address, birthdate, sex, contact, family_composition_text) VALUES (:full_name, :address, :birthdate, :sex, :contact, :family_composition_text)');
        $insertPatient->execute([
            ':full_name' => $full_name,
            ':address' => $address,
            ':birthdate' => $birthdate,
            ':sex' => $sex,
            ':contact' => $contact,
            ':family_composition_text' => $family_composition_text
        ]);

        $newId = $pdo->lastInsertId();

        $insertHealth = $pdo->prepare('INSERT INTO patient_health_records (patient_id, medical_history, immunization_records, medication_records, maternal_child_health, chronic_disease_mgmt, referral_information) VALUES (:patient_id, :medical_history, :immunization_records, :medication_records, :maternal_child_health, :chronic_disease_mgmt, :referral_information)');
        $insertHealth->execute([
            ':patient_id' => $newId,
            ':medical_history' => $medical_history,
            ':immunization_records' => $immunization_records,
            ':medication_records' => $medication_records,
            ':maternal_child_health' => $maternal_child_health,
            ':chronic_disease_mgmt' => $chronic_disease_mgmt,
            ':referral_information' => $referral_information
        ]);

        $pdo->commit();
        $_SESSION['form_success'] = 'Patient added successfully!';
    } else {
        // UPDATE existing
        $updatePatient = $pdo->prepare('UPDATE patients SET full_name = :full_name, address = :address, birthdate = :birthdate, sex = :sex, contact = :contact, family_composition_text = :family_composition_text WHERE patient_id = :patient_id');
        $updatePatient->execute([
            ':full_name' => $full_name,
            ':address' => $address,
            ':birthdate' => $birthdate,
            ':sex' => $sex,
            ':contact' => $contact,
            ':family_composition_text' => $family_composition_text,
            ':patient_id' => $patient_id
        ]);

        // Check if health record exists
        $stmt = $pdo->prepare('SELECT id FROM patient_health_records WHERE patient_id = :patient_id LIMIT 1');
        $stmt->execute([':patient_id' => $patient_id]);
        $exists = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($exists) {
            $updateHealth = $pdo->prepare('UPDATE patient_health_records SET medical_history = :medical_history, immunization_records = :immunization_records, medication_records = :medication_records, maternal_child_health = :maternal_child_health, chronic_disease_mgmt = :chronic_disease_mgmt, referral_information = :referral_information WHERE patient_id = :patient_id');
            $updateHealth->execute([
                ':medical_history' => $medical_history,
                ':immunization_records' => $immunization_records,
                ':medication_records' => $medication_records,
                ':maternal_child_health' => $maternal_child_health,
                ':chronic_disease_mgmt' => $chronic_disease_mgmt,
                ':referral_information' => $referral_information,
                ':patient_id' => $patient_id
            ]);
        } else {
            $insertHealth = $pdo->prepare('INSERT INTO patient_health_records (patient_id, medical_history, immunization_records, medication_records, maternal_child_health, chronic_disease_mgmt, referral_information) VALUES (:patient_id, :medical_history, :immunization_records, :medication_records, :maternal_child_health, :chronic_disease_mgmt, :referral_information)');
            $insertHealth->execute([
                ':patient_id' => $patient_id,
                ':medical_history' => $medical_history,
                ':immunization_records' => $immunization_records,
                ':medication_records' => $medication_records,
                ':maternal_child_health' => $maternal_child_health,
                ':chronic_disease_mgmt' => $chronic_disease_mgmt,
                ':referral_information' => $referral_information
            ]);
        }

        $pdo->commit();
        $_SESSION['form_success'] = 'Patient updated successfully!';
    }

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Patient save error: ' . $e->getMessage());
    // Provide a more helpful error message in development environment
    if (defined('APP_ENV') && APP_ENV === 'development') {
        $_SESSION['form_error'] = 'An error occurred: ' . $e->getMessage();
    } else {
        $_SESSION['form_error'] = 'An error occurred.';
    }
}

header('Location: ' . BASE_URL . 'admin-patients');
exit();

