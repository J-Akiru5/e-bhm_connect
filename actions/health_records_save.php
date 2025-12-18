<?php
/**
 * Health Records - Unified Save Action Handler
 * E-BHM Connect
 * 
 * Handles all health record CRUD save operations.
 * Includes CSRF protection for all operations.
 */

require_once __DIR__ . '/../includes/auth_bhw.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/security_helper.php';
require_once __DIR__ . '/../includes/auth_helpers.php';

// Validate CSRF token for all health record saves
require_csrf();

// Get the action type from the URL
$action = $_GET['action'] ?? '';

// Determine which record type to save
switch ($action) {
    case 'save-pregnancy-tracking':
        savePregnancyTracking($pdo);
        break;
    case 'save-childcare-record':
        saveChildCareRecord($pdo);
        break;
    case 'save-natality-record':
        saveNatalityRecord($pdo);
        break;
    case 'save-mortality-record':
        saveMortalityRecord($pdo);
        break;
    case 'save-chronic-disease':
        saveChronicDisease($pdo);
        break;
    case 'save-ntp-client':
        saveNtpClient($pdo);
        break;
    case 'save-wra-tracking':
        saveWraTracking($pdo);
        break;
    default:
        $_SESSION['error'] = 'Invalid action.';
        header('Location: ' . BASE_URL . 'admin-health-records');
        exit;
}

/**
 * Save/Update Pregnancy Tracking Record
 */
function savePregnancyTracking($pdo) {
    $id = isset($_POST['pregnancy_id']) && $_POST['pregnancy_id'] !== '' ? (int)$_POST['pregnancy_id'] : null;
    
    $data = [
        'mother_name' => trim($_POST['mother_name'] ?? ''),
        'date_of_birth' => $_POST['date_of_birth'] ?: null,
        'address' => trim($_POST['address'] ?? ''),
        'lmp' => $_POST['lmp'] ?: null,
        'edc' => $_POST['edc'] ?: null,
        'gravida' => $_POST['gravida'] ?: null,
        'para' => $_POST['para'] ?: null,
        'is_nhts' => isset($_POST['is_nhts']) ? 1 : 0,
        'philhealth_number' => trim($_POST['philhealth_number'] ?? ''),
        'delivery_date' => $_POST['delivery_date'] ?: null,
        'delivery_outcome' => trim($_POST['delivery_outcome'] ?? ''),
        'patient_id' => $_POST['patient_id'] ?: null,
        'bhw_id' => $_POST['bhw_id'] ?: $_SESSION['bhw_id']
    ];
    
    try {
        if ($id) {
            $sql = "UPDATE pregnancy_tracking SET 
                    mother_name = ?, date_of_birth = ?, address = ?, lmp = ?, edc = ?,
                    gravida = ?, para = ?, is_nhts = ?, philhealth_number = ?,
                    delivery_date = ?, delivery_outcome = ?, patient_id = ?, bhw_id = ?, updated_at = NOW()
                    WHERE pregnancy_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['mother_name'], $data['date_of_birth'], $data['address'], $data['lmp'], $data['edc'],
                $data['gravida'], $data['para'], $data['is_nhts'], $data['philhealth_number'],
                $data['delivery_date'], $data['delivery_outcome'], $data['patient_id'], $data['bhw_id'], $id
            ]);
            log_audit('update_health_record', 'pregnancy', $id, ['type' => 'pregnancy', 'name' => $data['mother_name']]);
            $_SESSION['success'] = 'Pregnancy record updated successfully.';
        } else {
            $sql = "INSERT INTO pregnancy_tracking 
                    (mother_name, date_of_birth, address, lmp, edc, gravida, para, is_nhts, philhealth_number,
                     delivery_date, delivery_outcome, patient_id, bhw_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['mother_name'], $data['date_of_birth'], $data['address'], $data['lmp'], $data['edc'],
                $data['gravida'], $data['para'], $data['is_nhts'], $data['philhealth_number'],
                $data['delivery_date'], $data['delivery_outcome'], $data['patient_id'], $data['bhw_id']
            ]);
            $newId = $pdo->lastInsertId();
            log_audit('create_health_record', 'pregnancy', (int)$newId, ['type' => 'pregnancy', 'name' => $data['mother_name']]);
            $_SESSION['success'] = 'Pregnancy record added successfully.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }
    
    header('Location: ' . BASE_URL . 'admin-health-records-pregnancy');
    exit;
}

