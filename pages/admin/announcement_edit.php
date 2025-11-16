<?php
// pages/admin/announcement_edit.php
include_once __DIR__ . '/../../includes/header_admin.php';

$announcement_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$announcement = null;
try {
    $stmt = $pdo->prepare('SELECT * FROM announcements WHERE announcement_id = ?');
    $stmt->execute([$announcement_id]);
    $announcement = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Announcement fetch error: ' . $e->getMessage());
}

if (!$announcement) {
    $_SESSION['form_error'] = 'Announcement not found.';
    header('Location: ' . BASE_URL . 'admin-announcements');
    exit();
}
?>

<div class="container">
    <h1 class="mb-4">Edit Announcement</h1>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?php echo BASE_URL; ?>?action=update-announcement">
                <input type="hidden" name="announcement_id" value="<?php echo htmlspecialchars($announcement['announcement_id']); ?>">

                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($announcement['title'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea name="content" class="form-control" rows="6" required><?php echo htmlspecialchars($announcement['content'] ?? ''); ?></textarea>
                </div>

                <div class="d-grid">
                    <button class="btn btn-primary" type="submit">Update Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
