<?php
/**
 * Quick fix script to approve all verified accounts
 * Run this once to fix existing accounts that can't log in
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

try {
    // Update all accounts that have email_verified = 1 to be approved
    $stmt = $pdo->prepare("UPDATE bhw_users SET account_status = 'approved' WHERE email_verified = 1 OR role IN ('admin', 'superadmin')");
    $stmt->execute();
    $affected = $stmt->rowCount();
    
    echo "<h2>Fix Complete!</h2>";
    echo "<p>Updated {$affected} accounts to 'approved' status.</p>";
    echo "<p><strong>You can now log in.</strong></p>";
    echo "<p><a href='" . BASE_URL . "login-bhw'>Go to Login</a></p>";
    echo "<hr>";
    echo "<p style='color: red;'><strong>IMPORTANT:</strong> Delete this file (fix_accounts.php) after use for security.</p>";
    
} catch (Throwable $e) {
    echo "<h2>Error</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
