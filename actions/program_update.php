<?php
// actions/program_update.php
// Update existing health program

// Router bootstraps session, $pdo and BASE_URL
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-programs');
    exit();
}

$program_id = isset($_POST['program_id']) ? (int) $_POST['program_id'] : 0;
$program_name = isset($_POST['program_name']) ? trim($_POST['program_name']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? trim($_POST['start_date']) : null;
$end_date = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? trim($_POST['end_date']) : null;
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

if ($program_id <= 0) {
    $_SESSION['form_error'] = 'Invalid program specified.';
    header('Location: ' . BASE_URL . 'admin-programs');
    exit();
}

try {
    $stmt = $pdo->prepare('UPDATE health_programs SET program_name = :program_name, description = :description, start_date = :start_date, end_date = :end_date, status = :status WHERE program_id = :program_id');
    $stmt->execute([
        ':program_name' => $program_name,
        ':description' => $description,
        ':start_date' => $start_date,
        ':end_date' => $end_date,
        ':status' => $status,
        ':program_id' => $program_id
    ]);

    $_SESSION['form_success'] = 'Program updated.';
} catch (Throwable $e) {
    error_log('Program update error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'An error occurred.';
}

header('Location: ' . BASE_URL . 'admin-programs');
exit();