/**
 * Save/Update Child Care Record
 */
function saveChildCareRecord($pdo) {
    $id = isset($_POST['child_care_id']) && $_POST['child_care_id'] !== '' ? (int)$_POST['child_care_id'] : null;
    
    $data = [
        'child_name' => trim($_POST['child_name'] ?? ''),
        'date_of_birth' => $_POST['date_of_birth'] ?: null,
        'age_months' => $_POST['age_months'] ?: null,
        'sex' => $_POST['sex'] ?: null,
        'vitamin_a_date' => $_POST['vitamin_a_date'] ?: null,
        'albendazole_date' => $_POST['albendazole_date'] ?: null,
        'patient_id' => $_POST['patient_id'] ?: null,
        'bhw_id' => $_POST['bhw_id'] ?: $_SESSION['bhw_id']
    ];
    
    try {
        if ($id) {
            $sql = "UPDATE child_care_records SET 
                    child_name = ?, date_of_birth = ?, age_months = ?, sex = ?,
                    vitamin_a_date = ?, albendazole_date = ?, patient_id = ?, bhw_id = ?, updated_at = NOW()
                    WHERE child_care_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['child_name'], $data['date_of_birth'], $data['age_months'], $data['sex'],
                $data['vitamin_a_date'], $data['albendazole_date'], $data['patient_id'], $data['bhw_id'], $id
            ]);
            $_SESSION['success'] = 'Child care record updated successfully.';
        } else {
            $sql = "INSERT INTO child_care_records 
                    (child_name, date_of_birth, age_months, sex, vitamin_a_date, albendazole_date, patient_id, bhw_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['child_name'], $data['date_of_birth'], $data['age_months'], $data['sex'],
                $data['vitamin_a_date'], $data['albendazole_date'], $data['patient_id'], $data['bhw_id']
            ]);
            $_SESSION['success'] = 'Child care record added successfully.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }
    
    header('Location: ' . BASE_URL . 'admin-health-records-childcare');
    exit;
}

/**
 * Save/Update Natality (Birth) Record
 */
function saveNatalityRecord($pdo) {
    $id = isset($_POST['natality_id']) && $_POST['natality_id'] !== '' ? (int)$_POST['natality_id'] : null;
    
    $data = [
        'baby_name' => trim($_POST['baby_name'] ?? ''),
        'date_of_birth' => $_POST['date_of_birth'] ?: null,
        'time_of_birth' => $_POST['time_of_birth'] ?: null,
        'sex' => $_POST['sex'] ?: null,
        'birth_weight_kg' => $_POST['birth_weight_kg'] ?: null,
        'birth_length_cm' => $_POST['birth_length_cm'] ?: null,
        'place_of_delivery' => trim($_POST['place_of_delivery'] ?? ''),
        'type_of_delivery' => trim($_POST['type_of_delivery'] ?? ''),
        'mother_name' => trim($_POST['mother_name'] ?? ''),
        'mother_age' => $_POST['mother_age'] ?: null,
        'father_name' => trim($_POST['father_name'] ?? ''),
        'attendant' => trim($_POST['attendant'] ?? ''),
        'patient_id' => $_POST['patient_id'] ?: null,
        'bhw_id' => $_POST['bhw_id'] ?: $_SESSION['bhw_id']
    ];
    
    try {
        if ($id) {
            $sql = "UPDATE natality_records SET 
                    baby_name = ?, date_of_birth = ?, time_of_birth = ?, sex = ?,
                    birth_weight_kg = ?, birth_length_cm = ?, place_of_delivery = ?,
                    type_of_delivery = ?, mother_name = ?, mother_age = ?, father_name = ?,
                    attendant = ?, patient_id = ?, bhw_id = ?, updated_at = NOW()
                    WHERE natality_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['baby_name'], $data['date_of_birth'], $data['time_of_birth'], $data['sex'],
                $data['birth_weight_kg'], $data['birth_length_cm'], $data['place_of_delivery'],
                $data['type_of_delivery'], $data['mother_name'], $data['mother_age'], $data['father_name'],
                $data['attendant'], $data['patient_id'], $data['bhw_id'], $id
            ]);
            $_SESSION['success'] = 'Birth record updated successfully.';
        } else {
            $sql = "INSERT INTO natality_records 
                    (baby_name, date_of_birth, time_of_birth, sex, birth_weight_kg, birth_length_cm,
                     place_of_delivery, type_of_delivery, mother_name, mother_age, father_name, attendant, patient_id, bhw_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['baby_name'], $data['date_of_birth'], $data['time_of_birth'], $data['sex'],
                $data['birth_weight_kg'], $data['birth_length_cm'], $data['place_of_delivery'],
                $data['type_of_delivery'], $data['mother_name'], $data['mother_age'], $data['father_name'],
                $data['attendant'], $data['patient_id'], $data['bhw_id']
            ]);
            $_SESSION['success'] = 'Birth record added successfully.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }
    
    header('Location: ' . BASE_URL . 'admin-health-records-natality');
    exit;
}

