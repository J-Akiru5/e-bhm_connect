<?php
// actions/announcement_delete.php
// Delete an announcement with comprehensive error handling

// DEBUG: Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_log('=== ANNOUNCEMENT DELETE ACTION STARTED ===');
error_log('POST data: ' . print_r($_POST, true));
error_log('REQUEST_METHOD: ' . $_SERVER['REQUEST_METHOD']);

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_log('Session bhw_id: ' . ($_SESSION['bhw_id'] ?? 'NOT SET'));
error_log('Session csrf_token (first 20): ' . substr($_SESSION['csrf_token'] ?? 'NOT SET', 0, 20));
error_log('POST csrf_token (first 20): ' . substr($_POST['csrf_token'] ?? 'NOT SET', 0, 20));

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/security_helper.php';

// Must be POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['form_error'] = 'Invalid request method. Please use the delete button.';
    header('Location: ' . BASE_URL . 'admin-announcements');
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['bhw_id'])) {
    $_SESSION['form_error'] = 'You must be logged in to delete announcements.';
    header('Location: ' . BASE_URL . 'login-bhw');
    exit();
}

// Check permission
if (!has_permission('manage_announcements')) {
    $_SESSION['form_error'] = 'You do not have permission to delete announcements.';
    header('Location: ' . BASE_URL . 'admin-announcements');
    exit();
}

// Validate CSRF token
try {
    require_csrf();
} catch (Throwable $e) {
    error_log('CSRF validation failed: ' . $e->getMessage());
    $_SESSION['form_error'] = 'Security token expired. Please refresh the page and try again.';
    header('Location: ' . BASE_URL . 'admin-announcements');
    exit();
}

$announcement_id = isset($_POST['announcement_id']) ? (int) $_POST['announcement_id'] : 0;

if ($announcement_id <= 0) {
    $_SESSION['form_error'] = 'Invalid announcement ID provided.';
    header('Location: ' . BASE_URL . 'admin-announcements');
    exit();
}

try {
    // First check if the announcement exists
    $checkStmt = $pdo->prepare('SELECT announcement_id, title FROM announcements WHERE announcement_id = ?');
    $checkStmt->execute([$announcement_id]);
    $announcement = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$announcement) {
        $_SESSION['form_error'] = 'Announcement not found. It may have already been deleted.';
        header('Location: ' . BASE_URL . 'admin-announcements');
        exit();
    }
    
    // Perform the delete
    $stmt = $pdo->prepare('DELETE FROM announcements WHERE announcement_id = ?');
    $result = $stmt->execute([$announcement_id]);
    
    if ($result && $stmt->rowCount() > 0) {
        log_audit('delete_announcement', 'announcement', $announcement_id, [
            'title' => $announcement['title']
        ]);
        $_SESSION['form_success'] = 'Announcement "' . htmlspecialchars($announcement['title']) . '" has been deleted successfully.';
    } else {
        $_SESSION['form_error'] = 'Failed to delete announcement. No rows affected.';
    }
    
} catch (PDOException $e) {
    error_log('Announcement delete DB error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'Database error: Unable to delete announcement. Please try again.';
} catch (Throwable $e) {
    error_log('Announcement delete error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'An unexpected error occurred: ' . $e->getMessage();
}

header('Location: ' . BASE_URL . 'admin-announcements');
exit();

