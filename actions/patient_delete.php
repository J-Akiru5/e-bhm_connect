<?php
// actions/patient_delete.php
// Delete a patient (router bootstraps session and DB)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-patients');
    exit();
}

if (!isset($_POST['patient_id']) || trim($_POST['patient_id']) === '') {
    $_SESSION['form_error'] = 'No patient specified for deletion.';
    header('Location: ' . BASE_URL . 'admin-patients');
    exit();
}

$patient_id = (int) $_POST['patient_id'];

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('DELETE FROM patients WHERE patient_id = :id');
    $stmt->execute([':id' => $patient_id]);

    $pdo->commit();
    $_SESSION['form_success'] = 'Patient deleted successfully.';
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Patient delete error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'An error occurred while deleting the patient.';
}

header('Location: ' . BASE_URL . 'admin-patients');
exit();
