<?php
// pages/admin/inventory_categories.php
include_once __DIR__ . '/../../includes/header_admin.php';

$categories = [];
try {
    $stmt = $pdo->query("SELECT category_id, category_name, created_at FROM inventory_categories ORDER BY category_name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Load inventory categories error: ' . $e->getMessage());
}
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4">Inventory Categories</h2>
        <a href="<?php echo BASE_URL; ?>admin-inventory" class="btn btn-outline-secondary">Back to Inventory</a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">Add Category</div>
                <div class="card-body">
                    <form method="post" action="?action=save-inventory-category">
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="category_name" class="form-control" required>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-primary">Save Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Existing Categories</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr><th>Name</th><th>Created</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categories)): ?>
                                    <tr><td colspan="3">No categories yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($cat['category_name']); ?></td>
                                            <td><?php echo htmlspecialchars($cat['created_at'] ?? ''); ?></td>
                                            <td>
                                                <form method="post" action="?action=delete-inventory-category" class="d-inline" onsubmit="return confirm('Delete category? Items will be uncategorized.');">
                                                    <input type="hidden" name="category_id" value="<?php echo (int)$cat['category_id']; ?>">
                                                    <button class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php';
