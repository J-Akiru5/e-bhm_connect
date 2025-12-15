<?php
// actions/bhw_update.php
// Update BHW user information

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-bhw-users');
    exit();
}

$bhw_id = isset($_POST['bhw_id']) ? intval($_POST['bhw_id']) : 0;
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$bhw_unique_id = isset($_POST['bhw_unique_id']) ? trim($_POST['bhw_unique_id']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';
$birthdate = isset($_POST['birthdate']) ? trim($_POST['birthdate']) : null;
$contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
$training_cert = isset($_POST['training_cert']) ? trim($_POST['training_cert']) : '';
$assigned_area = isset($_POST['assigned_area']) ? trim($_POST['assigned_area']) : '';
$employment_status = isset($_POST['employment_status']) ? trim($_POST['employment_status']) : '';

if ($bhw_id <= 0) {
    $_SESSION['form_error'] = 'Invalid BHW ID.';
    header('Location: ' . BASE_URL . 'admin-bhw-users');
    exit();
}

try {
    $sql = 'UPDATE bhw_users SET full_name = :full_name, username = :username, bhw_unique_id = :bhw_unique_id, address = :address, birthdate = :birthdate, contact = :contact, training_cert = :training_cert, assigned_area = :assigned_area, employment_status = :employment_status WHERE bhw_id = :bhw_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':full_name' => $full_name,
        ':username' => $username,
        ':bhw_unique_id' => $bhw_unique_id,
        ':address' => $address,
        ':birthdate' => $birthdate,
        ':contact' => $contact,
        ':training_cert' => $training_cert,
        ':assigned_area' => $assigned_area,
        ':employment_status' => $employment_status,
        ':bhw_id' => $bhw_id
    ]);

    $_SESSION['form_success'] = 'BHW information updated.';
    header('Location: ' . BASE_URL . 'admin-bhw-users');
    exit();

} catch (Throwable $e) {
    error_log('BHW update error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'An error occurred (username or ID may be taken).';
    header('Location: ' . BASE_URL . 'admin-bhw-users');
    exit();
}

?>
