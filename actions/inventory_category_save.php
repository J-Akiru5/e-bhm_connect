<?php
// actions/inventory_category_save.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-inventory-categories');
    exit();
}

$name = isset($_POST['category_name']) ? trim($_POST['category_name']) : '';
if ($name === '') {
    $_SESSION['form_error'] = 'Category name is required.';
    header('Location: ' . BASE_URL . 'admin-inventory-categories');
    exit();
}

try {
    $stmt = $pdo->prepare('INSERT INTO inventory_categories (category_name, created_at) VALUES (:name, NOW())');
    $stmt->execute([':name' => $name]);
    $_SESSION['form_success'] = 'Category created.';
} catch (Throwable $e) {
    error_log('Category save error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'Unable to create category.';
}

header('Location: ' . BASE_URL . 'admin-inventory-categories');
exit();
