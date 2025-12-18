<?php
/**
 * BHW User Approval Action (Admin/Superadmin Only)
 * E-BHM Connect
 * 
 * Approves a pending BHW user account
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/security_helper.php';

// Require admin access
if (!is_admin()) {
    $_SESSION['flash_error'] = 'You do not have permission to perform this action.';
    header('Location: ' . BASE_URL . 'admin-dashboard');
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-bhw-users');
    exit;
}

// Verify CSRF token
if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['flash_error'] = 'Invalid security token. Please try again.';
    header('Location: ' . BASE_URL . 'admin-bhw-users');
    exit;
}

// Get user ID
$user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;

if ($user_id <= 0) {
    $_SESSION['flash_error'] = 'Invalid user ID.';
    header('Location: ' . BASE_URL . 'admin-bhw-users');
    exit;
}

try {
    // Get user info
    $stmt = $pdo->prepare("SELECT full_name, email, account_status FROM bhw_users WHERE bhw_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $_SESSION['flash_error'] = 'User not found.';
        header('Location: ' . BASE_URL . 'admin-bhw-users');
        exit;
    }
    
    if ($user['account_status'] === 'approved') {
        $_SESSION['flash_error'] = 'User is already approved.';
        header('Location: ' . BASE_URL . 'admin-bhw-users');
        exit;
    }
    
    // Update account status to approved and set as verified
    $stmt = $pdo->prepare("UPDATE bhw_users SET account_status = 'approved', email_verified = 1 WHERE bhw_id = ?");
    $stmt->execute([$user_id]);
    
    // Log the action
    log_audit('user_approve', 'bhw_user', $user_id, [
        'user_name' => $user['full_name'],
        'previous_status' => $user['account_status']
    ]);
    
    // Create notification for the approved user
    create_notification(
        $user_id,
        'bhw',
        'success',
        'Account Approved',
        'Your account has been approved! You can now log in to the system.',
        null
    );
    
    $_SESSION['flash_success'] = "Account for {$user['full_name']} has been approved successfully!";
    
} catch (Throwable $e) {
    error_log('User approval error: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Failed to approve user: ' . $e->getMessage();
}

header('Location: ' . BASE_URL . 'admin-bhw-users');
exit;
