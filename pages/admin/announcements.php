<?php
// pages/admin/announcements.php
// Admin: Manage announcements
include_once __DIR__ . '/../../includes/header_admin.php';

// Fetch announcements with author name
try {
    $stmt = $pdo->query("SELECT a.*, b.full_name 
                    FROM announcements a 
                    LEFT JOIN bhw_users b ON a.bhw_id = b.bhw_id 
                    ORDER BY a.created_at DESC");
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Announcements fetch error: ' . $e->getMessage());
    $announcements = [];
}

// Flash messages
if (isset($_SESSION['form_success'])) {
    $msg = json_encode($_SESSION['form_success']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'success', title: 'Success', text: $msg}); } });</script>";
    unset($_SESSION['form_success']);
}
if (isset($_SESSION['form_error'])) {
    $emsg = json_encode($_SESSION['form_error']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'error', title: 'Error', text: $emsg}); } });</script>";
    unset($_SESSION['form_error']);
}
?>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Create New Announcement</div>
                <div class="card-body">
                    <form method="POST" action="<?php echo BASE_URL; ?>?action=save-announcement">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea name="content" class="form-control" rows="5" required></textarea>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-primary" type="submit">Post Announcement</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Posted Announcements</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Posted By</th>
                                    <th>Date Posted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($announcements)): ?>
                                    <tr><td colspan="4">No announcements found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($announcements as $ann): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($ann['title'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($ann['full_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($ann['created_at'] ?? ''); ?></td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>admin-announcement-edit?id=<?php echo urlencode($ann['announcement_id']); ?>" class="btn btn-secondary btn-sm">Edit</a>

                                                <form action="<?php echo BASE_URL; ?>?action=delete-announcement" method="POST" class="d-inline" onsubmit="return confirmDelete(event);">
                                                    <input type="hidden" name="announcement_id" value="<?php echo htmlspecialchars($ann['announcement_id'] ?? ''); ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
