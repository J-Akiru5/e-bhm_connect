<?php
/**
 * Quick migration script to create rate_limits table
 * Run this manually if the migration system didn't pick it up
 */

require_once __DIR__ . '/config/database.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS `rate_limits` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `rate_key` VARCHAR(255) NOT NULL COMMENT 'Composite key: action:identifier (e.g., login_bhw:192.168.1.1)',
        `attempts` INT(11) NOT NULL DEFAULT 0,
        `first_attempt_at` DATETIME NOT NULL,
        `expires_at` DATETIME NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `rate_key_unique` (`rate_key`),
        KEY `expires_at_idx` (`expires_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Rate limiting for security features'";
    
    $pdo->exec($sql);
    echo "SUCCESS: rate_limits table created or already exists.\n";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
