<?php
// actions/announcement_delete.php
// Delete an announcement

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-announcements');
    exit();
}

$announcement_id = isset($_POST['announcement_id']) ? (int) $_POST['announcement_id'] : 0;

if ($announcement_id <= 0) {
    $_SESSION['form_error'] = 'Invalid announcement id.';
    header('Location: ' . BASE_URL . 'admin-announcements');
    exit();
}

try {
    $stmt = $pdo->prepare('DELETE FROM announcements WHERE announcement_id = ?');
    $stmt->execute([$announcement_id]);
    $_SESSION['form_success'] = 'Announcement deleted.';
} catch (Throwable $e) {
    error_log('Announcement delete error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'An error occurred while deleting the announcement.';
}

header('Location: ' . BASE_URL . 'admin-announcements');
exit();
