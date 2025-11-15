<?php
// actions/program_delete.php
// Delete a health program

// Router bootstraps session, $pdo and BASE_URL
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-programs');
    exit();
}

$program_id = isset($_POST['program_id']) ? (int) $_POST['program_id'] : 0;

if ($program_id <= 0) {
    $_SESSION['form_error'] = 'No program specified.';
    header('Location: ' . BASE_URL . 'admin-programs');
    exit();
}

try {
    $stmt = $pdo->prepare('DELETE FROM health_programs WHERE program_id = :id');
    $stmt->execute([':id' => $program_id]);

    $_SESSION['form_success'] = 'Program deleted.';
} catch (Throwable $e) {
    error_log('Program delete error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'An error occurred.';
}

header('Location: ' . BASE_URL . 'admin-programs');
exit();