/**
 * Save/Update Mortality (Death) Record
 */
function saveMortalityRecord($pdo) {
    $id = isset($_POST['mortality_id']) && $_POST['mortality_id'] !== '' ? (int)$_POST['mortality_id'] : null;
    
    $data = [
        'deceased_name' => trim($_POST['deceased_name'] ?? ''),
        'date_of_birth' => $_POST['date_of_birth'] ?: null,
        'date_of_death' => $_POST['date_of_death'] ?: null,
        'age_at_death' => $_POST['age_at_death'] ?: null,
        'sex' => $_POST['sex'] ?: null,
        'cause_of_death' => trim($_POST['cause_of_death'] ?? ''),
        'place_of_death' => trim($_POST['place_of_death'] ?? ''),
        'is_maternal_death' => isset($_POST['is_maternal_death']) ? 1 : 0,
        'remarks' => trim($_POST['remarks'] ?? ''),
        'patient_id' => $_POST['patient_id'] ?: null,
        'bhw_id' => $_POST['bhw_id'] ?: $_SESSION['bhw_id']
    ];
    
    try {
        if ($id) {
            $sql = "UPDATE mortality_records SET 
                    deceased_name = ?, date_of_birth = ?, date_of_death = ?, age_at_death = ?,
                    sex = ?, cause_of_death = ?, place_of_death = ?, is_maternal_death = ?,
                    remarks = ?, patient_id = ?, bhw_id = ?, updated_at = NOW()
                    WHERE mortality_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['deceased_name'], $data['date_of_birth'], $data['date_of_death'], $data['age_at_death'],
                $data['sex'], $data['cause_of_death'], $data['place_of_death'], $data['is_maternal_death'],
                $data['remarks'], $data['patient_id'], $data['bhw_id'], $id
            ]);
            $_SESSION['success'] = 'Mortality record updated successfully.';
        } else {
            $sql = "INSERT INTO mortality_records 
                    (deceased_name, date_of_birth, date_of_death, age_at_death, sex, cause_of_death,
                     place_of_death, is_maternal_death, remarks, patient_id, bhw_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['deceased_name'], $data['date_of_birth'], $data['date_of_death'], $data['age_at_death'],
                $data['sex'], $data['cause_of_death'], $data['place_of_death'], $data['is_maternal_death'],
                $data['remarks'], $data['patient_id'], $data['bhw_id']
            ]);
            $_SESSION['success'] = 'Mortality record added successfully.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }
    
    header('Location: ' . BASE_URL . 'admin-health-records-mortality');
    exit;
}

/**
 * Save/Update Chronic Disease Masterlist Record
 */
