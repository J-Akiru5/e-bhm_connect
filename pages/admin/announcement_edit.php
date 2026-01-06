<?php
// pages/admin/announcement_edit.php
// Included by router, $pdo and session already available via header

require_once __DIR__ . '/../../includes/header_admin.php';

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
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Announcement</h1>
            <p class="page-subtitle">Update announcement details</p>
        </div>
        <a href="<?php echo BASE_URL; ?>admin-announcements" class="btn-secondary-glass">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="glass-card">
        <div class="glass-card-body">
            <form method="POST" action="<?php echo BASE_URL; ?>?action=update-announcement">
                <?php echo csrf_input(); ?>
                <input type="hidden" name="announcement_id" value="<?php echo htmlspecialchars($announcement['announcement_id']); ?>">

                <div class="form-group">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="glass-input" required value="<?php echo htmlspecialchars($announcement['title'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Content *</label>
                    <textarea name="content" class="glass-input" rows="6" required><?php echo htmlspecialchars($announcement['content'] ?? ''); ?></textarea>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button class="btn-primary-glass" type="submit">
                        <i class="fas fa-save"></i> Update Announcement
                    </button>
                    <a href="<?php echo BASE_URL; ?>admin-announcements" class="btn-secondary-glass">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Flash messages
if (isset($_SESSION['form_success'])) {
    $msg = json_encode($_SESSION['form_success']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'success', title: 'Success', text: $msg, background: 'rgba(30, 41, 59, 0.95)', color: '#ffffff'}); } });</script>";
    unset($_SESSION['form_success']);
}
if (isset($_SESSION['form_error'])) {
    $emsg = json_encode($_SESSION['form_error']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'error', title: 'Error', text: $emsg, background: 'rgba(30, 41, 59, 0.95)', color: '#ffffff'}); } });</script>";
    unset($_SESSION['form_error']);
}

include_once __DIR__ . '/../../includes/footer_admin.php';
?>

