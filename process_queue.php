<?php
/**
 * process_queue.php
 *
 * Fetch pending SMS messages from `sms_queue` and forward them to the
 * Android Gateway. Intended to be run by scheduler/cron.
 */

// Include DB connection
require_once __DIR__ . '/config/database.php';

// Optional: include helper (not required for operation)
if (file_exists(__DIR__ . '/includes/sms_helper.php')) {
    require_once __DIR__ . '/includes/sms_helper.php';
}

// --- Configuration: update these values for your local Android Gateway ---
if (!defined('GATEWAY_URL')) {
    define('GATEWAY_URL', 'http://192.168.68.200:8080/send-sms'); // Simple SMS Gateway endpoint
}

$LIMIT = 5;

if (!($conn instanceof mysqli)) {
    echo "Database connection not available.\n";
    exit(1);
}

// Fetch oldest pending messages (limit to prevent overload)
$selectSql = "SELECT id, phone_number, message FROM sms_queue WHERE status = 'pending' ORDER BY created_at ASC LIMIT ?";
$stmt = $conn->prepare($selectSql);
if ($stmt === false) {
    echo "Prepare failed: " . $conn->error . "\n";
    exit(1);
}

$stmt->bind_param('i', $LIMIT);
if (!$stmt->execute()) {
    echo "Execute failed: " . $stmt->error . "\n";
    $stmt->close();
    exit(1);
}

$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (empty($messages)) {
    echo "No pending messages.\n";
    exit(0);
}

foreach ($messages as $msg) {
    $id = (int)$msg['id'];
    $phone = $msg['phone_number'];
    $message = $msg['message'];

    // Prepare payload
    $payload = json_encode(['phone' => $phone, 'message' => $message]);

    // Setup cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, GATEWAY_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $responseBody = curl_exec($ch);
    $curlErrNo = curl_errno($ch);
    $curlErr = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curlErrNo) {
        // cURL error => mark failed
        $up = $conn->prepare("UPDATE sms_queue SET status = 'failed' WHERE id = ?");
        if ($up) {
            $up->bind_param('i', $id);
            $up->execute();
            $up->close();
        }
        echo "Processed ID: {$id} - Failed (cURL error: {$curlErr})\n";
    } elseif ($httpCode === 200 || $httpCode === 202) {
        // Success => mark sent and set sent_at
        $up = $conn->prepare("UPDATE sms_queue SET status = 'sent', sent_at = NOW() WHERE id = ?");
        if ($up) {
            $up->bind_param('i', $id);
            $up->execute();
            $up->close();
        }
        echo "Processed ID: {$id} - Sent\n";
    } else {
        // Non-success HTTP code => mark failed
        $up = $conn->prepare("UPDATE sms_queue SET status = 'failed' WHERE id = ?");
        if ($up) {
            $up->bind_param('i', $id);
            $up->execute();
            $up->close();
        }
        echo "Processed ID: {$id} - Failed (HTTP {$httpCode})\n";
    }

    // Avoid hammering the Android device
    sleep(2);
}

echo "Done.\n";
