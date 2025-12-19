<?php
/**
 * Fix patient_users table after restore
 * Run this file once to add missing columns
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

echo "<h2>Fixing patient_users table...</h2>";

try {
    // Check if columns exist, add if not
    $columns = [];
    $stmt = $pdo->query("DESCRIBE patient_users");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $row['Field'];
    }
    
    $alterStatements = [];
    
    if (!in_array('email_verified', $columns)) {
        $alterStatements[] = "ADD COLUMN `email_verified` TINYINT(1) DEFAULT 1";
    }
    
    if (!in_array('status', $columns)) {
        $alterStatements[] = "ADD COLUMN `status` ENUM('active','pending','suspended') DEFAULT 'active'";
    }
    
    if (!in_array('verification_token', $columns)) {
        $alterStatements[] = "ADD COLUMN `verification_token` VARCHAR(255) NULL";
    }
    
    if (!in_array('verification_expires_at', $columns)) {
        $alterStatements[] = "ADD COLUMN `verification_expires_at` DATETIME NULL";
    }
    
    if (empty($alterStatements)) {
        echo "<p style='color: green;'>✅ All columns already exist. No changes needed.</p>";
    } else {
        $sql = "ALTER TABLE `patient_users` " . implode(", ", $alterStatements);
        $pdo->exec($sql);
        echo "<p style='color: green;'>✅ Added " . count($alterStatements) . " missing columns:</p>";
        echo "<ul>";
        foreach ($alterStatements as $stmt) {
            echo "<li>" . htmlspecialchars($stmt) . "</li>";
        }
        echo "</ul>";
    }
    
    // Set all existing users to verified and active
    $pdo->exec("UPDATE patient_users SET email_verified = 1, status = 'active' WHERE email_verified IS NULL OR status IS NULL");
    echo "<p style='color: green;'>✅ All existing patient accounts set to verified and active.</p>";
    
    echo "<h3>Done! You can now log in to the Patient Portal.</h3>";
    echo "<p><a href='" . BASE_URL . "login-patient'>Go to Patient Login</a></p>";
    
} catch (Throwable $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
