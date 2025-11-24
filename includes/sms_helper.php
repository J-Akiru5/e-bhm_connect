<?php
/**
 * SMS helper functions
 *
 * Adds messages to the sms_queue table with status 'pending'.
 */

if (!function_exists('queue_sms')) {
    /**
     * Queue an SMS for later sending.
     *
     * @param mysqli $conn   MySQLi connection object (from config/database.php)
     * @param string $phone  Recipient phone number
     * @param string $message Message body
     * @return bool True on success, False on failure
     */
    function queue_sms($conn, $phone, $message)
    {
        if (!($conn instanceof mysqli)) {
            return false;
        }

        $phone = trim((string)$phone);
        $message = trim((string)$message);

        if ($phone === '' || $message === '') {
            return false;
        }

        $sql = "INSERT INTO sms_queue (phone_number, message, status, created_at) VALUES (?, ?, 'pending', NOW())";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        // Bind and execute
        if (!$stmt->bind_param('ss', $phone, $message)) {
            $stmt->close();
            return false;
        }

        $exec = $stmt->execute();
        if ($exec === false) {
            $stmt->close();
            return false;
        }

        $stmt->close();
        return true;
    }
}