function saveChronicDisease($pdo) {
    $id = isset($_POST['chronic_id']) && $_POST['chronic_id'] !== '' ? (int)$_POST['chronic_id'] : null;
    
    // Handle medications as JSON array
    $medications = [];
    if (isset($_POST['medications']) && is_array($_POST['medications'])) {
        $medications = $_POST['medications'];
    }
    
    $data = [
        'patient_name' => trim($_POST['patient_name'] ?? ''),
        'date_of_birth' => $_POST['date_of_birth'] ?: null,
        'sex' => $_POST['sex'] ?: null,
        'address' => trim($_POST['address'] ?? ''),
        'is_hypertensive' => isset($_POST['is_hypertensive']) ? 1 : 0,
        'is_diabetic' => isset($_POST['is_diabetic']) ? 1 : 0,
        'medications' => json_encode($medications),
        'blood_sugar_level' => $_POST['blood_sugar_level'] ?: null,
        'blood_sugar_test_type' => trim($_POST['blood_sugar_test_type'] ?? ''),
        'last_checkup_date' => $_POST['last_checkup_date'] ?: null,
        'remarks' => trim($_POST['remarks'] ?? ''),
        'patient_id' => $_POST['patient_id'] ?: null,
        'bhw_id' => $_POST['bhw_id'] ?: $_SESSION['bhw_id']
    ];
    
    try {
        if ($id) {
            $sql = "UPDATE chronic_disease_masterlist SET 
                    patient_name = ?, date_of_birth = ?, sex = ?, address = ?,
                    is_hypertensive = ?, is_diabetic = ?, medications = ?, blood_sugar_level = ?,
                    blood_sugar_test_type = ?, last_checkup_date = ?, remarks = ?, patient_id = ?, bhw_id = ?, updated_at = NOW()
                    WHERE chronic_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['patient_name'], $data['date_of_birth'], $data['sex'], $data['address'],
                $data['is_hypertensive'], $data['is_diabetic'], $data['medications'], $data['blood_sugar_level'],
                $data['blood_sugar_test_type'], $data['last_checkup_date'], $data['remarks'], $data['patient_id'], $data['bhw_id'], $id
            ]);
            $_SESSION['success'] = 'Chronic disease record updated successfully.';
        } else {
            $sql = "INSERT INTO chronic_disease_masterlist 
                    (patient_name, date_of_birth, sex, address, is_hypertensive, is_diabetic, medications,
                     blood_sugar_level, blood_sugar_test_type, last_checkup_date, remarks, patient_id, bhw_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['patient_name'], $data['date_of_birth'], $data['sex'], $data['address'],
                $data['is_hypertensive'], $data['is_diabetic'], $data['medications'], $data['blood_sugar_level'],
                $data['blood_sugar_test_type'], $data['last_checkup_date'], $data['remarks'], $data['patient_id'], $data['bhw_id']
            ]);
            $_SESSION['success'] = 'Chronic disease record added successfully.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }
    
    header('Location: ' . BASE_URL . 'admin-health-records-chronic');
    exit;
}

/**
 * Save/Update NTP Client Monitoring Record
 */
