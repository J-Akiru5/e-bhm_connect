<?php
// actions/inventory_category_delete.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-inventory-categories');
    exit();
}

$id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
if ($id <= 0) {
    $_SESSION['form_error'] = 'Invalid category id.';
    header('Location: ' . BASE_URL . 'admin-inventory-categories');
    exit();
}

try {
    // Unset category on items first (safe fallback if FK not present)
    $u = $pdo->prepare('UPDATE medication_inventory SET category_id = NULL WHERE category_id = :id');
    $u->execute([':id' => $id]);

    $d = $pdo->prepare('DELETE FROM inventory_categories WHERE category_id = :id');
    $d->execute([':id' => $id]);

    $_SESSION['form_success'] = 'Category deleted.';
} catch (Throwable $e) {
    error_log('Category delete error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'Unable to delete category.';
}

header('Location: ' . BASE_URL . 'admin-inventory-categories');
exit();
