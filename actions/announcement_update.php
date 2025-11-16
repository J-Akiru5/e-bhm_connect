<?php
// actions/announcement_update.php
// Update an existing announcement

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-announcements');
    exit();
}

$announcement_id = isset($_POST['announcement_id']) ? (int) $_POST['announcement_id'] : 0;
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

if ($announcement_id <= 0 || $title === '' || $content === '') {
    $_SESSION['form_error'] = 'Missing required fields.';
    header('Location: ' . BASE_URL . 'admin-announcements');
    exit();
}

try {
    $stmt = $pdo->prepare('UPDATE announcements SET title = :title, content = :content WHERE announcement_id = :id');
    $stmt->execute([
        ':title' => $title,
        ':content' => $content,
        ':id' => $announcement_id
    ]);

    $_SESSION['form_success'] = 'Announcement updated.';
} catch (Throwable $e) {
    error_log('Announcement update error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'An error occurred while updating the announcement.';
}

header('Location: ' . BASE_URL . 'admin-announcements');
exit();