function saveNtpClient($pdo) {
    $id = isset($_POST['ntp_id']) && $_POST['ntp_id'] !== '' ? (int)$_POST['ntp_id'] : null;
    
    // Handle monthly weights as JSON
    $monthlyWeights = [];
    for ($i = 1; $i <= 6; $i++) {
        $key = "month_{$i}_weight";
        $monthlyWeights["month_$i"] = $_POST[$key] ?? null;
    }
    
    $data = [
        'client_name' => trim($_POST['client_name'] ?? ''),
        'date_of_birth' => $_POST['date_of_birth'] ?: null,
        'sex' => $_POST['sex'] ?: null,
        'address' => trim($_POST['address'] ?? ''),
        'registration_date' => $_POST['registration_date'] ?: null,
        'registration_type' => trim($_POST['registration_type'] ?? ''),
        'treatment_start_date' => $_POST['treatment_start_date'] ?: null,
        'monthly_weights' => json_encode($monthlyWeights),
        'treatment_outcome' => trim($_POST['treatment_outcome'] ?? ''),
        'treatment_end_date' => $_POST['treatment_end_date'] ?: null,
        'remarks' => trim($_POST['remarks'] ?? ''),
        'patient_id' => $_POST['patient_id'] ?: null,
        'bhw_id' => $_POST['bhw_id'] ?: $_SESSION['bhw_id']
    ];
    
    try {
        if ($id) {
            $sql = "UPDATE ntp_client_monitoring SET 
                    client_name = ?, date_of_birth = ?, sex = ?, address = ?,
                    registration_date = ?, registration_type = ?, treatment_start_date = ?,
                    monthly_weights = ?, treatment_outcome = ?, treatment_end_date = ?,
                    remarks = ?, patient_id = ?, bhw_id = ?, updated_at = NOW()
                    WHERE ntp_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['client_name'], $data['date_of_birth'], $data['sex'], $data['address'],
                $data['registration_date'], $data['registration_type'], $data['treatment_start_date'],
                $data['monthly_weights'], $data['treatment_outcome'], $data['treatment_end_date'],
                $data['remarks'], $data['patient_id'], $data['bhw_id'], $id
            ]);
            $_SESSION['success'] = 'NTP client record updated successfully.';
        } else {
            $sql = "INSERT INTO ntp_client_monitoring 
                    (client_name, date_of_birth, sex, address, registration_date, registration_type,
                     treatment_start_date, monthly_weights, treatment_outcome, treatment_end_date, remarks, patient_id, bhw_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['client_name'], $data['date_of_birth'], $data['sex'], $data['address'],
                $data['registration_date'], $data['registration_type'], $data['treatment_start_date'],
                $data['monthly_weights'], $data['treatment_outcome'], $data['treatment_end_date'],
                $data['remarks'], $data['patient_id'], $data['bhw_id']
            ]);
            $_SESSION['success'] = 'NTP client record added successfully.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }
    
    header('Location: ' . BASE_URL . 'admin-health-records-ntp');
    exit;
}

/**
 * Save/Update WRA Tracking Record
 */
function saveWraTracking($pdo) {
    $id = isset($_POST['wra_id']) && $_POST['wra_id'] !== '' ? (int)$_POST['wra_id'] : null;
    
    // Handle monthly status as JSON
    $monthlyStatus = [];
    $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
    foreach ($months as $m) {
        $monthlyStatus[$m] = trim($_POST["status_$m"] ?? '');
    }
    
    $data = [
        'woman_name' => trim($_POST['woman_name'] ?? ''),
        'date_of_birth' => $_POST['date_of_birth'] ?: null,
        'address' => trim($_POST['address'] ?? ''),
        'tracking_year' => $_POST['tracking_year'] ?: date('Y'),
        'monthly_status' => json_encode($monthlyStatus),
        'fp_method' => trim($_POST['fp_method'] ?? ''),
        'is_nhts' => isset($_POST['is_nhts']) ? 1 : 0,
        'remarks' => trim($_POST['remarks'] ?? ''),
        'patient_id' => $_POST['patient_id'] ?: null,
        'bhw_id' => $_POST['bhw_id'] ?: $_SESSION['bhw_id']
    ];
    
    try {
        if ($id) {
            $sql = "UPDATE wra_tracking SET 
                    woman_name = ?, date_of_birth = ?, address = ?, tracking_year = ?,
                    monthly_status = ?, fp_method = ?, is_nhts = ?, remarks = ?,
                    patient_id = ?, bhw_id = ?, updated_at = NOW()
                    WHERE wra_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['woman_name'], $data['date_of_birth'], $data['address'], $data['tracking_year'],
                $data['monthly_status'], $data['fp_method'], $data['is_nhts'], $data['remarks'],
                $data['patient_id'], $data['bhw_id'], $id
            ]);
            $_SESSION['success'] = 'WRA tracking record updated successfully.';
        } else {
            $sql = "INSERT INTO wra_tracking 
                    (woman_name, date_of_birth, address, tracking_year, monthly_status, fp_method, is_nhts, remarks, patient_id, bhw_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['woman_name'], $data['date_of_birth'], $data['address'], $data['tracking_year'],
                $data['monthly_status'], $data['fp_method'], $data['is_nhts'], $data['remarks'],
                $data['patient_id'], $data['bhw_id']
            ]);
            $_SESSION['success'] = 'WRA tracking record added successfully.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }
    
    header('Location: ' . BASE_URL . 'admin-health-records-wra');
    exit;
}
