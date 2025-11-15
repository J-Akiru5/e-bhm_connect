<?php
// Quick DB connection test. Browse to /config/db_test.php
require_once __DIR__ . '/config.php';

try {
    $pdo = get_db();
    // Using a simple query to verify connection and privileges
    $stmt = $pdo->query('SELECT DATABASE() as db');
    $row = $stmt->fetch();
    header('Content-Type: text/plain');
    echo "Connected to database: " . ($row['db'] ?? 'unknown') . "\n";
    echo "PDO driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";
} catch (Throwable $e) {
    header('Content-Type: text/plain');
    echo "Connection failed: " . $e->getMessage();
}
