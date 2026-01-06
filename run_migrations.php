<?php
/**
 * Migration Runner
 * Run all pending SQL migrations
 */

$host = 'localhost';
$db = 'e-bhw_connect';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database '$db' successfully.\n\n";
    
    // Run each migration
    $migrations = [
        'migrations/2025-12-08_add_inventory_categories_and_fields.sql',
        'migrations/2025-12-15_add_sms_queue_and_fix_chatbot.sql',
        'migrations/2025-12-16_admin_overhaul_tables.sql',
        'migrations/2025-12-16_bhw_account_verification.sql',
        'migrations/2025-12-16_bhw_superadmin_seeder.sql',
        'migrations/2025-12-17_health_records_dashboard.sql',
        'migrations/2025-12-17_health_records_seeder.sql',
        'migrations/2025-12-17_fix_missing_columns.sql',
        'migrations/2026-01-06_remove_email_verification.sql',
        'migrations/2026-01-06_fix_sms_queue_columns.sql'
    ];
    
    foreach ($migrations as $migration) {
        if (!file_exists($migration)) {
            echo "SKIP: $migration (file not found)\n";
            continue;
        }
        
        echo "Running: $migration\n";
        $sql = file_get_contents($migration);
        
        try {
            $pdo->exec($sql);
            echo "SUCCESS: $migration\n\n";
        } catch (PDOException $e) {
            // Check if error is just about table/column already existing
            if (strpos($e->getMessage(), 'already exists') !== false || 
                strpos($e->getMessage(), 'Duplicate') !== false) {
                echo "SKIP: $migration (already applied)\n\n";
            } else {
                echo "ERROR in $migration: " . $e->getMessage() . "\n\n";
            }
        }
    }
    
    echo "\n=== Migration complete ===\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
