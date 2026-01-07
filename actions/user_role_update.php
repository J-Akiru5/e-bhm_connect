<?php
/**
 * User Role Update Action (Super Admin Only)
 * E-BHM Connect
 * 
 * Handles user role changes
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/security_helper.php';

// Require superadmin access
if (!is_superadmin()) {
    $_SESSION['flash_error'] = 'You do not have permission to perform this action.';
    header('Location: ' . BASE_URL . 'admin-dashboard');
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-user-roles');
    exit;
}

// Verify CSRF token
if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['flash_error'] = 'Invalid security token. Please try again.';
    header('Location: ' . BASE_URL . 'admin-user-roles');
    exit;
}

// Get parameters
$user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
$new_role = $_POST['new_role'] ?? '';

// Validate user_id
if ($user_id <= 0) {
    $_SESSION['flash_error'] = 'Invalid user ID.';
    header('Location: ' . BASE_URL . 'admin-user-roles');
    exit;
}

// Validate role
$valid_roles = ['bhw', 'admin', 'superadmin'];
if (!in_array($new_role, $valid_roles)) {
    $_SESSION['flash_error'] = 'Invalid role selected.';
    header('Location: ' . BASE_URL . 'admin-user-roles');
    exit;
}

// Prevent changing own role
if ($user_id === (int) $_SESSION['bhw_id']) {
    $_SESSION['flash_error'] = 'You cannot change your own role.';
    header('Location: ' . BASE_URL . 'admin-user-roles');
    exit;
}

try {
    // Get current user info for audit log
    $stmt = $pdo->prepare("SELECT full_name, role FROM bhw_users WHERE bhw_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $_SESSION['flash_error'] = 'User not found.';
        header('Location: ' . BASE_URL . 'admin-user-roles');
        exit;
    }
    
    $old_role = $user['role'] ?? 'bhw';
    
    // Skip if role is the same
    if ($old_role === $new_role) {
        header('Location: ' . BASE_URL . 'admin-user-roles');
        exit;
    }
    
    // Update role
    $stmt = $pdo->prepare("UPDATE bhw_users SET role = ? WHERE bhw_id = ?");
    $stmt->execute([$new_role, $user_id]);
    
    // Log the action
    log_audit('role_change', 'bhw_user', $user_id, [
        'user_name' => $user['full_name'],
        'old_role' => $old_role,
        'new_role' => $new_role
    ]);
    
    // Create notification for the affected user
    $role_labels = [
        'bhw' => 'Health Worker',
        'admin' => 'Administrator',
        'superadmin' => 'Super Admin'
    ];
    
    create_notification(
        $user_id,
        'bhw',
        'info',
        'Role Changed',
        "Your role has been changed from {$role_labels[$old_role]} to {$role_labels[$new_role]}.",
        null
    );
    
    $_SESSION['flash_success'] = "Role for {$user['full_name']} changed from {$role_labels[$old_role]} to {$role_labels[$new_role]}.";
    $_SESSION['show_success_modal'] = true; // Trigger SweetAlert
    
} catch (Throwable $e) {
    error_log('Role update error: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Failed to update role: ' . $e->getMessage();
}

header('Location: ' . BASE_URL . 'admin-user-roles');
exit;
