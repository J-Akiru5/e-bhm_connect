<?php
/**
 * Migration Runner - E-BHM Connect
 * Run all pending SQL migrations and seeders
 * 
 * USAGE: php run_migrations.php
 * 
 * SAFE TO RE-RUN: All migrations and seeders use IF NOT EXISTS
 * and INSERT IGNORE to prevent duplicate errors.
 */

// Database configuration - UPDATE THESE FOR YOUR ENVIRONMENT
$host = 'localhost';
$db = 'e-bhw_connect';
$user = 'root';
$pass = '';

echo "==============================================\n";
echo "  E-BHM Connect Migration Runner\n";
echo "==============================================\n\n";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connected to database '$db' successfully.\n\n";
    
    // Run each migration in order (dependencies first)
    $migrations = [
        // Phase 1: Schema migrations (create/alter tables)
        'migrations/2025-12-08_add_inventory_categories_and_fields.sql',
        'migrations/2025-12-15_add_sms_queue_and_fix_chatbot.sql',
        'migrations/2025-12-16_admin_overhaul_tables.sql',
        'migrations/2025-12-16_bhw_account_verification.sql',
        'migrations/2025-12-17_health_records_dashboard.sql',
        'migrations/2025-12-17_fix_missing_columns.sql',
        'migrations/2025-12-17_rate_limits_table.sql',
        'migrations/2025-12-19_create_audit_logs.sql',
        'migrations/2025-12-19_fix_dispensing_log.sql',
        'migrations/2025-12-19_patient_portal_access.sql',
        'migrations/2026-01-06_remove_email_verification.sql',
        'migrations/2026-01-06_fix_sms_queue_columns.sql',
        'migrations/2026-01-07_add_last_login_column.sql',
        
        // Phase 2: Seeders (insert sample data)
        'migrations/2025-12-16_bhw_superadmin_seeder.sql',
        'migrations/2025-12-17_health_records_seeder.sql',
    ];
    
    $success = 0;
    $skipped = 0;
    $errors = 0;
    
    foreach ($migrations as $migration) {
        if (!file_exists($migration)) {
            echo "⊘ SKIP: $migration (file not found)\n";
            $skipped++;
            continue;
        }
        
        echo "► Running: $migration\n";
        $sql = file_get_contents($migration);
        
        try {
            // Handle DELIMITER for stored procedures
            if (strpos($sql, 'DELIMITER') !== false) {
                // This migration uses stored procedures, run via MySQL client instead
                echo "  ℹ Note: This migration uses stored procedures.\n";
                echo "  Please run manually via phpMyAdmin or mysql CLI.\n";
                $skipped++;
                continue;
            }
            
            $pdo->exec($sql);
            echo "  ✓ SUCCESS\n";
            $success++;
        } catch (PDOException $e) {
            // Check if error is just about table/column already existing
            $msg = $e->getMessage();
            if (strpos($msg, 'already exists') !== false || 
                strpos($msg, 'Duplicate') !== false ||
                strpos($msg, 'UNIQUE constraint') !== false) {
                echo "  ⊘ SKIP: Already applied\n";
                $skipped++;
            } else {
                echo "  ✗ ERROR: " . $msg . "\n";
                $errors++;
            }
        }
    }
    
    echo "\n==============================================\n";
    echo "  Migration Summary\n";
    echo "==============================================\n";
    echo "  ✓ Success: $success\n";
    echo "  ⊘ Skipped: $skipped\n";
    echo "  ✗ Errors:  $errors\n";
    echo "==============================================\n\n";
    
    if ($errors === 0) {
        echo "✓ All migrations completed successfully!\n\n";
        echo "You can now login with:\n";
        echo "  Username: superadmin\n";
        echo "  Password: SuperAdmin@2025\n";
    } else {
        echo "⚠ Some migrations had errors. Check the output above.\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Connection failed: " . $e->getMessage() . "\n";
    echo "\nPlease check:\n";
    echo "  1. MySQL is running (XAMPP Control Panel)\n";
    echo "  2. Database '$db' exists\n";
    echo "  3. Username/password are correct\n";
    exit(1);
}
