<?php
// actions/change_password.php
// Change BHW password

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_helpers.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-profile');
    exit();
}

$bhw_id = isset($_POST['bhw_id']) ? intval($_POST['bhw_id']) : 0;
$old_password = isset($_POST['old_password']) ? $_POST['old_password'] : '';
$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
$confirm_new_password = isset($_POST['confirm_new_password']) ? $_POST['confirm_new_password'] : '';

if ($new_password !== $confirm_new_password) {
    $_SESSION['form_error'] = 'New passwords do not match.';
    header('Location: ' . BASE_URL . 'admin-profile');
    exit();
}

try {
    // Fetch current password hash
    $stmt = $pdo->prepare('SELECT password_hash FROM bhw_users WHERE bhw_id = :bhw_id LIMIT 1');
    $stmt->execute([':bhw_id' => $bhw_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !isset($user['password_hash']) || !password_verify($old_password, $user['password_hash'])) {
        $_SESSION['form_error'] = 'Your current password was incorrect.';
        header('Location: ' . BASE_URL . 'admin-profile');
        exit();
    }

    // Hash new password and update
    $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
    $update = $pdo->prepare('UPDATE bhw_users SET password_hash = :password_hash WHERE bhw_id = :bhw_id');
    $update->execute([':password_hash' => $new_password_hash, ':bhw_id' => $bhw_id]);

    log_audit('change_password', 'bhw', $bhw_id);
    $_SESSION['form_success'] = 'Password changed successfully.';
    header('Location: ' . BASE_URL . 'admin-profile');
    exit();

} catch (Throwable $e) {
    error_log('Change password error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'An unexpected error occurred.';
    header('Location: ' . BASE_URL . 'admin-profile');
    exit();
}

?>
