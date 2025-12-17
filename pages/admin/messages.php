<?php
// SMS Management (Admin)
include_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../includes/pagination_helper.php';
?>
<style>
/* Glassmorphism Design - Inline Styles */
.glass-card {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.glass-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.16);
}

.page-header {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 24px;
}

@media (min-width: 768px) {
    .page-header {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
}

.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
}

.page-subtitle {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.875rem;
    margin-top: 4px;
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

@media (min-width: 768px) {
    .stats-row {
        grid-template-columns: repeat(3, 1fr);
    }
}

.stat-card {
    padding: 20px;
    text-align: center;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    font-size: 1.25rem;
}

.stat-icon.primary { background: rgba(32, 201, 151, 0.2); color: #20c997; }
.stat-icon.danger { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
.stat-icon.warning { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1;
}

.stat-label {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-top: 4px;
}

.glass-card-header {
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.table-container {
    padding: 0;
    overflow: hidden;
}

.glass-table {
    width: 100%;
    border-collapse: collapse;
}

.glass-table thead th {
    padding: 16px 20px;
    text-align: left;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: rgba(255, 255, 255, 0.7);
    background: rgba(255, 255, 255, 0.05);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.glass-table tbody td {
    padding: 16px 20px;
    color: #ffffff;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    vertical-align: middle;
}

.glass-table tbody tr {
    transition: background 0.15s ease;
}

.glass-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.05);
}

.glass-table tbody tr:last-child td {
    border-bottom: none;
}

.glass-input {
    width: 100%;
    padding: 12px 16px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    color: #ffffff;
    font-size: 1rem;
    transition: all 0.25s ease;
}

.glass-input:hover {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.25);
}

.glass-input:focus {
    outline: none;
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid #20c997;
    box-shadow: 0 0 0 4px rgba(32, 201, 151, 0.15);
}

.glass-input::placeholder {
    color: rgba(255, 255, 255, 0.4);
}

.btn-primary-glass {
    padding: 12px 24px;
    background: linear-gradient(135deg, #20c997, #0f5132);
    border: none;
    border-radius: 12px;
    color: #ffffff;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 4px 16px rgba(32, 201, 151, 0.35);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-primary-glass:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(32, 201, 151, 0.45);
    color: #ffffff;
}

.btn-secondary-glass {
    padding: 12px 24px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    color: #ffffff;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.25s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-secondary-glass:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}

.btn-sm-glass {
    padding: 8px 16px;
    font-size: 0.75rem;
    border-radius: 8px;
}

.btn-danger-glass {
    background: rgba(239, 68, 68, 0.2);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
}

.btn-danger-glass:hover {
    background: rgba(239, 68, 68, 0.3);
}

.actions-cell {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.empty-state {
    text-align: center;
    padding: 48px 24px;
    color: rgba(255, 255, 255, 0.5);
}

.empty-state-icon {
    font-size: 3rem;
    margin-bottom: 16px;
    opacity: 0.5;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    padding: 16px;
}

.modal-overlay.active {
    opacity: 1;
    visibility: visible;
}

.modal-content {
    background: rgba(30, 41, 59, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 24px;
    width: 100%;
    max-width: 560px;
    max-height: 90vh;
    overflow-y: auto;
    transform: translateY(20px) scale(0.95);
    transition: all 0.3s ease;
    box-shadow: 0 24px 64px rgba(0, 0, 0, 0.4);
}

.modal-overlay.active .modal-content {
    transform: translateY(0) scale(1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 28px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #ffffff;
    margin: 0;
}

.modal-close {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: rgba(255, 255, 255, 0.7);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
}

.modal-body {
    padding: 28px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 20px 28px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.875rem;
    margin-bottom: 8px;
}

.pagination-container {
    padding: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

@media (max-width: 767px) {
    .glass-table thead { display: none; }
    
    .glass-table tbody tr {
        display: block;
        padding: 16px;
        margin-bottom: 12px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 12px;
    }
    
    .glass-table tbody td {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .glass-table tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    
    .glass-table tbody td:last-child {
        border-bottom: none;
    }
    
    .actions-cell {
        justify-content: flex-end;
    }
}
</style>
<?php

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
            <h3 style="color: #ffffff; margin: 0; padding: 20px;">Failed Messages</h3>
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

