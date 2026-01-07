<?php
// actions/inventory_category_save.php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/security_helper.php';
require_once __DIR__ . '/../includes/auth_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-inventory-categories');
    exit();
}

// Validate CSRF token
require_csrf();

$name = isset($_POST['category_name']) ? trim($_POST['category_name']) : '';
if ($name === '') {
    $_SESSION['form_error'] = 'Category name is required.';
    header('Location: ' . BASE_URL . 'admin-inventory-categories');
    exit();
}

try {
    $stmt = $pdo->prepare('INSERT INTO inventory_categories (category_name, created_at) VALUES (:name, NOW())');
    $stmt->execute([':name' => $name]);
    $newId = $pdo->lastInsertId();
    log_audit('create_category', 'inventory_category', (int)$newId, ['category_name' => $name]);
    $_SESSION['form_success'] = 'Category created.';
} catch (Throwable $e) {
    error_log('Category save error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'Unable to create category.';
}

header('Location: ' . BASE_URL . 'admin-inventory-categories');
exit();
