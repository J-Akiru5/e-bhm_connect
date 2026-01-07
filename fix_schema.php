<?php
/**
 * Schema Validation & Auto-Repair Tool
 * E-BHM Connect - Database Schema Fixer
 * 
 * USAGE: php fix_schema.php
 * 
 * Checks for missing columns/tables and adds them automatically.
 * Safe to run multiple times - uses conditional checks.
 */

// Database configuration
$host = 'localhost';
$db = 'e-bhw_connect';
$user = 'root';
$pass = '';

echo "==============================================\n";
echo "  E-BHM Connect Schema Validator & Fixer\n";
echo "==============================================\n\n";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connected to database '$db' successfully.\n";
    echo "✓ MySQL Version: " . $pdo->query('SELECT VERSION()')->fetchColumn() . "\n\n";
    
    $fixed = 0;
    $already_ok = 0;
    $errors = 0;
    
    // Define expected schema
    $schema_checks = [
        // Medication Inventory - New columns from migration 2025-12-08
        [
            'table' => 'medication_inventory',
            'column' => 'category_id',
            'definition' => 'INT(11) NULL DEFAULT NULL AFTER item_id',
            'description' => 'Link to inventory categories'
        ],
        [
            'table' => 'medication_inventory',
            'column' => 'batch_number',
            'definition' => 'VARCHAR(100) NULL DEFAULT NULL AFTER category_id',
            'description' => 'Batch/lot number for tracking'
        ],
        [
            'table' => 'medication_inventory',
            'column' => 'expiry_date',
            'definition' => 'DATE NULL DEFAULT NULL AFTER batch_number',
            'description' => 'Expiration date for medicines'
        ],
        [
            'table' => 'medication_inventory',
            'column' => 'stock_alert_limit',
            'definition' => 'INT(11) NOT NULL DEFAULT 10 AFTER expiry_date',
            'description' => 'Low stock alert threshold'
        ],
        
        // BHW Users - New columns
        [
            'table' => 'bhw_users',
            'column' => 'last_login',
            'definition' => 'DATETIME NULL DEFAULT NULL AFTER created_at',
            'description' => 'Track last login timestamp'
        ],
        [
            'table' => 'bhw_users',
            'column' => 'email',
            'definition' => 'VARCHAR(255) NULL DEFAULT NULL AFTER username',
            'description' => 'Email address for notifications'
        ],
        [
            'table' => 'bhw_users',
            'column' => 'role',
            'definition' => "ENUM('bhw', 'admin', 'superadmin') NOT NULL DEFAULT 'bhw' AFTER password_hash",
            'description' => 'User role/permission level'
        ],
        [
            'table' => 'bhw_users',
            'column' => 'account_status',
            'definition' => "ENUM('pending', 'approved', 'rejected', 'suspended') NOT NULL DEFAULT 'pending' AFTER role",
            'description' => 'Account verification status'
        ],
        [
            'table' => 'bhw_users',
            'column' => 'avatar',
            'definition' => 'VARCHAR(255) NULL DEFAULT NULL AFTER account_status',
            'description' => 'Profile picture path'
        ],
    ];
    
    // Tables that must exist
    $required_tables = [
        [
            'name' => 'inventory_categories',
            'sql' => "CREATE TABLE `inventory_categories` (
                `category_id` INT(11) NOT NULL AUTO_INCREMENT,
                `category_name` VARCHAR(191) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`category_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        ],
        [
            'name' => 'audit_logs',
            'sql' => "CREATE TABLE `audit_logs` (
                `log_id` INT(11) NOT NULL AUTO_INCREMENT,
                `user_id` INT(11) DEFAULT NULL,
                `user_type` ENUM('bhw', 'patient', 'system') NOT NULL DEFAULT 'bhw',
                `action` VARCHAR(100) NOT NULL,
                `entity_type` VARCHAR(50) DEFAULT NULL,
                `entity_id` INT(11) DEFAULT NULL,
                `old_values` JSON DEFAULT NULL,
                `new_values` JSON DEFAULT NULL,
                `ip_address` VARCHAR(45) DEFAULT NULL,
                `user_agent` VARCHAR(255) DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`log_id`),
                KEY `idx_audit_user` (`user_id`, `user_type`),
                KEY `idx_audit_action` (`action`),
                KEY `idx_audit_created` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        ],
        [
            'name' => 'notifications',
            'sql' => "CREATE TABLE `notifications` (
                `notification_id` INT(11) NOT NULL AUTO_INCREMENT,
                `user_id` INT(11) NOT NULL,
                `user_type` ENUM('bhw', 'patient') NOT NULL DEFAULT 'bhw',
                `title` VARCHAR(255) NOT NULL,
                `message` TEXT NOT NULL,
                `type` ENUM('info', 'success', 'warning', 'error') NOT NULL DEFAULT 'info',
                `link` VARCHAR(255) DEFAULT NULL,
                `is_read` TINYINT(1) NOT NULL DEFAULT 0,
                `read_at` TIMESTAMP NULL DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`notification_id`),
                KEY `idx_notif_user` (`user_id`, `user_type`),
                KEY `idx_notif_unread` (`user_id`, `user_type`, `is_read`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        ],
        [
            'name' => 'rate_limits',
            'sql' => "CREATE TABLE `rate_limits` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `identifier` VARCHAR(255) NOT NULL,
                `action` VARCHAR(100) NOT NULL,
                `attempts` INT(11) NOT NULL DEFAULT 1,
                `last_attempt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `blocked_until` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_action_identifier` (`action`, `identifier`),
                KEY `idx_blocked_until` (`blocked_until`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        ],
        [
            'name' => 'sms_queue',
            'sql' => "CREATE TABLE `sms_queue` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `phone_number` VARCHAR(20) NOT NULL,
                `message` TEXT NOT NULL,
                `status` ENUM('pending', 'sent', 'failed') NOT NULL DEFAULT 'pending',
                `attempts` INT(11) NOT NULL DEFAULT 0,
                `error_message` TEXT DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `sent_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_status` (`status`),
                KEY `idx_created` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        ]
    ];
    
    echo "════════════════════════════════════════════════\n";
    echo "STEP 1: Checking Required Tables\n";
    echo "════════════════════════════════════════════════\n\n";
    
    foreach ($required_tables as $table) {
        $tableName = $table['name'];
        echo "► Checking table: $tableName\n";
        
        $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
        if ($stmt->rowCount() === 0) {
            echo "  ⚠ Table missing - creating...\n";
            try {
                $pdo->exec($table['sql']);
                echo "  ✓ Table created successfully\n";
                $fixed++;
            } catch (PDOException $e) {
                echo "  ✗ ERROR: " . $e->getMessage() . "\n";
                $errors++;
            }
        } else {
            echo "  ✓ Table exists\n";
            $already_ok++;
        }
    }
    
    echo "\n════════════════════════════════════════════════\n";
    echo "STEP 2: Checking Column Structure\n";
    echo "════════════════════════════════════════════════\n\n";
    
    foreach ($schema_checks as $check) {
        $table = $check['table'];
        $column = $check['column'];
        $definition = $check['definition'];
        $description = $check['description'];
        
        echo "► Checking: $table.$column\n";
        echo "  Purpose: $description\n";
        
        // Check if column exists
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = :table 
            AND COLUMN_NAME = :column
        ");
        $stmt->execute([':table' => $table, ':column' => $column]);
        $exists = (int) $stmt->fetchColumn();
        
        if ($exists === 0) {
            echo "  ⚠ Column missing - adding...\n";
            try {
                $sql = "ALTER TABLE `$table` ADD COLUMN `$column` $definition";
                $pdo->exec($sql);
                echo "  ✓ Column added successfully\n";
                $fixed++;
            } catch (PDOException $e) {
                echo "  ✗ ERROR: " . $e->getMessage() . "\n";
                $errors++;
            }
        } else {
            echo "  ✓ Column exists\n";
            $already_ok++;
        }
    }
    
    echo "\n════════════════════════════════════════════════\n";
    echo "STEP 3: Creating Indexes for Performance\n";
    echo "════════════════════════════════════════════════\n\n";
    
    $indexes = [
        [
            'table' => 'medication_inventory',
            'name' => 'idx_med_category',
            'columns' => 'category_id',
            'sql' => 'ALTER TABLE medication_inventory ADD INDEX idx_med_category (category_id)'
        ],
        [
            'table' => 'bhw_users',
            'name' => 'idx_bhw_role',
            'columns' => 'role',
            'sql' => 'ALTER TABLE bhw_users ADD INDEX idx_bhw_role (role)'
        ],
        [
            'table' => 'bhw_users',
            'name' => 'idx_bhw_status',
            'columns' => 'account_status',
            'sql' => 'ALTER TABLE bhw_users ADD INDEX idx_bhw_status (account_status)'
        ],
    ];
    
    foreach ($indexes as $index) {
        echo "► Checking index: {$index['name']} on {$index['table']}\n";
        
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM information_schema.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = :table 
            AND INDEX_NAME = :index
        ");
        $stmt->execute([':table' => $index['table'], ':index' => $index['name']]);
        $exists = (int) $stmt->fetchColumn();
        
        if ($exists === 0) {
            echo "  ⚠ Index missing - creating...\n";
            try {
                $pdo->exec($index['sql']);
                echo "  ✓ Index created successfully\n";
                $fixed++;
            } catch (PDOException $e) {
                // Index might fail if column doesn't exist, that's ok
                if (strpos($e->getMessage(), 'Duplicate') !== false) {
                    echo "  ⊘ Index already exists (different detection method)\n";
                    $already_ok++;
                } else {
                    echo "  ⊘ SKIP: " . $e->getMessage() . "\n";
                }
            }
        } else {
            echo "  ✓ Index exists\n";
            $already_ok++;
        }
    }
    
    echo "\n════════════════════════════════════════════════\n";
    echo "VERIFICATION: Current Schema Status\n";
    echo "════════════════════════════════════════════════\n\n";
    
    // Verify critical tables
    echo "📋 medication_inventory columns:\n";
    $stmt = $pdo->query("DESCRIBE medication_inventory");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $marker = in_array($row['Field'], ['category_id', 'batch_number', 'expiry_date', 'stock_alert_limit']) ? '✓' : ' ';
        echo "   $marker {$row['Field']} ({$row['Type']})\n";
    }
    
    echo "\n📋 bhw_users columns:\n";
    $stmt = $pdo->query("DESCRIBE bhw_users");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $marker = in_array($row['Field'], ['email', 'role', 'account_status', 'last_login', 'avatar']) ? '✓' : ' ';
        echo "   $marker {$row['Field']} ({$row['Type']})\n";
    }
    
    echo "\n════════════════════════════════════════════════\n";
    echo "Summary\n";
    echo "════════════════════════════════════════════════\n";
    echo "  ✓ Fixed/Added:  $fixed\n";
    echo "  ✓ Already OK:   $already_ok\n";
    echo "  ✗ Errors:       $errors\n";
    echo "════════════════════════════════════════════════\n\n";
    
    if ($errors === 0) {
        echo "✅ Schema validation complete! Database is ready.\n\n";
        echo "You can now:\n";
        echo "  1. Refresh your inventory page - items should display\n";
        echo "  2. Test user role changes - should save properly\n";
        echo "  3. Run this script anytime to fix schema issues\n\n";
    } else {
        echo "⚠️ Some errors occurred. Check the output above.\n";
        echo "Most errors are safe to ignore if they're about existing structures.\n\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Connection failed: " . $e->getMessage() . "\n";
    echo "\nPlease check:\n";
    echo "  1. MySQL/XAMPP is running\n";
    echo "  2. Database '$db' exists\n";
    echo "  3. Username/password are correct\n";
    exit(1);
}
