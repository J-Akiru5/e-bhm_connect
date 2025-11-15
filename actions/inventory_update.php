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
    $stmt = $pdo->prepare('UPDATE medication_inventory SET item_name = :item_name, description = :description, quantity_in_stock = :quantity_in_stock, unit = :unit, last_restock = :last_restock WHERE item_id = :item_id');
    $stmt->execute([
        ':item_name' => $item_name,
        ':description' => $description,
        ':quantity_in_stock' => $quantity_in_stock,
        ':unit' => $unit,
        ':last_restock' => $last_restock,
        ':item_id' => $item_id
    ]);

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
