<?php
// actions/visit_save.php
// Save a new health visit record
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';


// Router bootstraps session, $pdo and BASE_URL
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-patients');
    exit();
}

$patient_id = isset($_POST['patient_id']) ? (int) $_POST['patient_id'] : 0;
$visit_date = isset($_POST['visit_date']) && $_POST['visit_date'] !== '' ? trim($_POST['visit_date']) : null;
$visit_type = isset($_POST['visit_type']) ? trim($_POST['visit_type']) : '';
$remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';
$bhw_id = isset($_SESSION['bhw_id']) ? (int) $_SESSION['bhw_id'] : 0;

if ($patient_id <= 0 || $bhw_id <= 0) {
    $_SESSION['form_error'] = 'Missing patient or BHW identification.';
    header('Location: ' . BASE_URL . 'admin-patient-view?id=' . $patient_id);
    exit();
}

try {
    $stmt = $pdo->prepare('INSERT INTO health_visits (patient_id, bhw_id, visit_date, visit_type, remarks) VALUES (:patient_id, :bhw_id, :visit_date, :visit_type, :remarks)');
    $stmt->execute([
        ':patient_id' => $patient_id,
        ':bhw_id' => $bhw_id,
        ':visit_date' => $visit_date,
        ':visit_type' => $visit_type,
        ':remarks' => $remarks
    ]);

    $_SESSION['form_success'] = 'Health visit recorded.';
} catch (Throwable $e) {
    error_log('Visit save error: ' . $e->getMessage());
    if (defined('APP_ENV') && APP_ENV === 'development') {
        $_SESSION['form_error'] = 'An error occurred: ' . $e->getMessage();
    } else {
        $_SESSION['form_error'] = 'An error occurred.';
    }
}

header('Location: ' . BASE_URL . 'admin-patient-view?id=' . $patient_id);
exit();
