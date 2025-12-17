<?php
/**
 * Dashboard Test Script
 */

require 'config/database.php';
require 'includes/admin_analytics_helper.php';

echo "Testing dashboard data fetch...\n\n";

try {
    $data = fetch_dashboard_data($pdo);
    echo "✓ Dashboard data fetched successfully!\n\n";
    echo "Data structure:\n";
    print_r(array_keys($data));
    echo "\n\nFull data:\n";
    print_r($data);
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
