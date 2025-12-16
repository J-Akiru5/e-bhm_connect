<?php
// pages/admin/messages.php
include_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../includes/pagination_helper.php';

$pagination = ['current_page' => 1, 'total_pages' => 1, 'total_records' => 0];
$per_page = 10;
$failed_messages = [];

try {
    // Count total failed messages
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM sms_queue WHERE status = 'failed'");
    $count_stmt->execute();
    $total_records = (int) $count_stmt->fetchColumn();
    
    // Calculate pagination
    $current_page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $pagination = paginate($total_records, $per_page, $current_page);
    
    // Fetch failed messages with pagination
    $stmt = $pdo->prepare("SELECT id, phone_number, message, created_at FROM sms_queue WHERE status = 'failed' ORDER BY created_at DESC LIMIT " . $pagination['per_page'] . " OFFSET " . $pagination['offset']);
    $stmt->execute();
    $failed_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('SMS fetch error: ' . $e->getMessage());
}

// Ensure $pdo is available (index.php loads config/database)
// Display flash messages
if (isset($_SESSION['sms_success'])) {
    $msg = json_encode($_SESSION['sms_success']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'success', title: 'SMS', text: $msg}); } else { console.log($msg); } });</script>";
    unset($_SESSION['sms_success']);
}
if (isset($_SESSION['sms_error'])) {
    $msg = json_encode($_SESSION['sms_error']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'error', title: 'SMS Error', text: $msg}); } else { console.error($msg); } });</script>";
    unset($_SESSION['sms_error']);
}

?>

<div class="container py-4">
    <h3>SMS Management</h3>

    <div class="card mb-4">
        <div class="card-header">Send Broadcast</div>
        <div class="card-body">
            <form method="post" action="?action=send-broadcast">
                <div class="mb-3">
                    <label for="message_body" class="form-label">Message Body</label>
                    <textarea id="message_body" name="message" class="form-control" rows="4" required></textarea>
                </div>
                <div>
                    <button class="btn btn-primary" type="submit">Send Broadcast</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Failed Messages</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Phone</th>
                            <th>Message</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (empty($failed_messages)) {
                            echo '<tr><td colspan="4" class="text-center">No failed messages.</td></tr>';
                        } else {
                            foreach ($failed_messages as $r) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($r['created_at']) . '</td>';
                                echo '<td>' . htmlspecialchars($r['phone_number']) . '</td>';
                                echo '<td>' . htmlspecialchars($r['message']) . '</td>';
                                echo '<td><a class="btn btn-sm btn-warning" href="?action=resend-sms&id=' . urlencode($r['id']) . '">Resend</a></td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <?php echo render_pagination($pagination, get_pagination_base_url()); ?>
            </div>
        </div>
    </div>

</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
