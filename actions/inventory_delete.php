<?php
// actions/inventory_delete.php
// Handle deletion of inventory items
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/security_helper.php';
require_once __DIR__ . '/../includes/auth_helpers.php';

// Router bootstraps session and $pdo and BASE_URL
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-inventory');
    exit();
}

// Validate CSRF token
require_csrf();

if (!isset($_POST['item_id']) || trim($_POST['item_id']) === '') {
    $_SESSION['form_error'] = 'No inventory item specified for deletion.';
    header('Location: ' . BASE_URL . 'admin-inventory');
    exit();
}

$item_id = (int) $_POST['item_id'];

try {
    $stmt = $pdo->prepare('DELETE FROM medication_inventory WHERE item_id = :id');
    $stmt->execute([':id' => $item_id]);
    log_audit('delete_inventory', 'inventory', $item_id);

    $_SESSION['form_success'] = 'Item deleted from inventory.';
} catch (Throwable $e) {
    error_log('Inventory delete error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'An error occurred.';
}

header('Location: ' . BASE_URL . 'admin-inventory');
exit();
