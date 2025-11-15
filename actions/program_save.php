<?php
// actions/program_save.php
// Save new health program

// Router bootstraps session, $pdo and BASE_URL
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-programs');
    exit();
}

$program_name = isset($_POST['program_name']) ? trim($_POST['program_name']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? trim($_POST['start_date']) : null;
$end_date = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? trim($_POST['end_date']) : null;
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

try {
    $stmt = $pdo->prepare('INSERT INTO health_programs (program_name, description, start_date, end_date, status) VALUES (:program_name, :description, :start_date, :end_date, :status)');
    $stmt->execute([
        ':program_name' => $program_name,
        ':description' => $description,
        ':start_date' => $start_date,
        ':end_date' => $end_date,
        ':status' => $status
    ]);

    $_SESSION['form_success'] = 'Program saved.';
} catch (Throwable $e) {
    error_log('Program save error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'An error occurred.';
}

header('Location: ' . BASE_URL . 'admin-programs');
exit();
