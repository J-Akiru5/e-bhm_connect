<?php
/**
 * Resident Account Actions (Superadmin Only)
 * Handles: enable, disable, reset-password, delete
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth_helpers.php';

// Security: Only superadmin can access
if (!isset($_SESSION['bhw_id']) || !is_superadmin()) {
    $_SESSION['error'] = 'Unauthorized access.';
    header('Location: ' . BASE_URL . 'admin-dashboard');
    exit();
}

// Verify CSRF
if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['error'] = 'Invalid security token. Please try again.';
    header('Location: ' . BASE_URL . 'admin-registered-residents');
    exit();
}

$actionType = $_POST['action_type'] ?? '';
$userId = (int)($_POST['user_id'] ?? 0);

if (!$userId) {
    $_SESSION['error'] = 'Invalid user ID.';
    header('Location: ' . BASE_URL . 'admin-registered-residents');
    exit();
}

try {
    switch ($actionType) {
        case 'enable':
            $stmt = $pdo->prepare("UPDATE patient_users SET status = 'active' WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            log_audit('enable_resident_account', 'patient_users', $userId, ['action' => 'Account enabled']);
            $_SESSION['success'] = 'Account has been enabled.';
            break;

        case 'disable':
            $stmt = $pdo->prepare("UPDATE patient_users SET status = 'disabled' WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            log_audit('disable_resident_account', 'patient_users', $userId, ['action' => 'Account disabled']);
            $_SESSION['success'] = 'Account has been disabled.';
            break;

        case 'reset-password':
            // Generate a random password
            $newPassword = bin2hex(random_bytes(4)); // 8 character hex string
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("UPDATE patient_users SET password_hash = ? WHERE user_id = ?");
            $stmt->execute([$hashedPassword, $userId]);
            
            // Get email for logging
            $stmt = $pdo->prepare("SELECT email FROM patient_users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $email = $stmt->fetchColumn();
            
            log_audit('reset_resident_password', 'patient_users', $userId, ['email' => $email]);
            
            $_SESSION['success'] = "Password reset successfully!<br><strong>New Password:</strong> <code>$newPassword</code><br>Please provide this to the resident securely.";
            break;

        default:
            $_SESSION['error'] = 'Invalid action type.';
    }
} catch (PDOException $e) {
    error_log("Resident account action error: " . $e->getMessage());
    $_SESSION['error'] = 'An error occurred. Please try again.';
}

header('Location: ' . BASE_URL . 'admin-registered-residents');
exit();
