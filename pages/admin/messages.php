<?php
// pages/admin/messages.php
include_once __DIR__ . '/../../includes/header_admin.php';

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
                        try {
                            $stmt = $pdo->prepare("SELECT id, phone_number, message, created_at FROM sms_queue WHERE status = 'failed' ORDER BY created_at DESC");
                            $stmt->execute();
                            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (!$rows) {
                                echo '<tr><td colspan="4" class="text-center">No failed messages.</td></tr>';
                            } else {
                                foreach ($rows as $r) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($r['created_at']) . '</td>';
                                    echo '<td>' . htmlspecialchars($r['phone_number']) . '</td>';
                                    echo '<td>' . htmlspecialchars($r['message']) . '</td>';
                                    echo '<td><a class="btn btn-sm btn-warning" href="?action=resend-sms&id=' . urlencode($r['id']) . '">Resend</a></td>';
                                    echo '</tr>';
                                }
                            }
                        } catch (Throwable $e) {
                            echo '<tr><td colspan="4" class="text-danger">Error loading failed messages.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
