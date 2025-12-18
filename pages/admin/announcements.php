<?php
// Announcements Management (Admin)
include_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../includes/pagination_helper.php';
?>
<style>
</style>
<?php

$announcements = [];
$pagination = ['current_page' => 1, 'total_pages' => 1, 'total_records' => 0];
$per_page = 10;

$total_announcements = 0;

try {
    // Count total
    $count_stmt = $pdo->query("SELECT COUNT(*) FROM announcements");
    $total_records = (int) $count_stmt->fetchColumn();
    $total_announcements = $total_records;
    
    // Calculate pagination
    $current_page = isset($_GET['pg']) ? max(1, (int) $_GET['pg']) : 1;
    $pagination = paginate($total_records, $per_page, $current_page);

    $stmt = $pdo->prepare("SELECT a.*, b.full_name 
                    FROM announcements a 
                    LEFT JOIN bhw_users b ON a.bhw_id = b.bhw_id 
                    ORDER BY a.created_at DESC
                    LIMIT " . $pagination['per_page'] . " OFFSET " . $pagination['offset']);
    $stmt->execute();
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Announcements fetch error: ' . $e->getMessage());
    $announcements = [];
}
?>

<div class="container">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Announcements</h1>
            <p class="page-subtitle">Post and manage community announcements</p>
        </div>
        <?php if (has_permission('manage_announcements')): ?>
        <button class="btn-primary-glass" onclick="openModal()">
            <i class="fas fa-bullhorn"></i>
            New Announcement
        </button>
        <?php endif; ?>
    </div>

    <!-- Stats Row -->
    <div class="stats-row" style="grid-template-columns: repeat(1, 1fr);">
        <div class="glass-card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div class="stat-value"><?php echo $total_announcements; ?></div>
            <div class="stat-label">Total Announcements</div>
        </div>
    </div>

    <!-- Announcements Table -->
    <div class="glass-card table-container">
        <div class="table-responsive">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Posted By</th>
                        <th>Date Posted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($announcements)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-bullhorn"></i>
                                    </div>
                                    <p>No announcements found</p>
                                    <?php if (has_permission('manage_announcements')): ?>
                                    <button class="btn-primary-glass" onclick="openModal()">Create First Announcement</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($announcements as $a): ?>
                            <tr>
                                <td data-label="Title"><?php echo htmlspecialchars($a['title'] ?? ''); ?></td>
                                <td data-label="Content" class="announcement-content" title="<?php echo htmlspecialchars($a['content'] ?? ''); ?>">
                                    <?php echo htmlspecialchars($a['content'] ?? ''); ?>
                                </td>
                                <td data-label="Posted By"><?php echo htmlspecialchars($a['full_name'] ?? 'Admin'); ?></td>
                                <td data-label="Date Posted"><?php echo isset($a['created_at']) ? date('M d, Y', strtotime($a['created_at'])) : '-'; ?></td>
                                <td data-label="Actions">
                                    <?php if (has_permission('manage_announcements')): ?>
                                    <div class="actions-cell">
                                        <a href="<?php echo BASE_URL; ?>admin-announcement-edit?id=<?php echo $a['announcement_id']; ?>" class="btn-secondary-glass btn-sm-glass">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo BASE_URL; ?>?action=delete-announcement" method="POST" class="d-inline" onsubmit="return confirmDelete(event);">
                                            <?php echo csrf_input(); ?>
                                            <input type="hidden" name="announcement_id" value="<?php echo htmlspecialchars($a['announcement_id'] ?? ''); ?>">
                                            <button type="submit" class="btn-danger-glass btn-sm-glass">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <?php else: ?>
                                    <span class="text-muted">View Only</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="pagination-container">
            <?php echo render_pagination($pagination, get_pagination_base_url()); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Announcement Modal -->
<div class="modal-overlay" id="addAnnouncementModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-bullhorn me-2" style="color: #20c997;"></i>
                Create New Announcement
            </h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="<?php echo BASE_URL; ?>?action=save-announcement">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="glass-input" required placeholder="Enter announcement title">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Content *</label>
                    <textarea name="content" class="glass-input" rows="5" required placeholder="Enter announcement content"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary-glass" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-primary-glass">
                    <i class="fas fa-paper-plane"></i>
                    Post Announcement
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('addAnnouncementModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('addAnnouncementModal').classList.remove('active');
    document.body.style.overflow = '';
}

document.getElementById('addAnnouncementModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

function confirmDelete(event) {
    event.preventDefault();
    Swal.fire({
        title: 'Delete Announcement?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
        background: 'rgba(30, 41, 59, 0.95)',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.submit();
        }
    });
    return false;
}
</script>

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

