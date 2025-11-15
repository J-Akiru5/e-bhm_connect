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
    $stmt = $pdo->prepare('INSERT INTO medication_inventory (item_name, description, quantity_in_stock, unit, last_restock) VALUES (:item_name, :description, :quantity_in_stock, :unit, :last_restock)');
    $stmt->execute([
        ':item_name' => $item_name,
        ':description' => $description,
        ':quantity_in_stock' => $quantity_in_stock,
        ':unit' => $unit,
        ':last_restock' => $last_restock
    ]);

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
