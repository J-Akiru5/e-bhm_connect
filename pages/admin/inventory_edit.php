<?php
// pages/admin/inventory_edit.php
// Edit an inventory item
include_once __DIR__ . '/../../includes/header_admin.php';

if (!isset($_GET['id']) || trim($_GET['id']) === '') {
    header('Location: ' . BASE_URL . 'admin-inventory');
    exit();
}

$item_id = (int) $_GET['id'];

try {
    $stmt = $pdo->prepare('SELECT * FROM medication_inventory WHERE item_id = :id LIMIT 1');
    $stmt->execute([':id' => $item_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$item) {
        header('Location: ' . BASE_URL . 'admin-inventory');
        exit();
    }
} catch (Throwable $e) {
    error_log('Inventory edit load error: ' . $e->getMessage());
    header('Location: ' . BASE_URL . 'admin-inventory');
    exit();
}

// Load categories
try {
    $catsStmt = $pdo->query("SELECT category_id, category_name FROM inventory_categories ORDER BY category_name ASC");
    $categories = $catsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $categories = [];
}
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Edit Inventory Item</h1>
        <a href="<?php echo BASE_URL; ?>admin-inventory" class="btn btn-secondary">Back to Inventory</a>
    </div>

    <div class="card">
        <div class="card-header">Edit Inventory Item</div>
        <div class="card-body">
            <form method="post" action="?action=update-inventory-item">
                <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id']); ?>">

                <div class="mb-3">
                    <label class="form-label">Item Name</label>
                    <input type="text" name="item_name" class="form-control" value="<?php echo htmlspecialchars($item['item_name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($item['description']); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">-- Select category --</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?php echo $c['category_id']; ?>" <?php echo (isset($item['category_id']) && $item['category_id'] == $c['category_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['category_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Batch Number</label>
                        <input type="text" name="batch_number" class="form-control" value="<?php echo htmlspecialchars($item['batch_number'] ?? ''); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control" value="<?php echo htmlspecialchars($item['expiry_date'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stock Alert Limit</label>
                        <input type="number" name="stock_alert_limit" class="form-control" min="0" step="1" value="<?php echo htmlspecialchars($item['stock_alert_limit'] ?? 10); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Quantity In Stock</label>
                        <input type="number" name="quantity_in_stock" class="form-control" min="0" step="1" value="<?php echo htmlspecialchars($item['quantity_in_stock']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Unit</label>
                        <input type="text" name="unit" class="form-control" value="<?php echo htmlspecialchars($item['unit']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Last Restock</label>
                        <input type="date" name="last_restock" class="form-control" value="<?php echo htmlspecialchars($item['last_restock']); ?>">
                    </div>
                </div>

                <div class="d-grid mt-3">
                    <button type="submit" class="btn btn-primary">Update Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
