<?php
// actions/vital_save.php
// Save a new patient vital record
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/security_helper.php';

// Router bootstraps session, $pdo and BASE_URL
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-patients');
    exit();
}

// Validate CSRF token
require_csrf();

$patient_id = isset($_POST['patient_id']) ? (int) $_POST['patient_id'] : 0;
$blood_pressure = isset($_POST['blood_pressure']) ? trim($_POST['blood_pressure']) : '';
$heart_rate = isset($_POST['heart_rate']) ? trim($_POST['heart_rate']) : null;
$temperature = isset($_POST['temperature']) ? trim($_POST['temperature']) : null;
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

if ($patient_id <= 0) {
    $_SESSION['form_error'] = 'Patient ID is required.';
    header('Location: ' . BASE_URL . 'admin-patients');
    exit();
}

try {
    $stmt = $pdo->prepare('INSERT INTO patient_vitals (patient_id, blood_pressure, heart_rate, temperature, notes) VALUES (:patient_id, :blood_pressure, :heart_rate, :temperature, :notes)');
    $stmt->execute([
        ':patient_id' => $patient_id,
        ':blood_pressure' => $blood_pressure,
        ':heart_rate' => $heart_rate,
        ':temperature' => $temperature,
        ':notes' => $notes
    ]);

    $_SESSION['form_success'] = 'Vital sign recorded.';
} catch (Throwable $e) {
    error_log('Vital save error: ' . $e->getMessage());
    if (defined('APP_ENV') && APP_ENV === 'development') {
        $_SESSION['form_error'] = 'An error occurred: ' . $e->getMessage();
    } else {
        $_SESSION['form_error'] = 'An error occurred.';
    }
}

header('Location: ' . BASE_URL . 'admin-patient-view?id=' . $patient_id);
exit();
