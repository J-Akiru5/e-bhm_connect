<?php
// actions/sms_actions.php
// Handles send-broadcast and resend-sms actions and processes pending SMS messages.

if (!isset($_GET['action'])) {
    die('Missing action.');
}

$act = $_GET['action'];

// Helper: process pending messages
function process_batch_sms(PDO $pdo)
{
    // allow longer processing
    set_time_limit(300);

    // Basic gateway check
    if (!defined('GATEWAY_URL') || GATEWAY_URL === '') {
        error_log('GATEWAY_URL not defined for SMS sending');
        return ['sent' => 0, 'failed' => 0, 'error' => 'GATEWAY_URL not configured'];
    }

    $sent = 0;
    $failed = 0;

    try {
        $sel = $pdo->prepare("SELECT id, phone_number, message FROM sms_queue WHERE status = 'pending' ORDER BY created_at ASC LIMIT 1000");
        $sel->execute();
        $rows = $sel->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $id = $row['id'];
            $phone = $row['phone_number'];
            $message = $row['message'];

            $payload = json_encode(['to' => $phone, 'message' => $message]);

            $ch = curl_init(GATEWAY_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200) {
                $u = $pdo->prepare("UPDATE sms_queue SET status = 'sent', updated_at = NOW() WHERE id = :id");
                $u->execute([':id' => $id]);
                $sent++;
            } else {
                $u = $pdo->prepare("UPDATE sms_queue SET status = 'failed', updated_at = NOW(), last_response = :resp WHERE id = :id");
                $u->execute([':id' => $id, ':resp' => ($curlErr ?: $response)]);
                $failed++;
            }
        }
    } catch (Throwable $e) {
        error_log('process_batch_sms error: ' . $e->getMessage());
        return ['sent' => $sent, 'failed' => $failed, 'error' => $e->getMessage()];
    }

    return ['sent' => $sent, 'failed' => $failed];
}

try {
    switch ($act) {
        case 'send-broadcast':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $_SESSION['sms_error'] = 'Invalid request method for broadcast.';
                header('Location: ' . BASE_URL . 'admin-messages');
                exit();
            }

            $message = isset($_POST['message']) ? trim($_POST['message']) : '';
            if ($message === '') {
                $_SESSION['sms_error'] = 'Message body cannot be empty.';
                header('Location: ' . BASE_URL . 'admin-messages');
                exit();
            }

            // Select contacts
            $stmt = $pdo->prepare("SELECT contact FROM patients WHERE contact IS NOT NULL AND contact != ''");
            $stmt->execute();
            $contacts = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

            $inserted = 0;
            $ins = $pdo->prepare("INSERT INTO sms_queue (phone_number, message, status, created_at) VALUES (:phone, :message, 'pending', NOW())");
            foreach ($contacts as $c) {
                if (trim($c) === '') continue;
                $ins->execute([':phone' => $c, ':message' => $message]);
                $inserted++;
            }

            // Process immediately
            $result = process_batch_sms($pdo);

            $_SESSION['sms_success'] = "Queued {$inserted} messages. Sent: {$result['sent']}, Failed: {$result['failed']}";
            header('Location: ' . BASE_URL . 'admin-messages');
            exit();
            break;

        case 'resend-sms':
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if ($id <= 0) {
                $_SESSION['sms_error'] = 'Invalid message id.';
                header('Location: ' . BASE_URL . 'admin-messages');
                exit();
            }

            $u = $pdo->prepare("UPDATE sms_queue SET status = 'pending', updated_at = NOW() WHERE id = :id");
            $u->execute([':id' => $id]);

            $result = process_batch_sms($pdo);
            $_SESSION['sms_success'] = "Resend requested. Sent: {$result['sent']}, Failed: {$result['failed']}";
            header('Location: ' . BASE_URL . 'admin-messages');
            exit();
            break;

        default:
            die('Unknown SMS action.');
    }
} catch (Throwable $e) {
    error_log('sms_actions error: ' . $e->getMessage());
    $_SESSION['sms_error'] = 'An unexpected error occurred while processing SMS actions.';
    header('Location: ' . BASE_URL . 'admin-messages');
    exit();
}

?>
