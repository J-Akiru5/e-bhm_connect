<?php
// actions/announcement_save.php
// Save a new announcement

// Router bootstraps session, $pdo and BASE_URL
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-announcements');
    exit();
}

$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$bhw_id = isset($_SESSION['bhw_id']) ? (int) $_SESSION['bhw_id'] : 0;

if ($title === '' || $content === '' || $bhw_id <= 0) {
    $_SESSION['form_error'] = 'Missing required fields or not authenticated.';
    header('Location: ' . BASE_URL . 'admin-announcements');
    exit();
}

try {
    $stmt = $pdo->prepare('INSERT INTO announcements (bhw_id, title, content, created_at) VALUES (:bhw_id, :title, :content, NOW())');
    $stmt->execute([
        ':bhw_id' => $bhw_id,
        ':title' => $title,
        ':content' => $content
    ]);

    $_SESSION['form_success'] = 'Announcement posted.';
} catch (Throwable $e) {
    error_log('Announcement save error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'An error occurred while saving the announcement.';
}

header('Location: ' . BASE_URL . 'admin-announcements');
exit();
