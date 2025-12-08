<?php
// actions/inventory_update.php
// Handle update of inventory items

// Router bootstraps session and $pdo and BASE_URL
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-inventory');
    exit();
}

$item_id = isset($_POST['item_id']) ? trim($_POST['item_id']) : '';
$item_name = isset($_POST['item_name']) ? trim($_POST['item_name']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$quantity_in_stock = isset($_POST['quantity_in_stock']) ? trim($_POST['quantity_in_stock']) : '';
$unit = isset($_POST['unit']) ? trim($_POST['unit']) : '';
$last_restock = isset($_POST['last_restock']) && $_POST['last_restock'] !== '' ? trim($_POST['last_restock']) : null;

if ($item_id === '' || $item_name === '' || $quantity_in_stock === '') {
    $_SESSION['form_error'] = 'Item name and quantity are required.';
    // Redirect back to edit page if possible
    $redirectId = $item_id !== '' ? (int)$item_id : '';
    if ($redirectId !== '') {
        header('Location: ' . BASE_URL . 'admin-inventory-edit?id=' . $redirectId);
    } else {
        header('Location: ' . BASE_URL . 'admin-inventory');
    }
    exit();
}

$item_id = (int)$item_id;

try {
    // Build dynamic SET list depending on which columns exist (keeps backward compatibility)
    $candidateCols = ['item_name','description','quantity_in_stock','unit','last_restock','category_id','category','batch_number','expiry_date','stock_alert_limit'];
    $setParts = [];
    $params = [':item_id' => $item_id];
    foreach ($candidateCols as $col) {
        $stmtC = $pdo->prepare("SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'medication_inventory' AND column_name = :c LIMIT 1");
        $stmtC->execute([':c' => $col]);
        if ($stmtC->fetchColumn()) {
            switch ($col) {
                case 'item_name': $setParts[] = "item_name = :item_name"; $params[':item_name'] = $item_name; break;
                case 'description': $setParts[] = "description = :description"; $params[':description'] = $description; break;
                case 'quantity_in_stock': $setParts[] = "quantity_in_stock = :quantity_in_stock"; $params[':quantity_in_stock'] = $quantity_in_stock; break;
                case 'unit': $setParts[] = "unit = :unit"; $params[':unit'] = $unit; break;
                case 'last_restock': $setParts[] = "last_restock = :last_restock"; $params[':last_restock'] = $last_restock; break;
                case 'category_id':
                    $setParts[] = "category_id = :category_id";
                    $params[':category_id'] = isset($_POST['category_id']) && $_POST['category_id'] !== '' ? (int)$_POST['category_id'] : null;
                    break;
                case 'category':
                    $setParts[] = "category = :category";
                    $params[':category'] = isset($_POST['category']) ? trim($_POST['category']) : null;
                    break;
                case 'batch_number':
                    $setParts[] = "batch_number = :batch_number"; $params[':batch_number'] = isset($_POST['batch_number']) ? trim($_POST['batch_number']) : null; break;
                case 'expiry_date':
                    $ed = isset($_POST['expiry_date']) && $_POST['expiry_date'] !== '' ? trim($_POST['expiry_date']) : null;
                    if ($ed) { try { $d = new DateTime($ed); $ed = $d->format('Y-m-d'); } catch (Throwable $e) { $ed = null; } }
                    $setParts[] = "expiry_date = :expiry_date"; $params[':expiry_date'] = $ed; break;
                case 'stock_alert_limit':
                    $setParts[] = "stock_alert_limit = :stock_alert_limit"; $params[':stock_alert_limit'] = isset($_POST['stock_alert_limit']) && $_POST['stock_alert_limit'] !== '' ? (int)$_POST['stock_alert_limit'] : 10; break;
            }
        }
    }

    if (empty($setParts)) {
        throw new RuntimeException('No updatable columns found on medication_inventory.');
    }

    $sql = 'UPDATE medication_inventory SET ' . implode(', ', $setParts) . ' WHERE item_id = :item_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $_SESSION['form_success'] = 'Inventory item updated.';
} catch (Throwable $e) {
    error_log('Inventory update error: ' . $e->getMessage());
    if (defined('APP_ENV') && APP_ENV === 'development') {
        $_SESSION['form_error'] = 'An error occurred: ' . $e->getMessage();
    } else {
        $_SESSION['form_error'] = 'An error occurred.';
    }
}

header('Location: ' . BASE_URL . 'admin-inventory');
exit();
