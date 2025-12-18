<?php
// SMS Management (Admin)
include_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../includes/pagination_helper.php';

$pagination = ['current_page' => 1, 'total_pages' => 1, 'total_records' => 0];
$per_page = 10;
$failed_messages = [];
$sms_sent = 0;
$sms_failed = 0;
$sms_pending = 0;

try {
    // Get SMS stats
    $statsStmt = $pdo->query("SELECT 
        SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
        SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
        FROM sms_queue");
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    $sms_sent = (int) $stats['sent'];
    $sms_failed = (int) $stats['failed'];
    $sms_pending = (int) $stats['pending'];

    // Count total failed messages
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM sms_queue WHERE status = 'failed'");
    $count_stmt->execute();
    $total_records = (int) $count_stmt->fetchColumn();
    
    // Calculate pagination
    $current_page = isset($_GET['pg']) ? max(1, (int) $_GET['pg']) : 1;
    $pagination = paginate($total_records, $per_page, $current_page);
    
    // Fetch failed messages with pagination
    $stmt = $pdo->prepare("SELECT id, phone_number, message, created_at FROM sms_queue WHERE status = 'failed' ORDER BY created_at DESC LIMIT " . $pagination['per_page'] . " OFFSET " . $pagination['offset']);
    $stmt->execute();
    $failed_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('SMS fetch error: ' . $e->getMessage());
}
?>

<div class="container">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">SMS Management</h1>
            <p class="page-subtitle">Send broadcast messages and view SMS status</p>
        </div>
        <button class="btn-primary-glass" onclick="openModal()">
            <i class="fas fa-sms"></i>
            Send Broadcast
        </button>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="glass-card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div class="stat-value"><?php echo $sms_sent; ?></div>
            <div class="stat-label">Sent</div>
        </div>
        <div class="glass-card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-value"><?php echo $sms_failed; ?></div>
            <div class="stat-label">Failed</div>
        </div>
        <div class="glass-card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value"><?php echo $sms_pending; ?></div>
            <div class="stat-label">Pending</div>
        </div>
    </div>

    <!-- Failed Messages Table -->
    <div class="glass-card table-container">
        <div class="glass-card-header">
            <h3 style="color: var(--text-color, #333); margin: 0; padding: 20px;">Failed Messages</h3>
        </div>
        <div class="table-responsive">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Phone</th>
                        <th>Message</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($failed_messages)): ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p>No failed messages</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($failed_messages as $r): ?>
                            <tr>
                                <td data-label="Date"><?php echo isset($r['created_at']) ? date('M d, Y H:i', strtotime($r['created_at'])) : '-'; ?></td>
                                <td data-label="Phone"><?php echo htmlspecialchars($r['phone_number'] ?? ''); ?></td>
                                <td data-label="Message"><?php echo htmlspecialchars($r['message'] ?? ''); ?></td>
                                <td data-label="Action">
                                    <form action="?action=retry-sms" method="POST" class="d-inline">
                                        <input type="hidden" name="sms_id" value="<?php echo $r['id']; ?>">
                                        <button type="submit" class="btn-primary-glass btn-sm-glass">
                                            <i class="fas fa-redo"></i>
                                            Retry
                                        </button>
                                    </form>
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

<!-- Send Broadcast Modal -->
<div class="modal-overlay" id="sendBroadcastModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-sms me-2" style="color: #20c997;"></i>
                Send Broadcast Message
            </h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="post" action="?action=send-broadcast">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Message Body *</label>
                    <textarea id="message_body" name="message" class="glass-input" rows="4" required placeholder="Enter message to broadcast to all patients"></textarea>
                    <small style="color: rgba(255, 255, 255, 0.5); display: block; margin-top: 8px;">
                        <i class="fas fa-info-circle"></i> This will send to all registered patients
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary-glass" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-primary-glass">
                    <i class="fas fa-paper-plane"></i>
                    Send Broadcast
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('sendBroadcastModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('sendBroadcastModal').classList.remove('active');
    document.body.style.overflow = '';
}

document.getElementById('sendBroadcastModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>

<?php
// Flash messages
if (isset($_SESSION['sms_success'])) {
    $msg = json_encode($_SESSION['sms_success']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'success', title: 'SMS', text: $msg, background: 'rgba(30, 41, 59, 0.95)', color: '#ffffff'}); } });</script>";
    unset($_SESSION['sms_success']);
}
if (isset($_SESSION['sms_error'])) {
    $msg = json_encode($_SESSION['sms_error']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'error', title: 'SMS Error', text: $msg, background: 'rgba(30, 41, 59, 0.95)', color: '#ffffff'}); } });</script>";
    unset($_SESSION['sms_error']);
}

include_once __DIR__ . '/../../includes/footer_admin.php';
?>

