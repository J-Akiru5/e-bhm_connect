<?php
// Check bhw_users table structure
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/plain');
echo "bhw_users table structure:\n";
echo "==========================\n\n";

$stmt = $pdo->query('DESCRIBE bhw_users');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Field: {$row['Field']} | Type: {$row['Type']} | Null: {$row['Null']} | Default: " . ($row['Default'] ?? 'NULL') . "\n";
}

echo "\n\n";
echo "Attempting to update user ID 2 (Juan Dela Cruz) to 'admin'...\n";
try {
    $stmt = $pdo->prepare("UPDATE bhw_users SET role = 'admin' WHERE bhw_id = 2");
    $result = $stmt->execute();
    echo "Execute result: " . ($result ? 'true' : 'false') . "\n";
    echo "Rows affected: " . $stmt->rowCount() . "\n";
    
    // Check if update worked
    $check = $pdo->query("SELECT bhw_id, full_name, role FROM bhw_users WHERE bhw_id = 2");
    $user = $check->fetch(PDO::FETCH_ASSOC);
    echo "After update - Role: " . ($user['role'] ?? 'NULL/EMPTY') . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
