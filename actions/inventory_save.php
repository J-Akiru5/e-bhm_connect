<?php
// actions/inventory_save.php
// Handle saving new inventory items

// Router bootstraps session and $pdo and BASE_URL
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-inventory');
    exit();
}

$item_name = isset($_POST['item_name']) ? trim($_POST['item_name']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$quantity_in_stock = isset($_POST['quantity_in_stock']) ? trim($_POST['quantity_in_stock']) : '';
$unit = isset($_POST['unit']) ? trim($_POST['unit']) : '';
$last_restock = isset($_POST['last_restock']) && $_POST['last_restock'] !== '' ? trim($_POST['last_restock']) : null;

if ($item_name === '' || $quantity_in_stock === '') {
    $_SESSION['form_error'] = 'Item name and quantity are required.';
    header('Location: ' . BASE_URL . 'admin-inventory');
    exit();
}

try {
    // Include category, batch_number, expiry_date, stock_alert_limit when available
    $cols = ['item_name','description','quantity_in_stock','unit','last_restock','category','batch_number','expiry_date','stock_alert_limit'];
    $available = array_filter($cols, function($c) use($pdo) { 
        $stmt = $pdo->prepare("SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'medication_inventory' AND column_name = :c LIMIT 1");
        $stmt->execute([':c' => $c]);
        return (bool)$stmt->fetchColumn();
    });

    $colList = implode(', ', $available);
    $placeholders = implode(', ', array_map(function($c){ return ':' . $c; }, $available));
    $sql = "INSERT INTO medication_inventory ({$colList}) VALUES ({$placeholders})";
    $stmt = $pdo->prepare($sql);

    $params = [];
    foreach ($available as $col) {
        switch ($col) {
            case 'item_name': $params[':item_name'] = $item_name; break;
            case 'description': $params[':description'] = $description; break;
            case 'quantity_in_stock': $params[':quantity_in_stock'] = $quantity_in_stock; break;
            case 'unit': $params[':unit'] = $unit; break;
            case 'last_restock': $params[':last_restock'] = $last_restock; break;
            case 'category': $params[':category'] = isset($_POST['category']) ? trim($_POST['category']) : null; break;
            case 'batch_number': $params[':batch_number'] = isset($_POST['batch_number']) ? trim($_POST['batch_number']) : null; break;
            case 'expiry_date': $params[':expiry_date'] = isset($_POST['expiry_date']) && $_POST['expiry_date'] !== '' ? trim($_POST['expiry_date']) : null; break;
            case 'stock_alert_limit': $params[':stock_alert_limit'] = isset($_POST['stock_alert_limit']) && $_POST['stock_alert_limit'] !== '' ? (int)$_POST['stock_alert_limit'] : 10; break;
        }
    }

    $stmt->execute($params);

    $_SESSION['form_success'] = 'Item added to inventory.';
} catch (Throwable $e) {
    error_log('Inventory save error: ' . $e->getMessage());
    if (defined('APP_ENV') && APP_ENV === 'development') {
        $_SESSION['form_error'] = 'An error occurred: ' . $e->getMessage();
    } else {
        $_SESSION['form_error'] = 'An error occurred.';
    }
}

header('Location: ' . BASE_URL . 'admin-inventory');
exit();
