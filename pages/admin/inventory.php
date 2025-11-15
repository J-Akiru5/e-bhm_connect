<?php
// Medication & Supply Inventory (Admin)
// Auth enforced by router; header/footer provide layout and SweetAlert
include_once __DIR__ . '/../../includes/header_admin.php';

$inventory_items = [];
try {
    $stmt = $pdo->query('SELECT * FROM medication_inventory ORDER BY item_name ASC');
    $inventory_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Inventory fetch error: ' . $e->getMessage());
}
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Medication & Supply Inventory</h1>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card mb-3">
                <div class="card-header">Add New Item</div>
                <div class="card-body">
                    <form method="post" action="?action=save-inventory-item">
                        <div class="mb-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" name="item_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity In Stock</label>
                                <input type="number" name="quantity_in_stock" class="form-control" min="0" step="1" value="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit</label>
                                <input type="text" name="unit" class="form-control" placeholder="e.g., boxes, bottles">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Last Restock</label>
                            <input type="date" name="last_restock" class="form-control">
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-primary" type="submit">Save Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-header">Current Stock</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Last Restock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($inventory_items)): ?>
                                    <tr><td colspan="6">No inventory items found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($inventory_items as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['item_name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($item['description'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($item['quantity_in_stock'] ?? '0'); ?></td>
                                            <td><?php echo htmlspecialchars($item['unit'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($item['last_restock'] ?? ''); ?></td>
                                            <td>
                                                <a href="/e-bmw_connect/admin-inventory-edit?id=<?php echo urlencode($item['item_id'] ?? ''); ?>" class="btn btn-secondary btn-sm">Edit</a>

                                                <form action="?action=delete-inventory-item" method="POST" class="d-inline" onsubmit="return confirmDelete(event);">
                                                    <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id'] ?? ''); ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
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

    <?php
    // Flash messages via SweetAlert2
    if (isset($_SESSION['form_success'])) {
        $msg = json_encode($_SESSION['form_success']);
        echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'success', title: 'Success', text: $msg}); } });</script>";
        unset($_SESSION['form_success']);
    }
    if (isset($_SESSION['form_error'])) {
        $emsg = json_encode($_SESSION['form_error']);
        echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'error', title: 'Error', text: $emsg}); } });</script>";
        unset($_SESSION['form_error']);
    }
    ?>

</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php';
