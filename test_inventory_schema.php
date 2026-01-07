<?php
require_once __DIR__ . '/config/database.php';

echo "Testing Inventory Schema...\n\n";

// Check table structure
echo "medication_inventory structure:\n";
$stmt = $pdo->query("DESCRIBE medication_inventory");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "  - {$row['Field']} ({$row['Type']})\n";
}

echo "\nInventory item count: ";
$count = $pdo->query("SELECT COUNT(*) FROM medication_inventory")->fetchColumn();
echo "$count items\n\n";

if ($count > 0) {
    echo "Sample items:\n";
    $stmt = $pdo->query("SELECT item_id, item_name, category_id, quantity_in_stock, stock_alert_limit FROM medication_inventory LIMIT 3");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  ID: {$row['item_id']} | {$row['item_name']} | Stock: {$row['quantity_in_stock']} | Alert: {$row['stock_alert_limit']}\n";
    }
}

echo "\n✅ Schema test complete!\n";
