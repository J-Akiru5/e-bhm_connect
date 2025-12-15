<?php
// actions/update_profile.php
// Update BHW profile (full_name and username)
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-profile');
    exit();
}

$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$bhw_id = isset($_POST['bhw_id']) ? intval($_POST['bhw_id']) : 0;

if ($full_name === '' || $username === '') {
    $_SESSION['form_error'] = 'Name and username cannot be empty.';
    header('Location: ' . BASE_URL . 'admin-profile');
    exit();
}

try {
    $stmt = $pdo->prepare('UPDATE bhw_users SET full_name = :full_name, username = :username WHERE bhw_id = :bhw_id');
    $stmt->execute([
        ':full_name' => $full_name,
        ':username' => $username,
        ':bhw_id' => $bhw_id
    ]);

    // Update session so header shows new name
    $_SESSION['bhw_full_name'] = $full_name;
    $_SESSION['form_success'] = 'Profile updated successfully.';
    header('Location: ' . BASE_URL . 'admin-profile');
    exit();

} catch (Throwable $e) {
    error_log('Update profile error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'An error occurred (that username might be taken).';
    header('Location: ' . BASE_URL . 'admin-profile');
    exit();
}

?>
