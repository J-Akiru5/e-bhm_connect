<?php
// Medication & Supply Inventory (Admin)
// Auth enforced by router; header/footer provide layout and SweetAlert
include_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../includes/pagination_helper.php';

/* Visual updates: Teal theme, rounded cards, hover lift and badges */
?>
<style>
    :root{ --brand-teal: #B2A08F; }
    .card { border-radius:12px; }
    .stat-card { transition: transform .18s ease, box-shadow .18s ease; }
    .stat-card:hover { transform: translateY(-6px); box-shadow: 0 14px 40px rgba(16,24,32,0.06); }
    .stat-bubble { width:48px; height:48px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:var(--brand-teal); color:#fff; }
    .table thead th { color:#495057; font-weight:600; }
    .badge-low { background:#ffc107; color:#000; }
    .badge-out { background:#dc3545; }
    .inventory-actions .btn { min-width:86px; }
    @media (max-width:575px){ .stat-bubble{width:42px;height:42px;} }
</style>
<?php

$inventory_items = [];
$pagination = ['current_page' => 1, 'total_pages' => 1, 'total_records' => 0];
$per_page = 10;

try {
    // Load categories for dropdown
    $catsStmt = $pdo->query("SELECT category_id, category_name FROM inventory_categories ORDER BY category_name ASC");
    $categories = $catsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Count total records first
    $count_sql = "SELECT COUNT(*) FROM medication_inventory mi";
    $params = [];
    $where = [];
    
    if (!empty($_GET['search'])) {
        $where[] = "mi.item_name LIKE ?";
        $params[] = '%' . $_GET['search'] . '%';
    }
    if (!empty($_GET['category']) && is_numeric($_GET['category'])) {
        $where[] = "mi.category_id = ?";
        $params[] = (int)$_GET['category'];
    }
    if (!empty($where)) {
        $count_sql .= ' WHERE ' . implode(' AND ', $where);
    }
    
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_records = (int) $count_stmt->fetchColumn();

    // Calculate pagination
    $current_page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $pagination = paginate($total_records, $per_page, $current_page);

    // Base SQL query with optional category name
    $sql = "SELECT mi.*, ic.category_name FROM medication_inventory mi LEFT JOIN inventory_categories ic ON mi.category_id = ic.category_id";
    $params = [];

    $where = [];
    if (!empty($_GET['search'])) {
        $where[] = "mi.item_name LIKE ?";
        $params[] = '%' . $_GET['search'] . '%';
    }
    if (!empty($_GET['category']) && is_numeric($_GET['category'])) {
        $where[] = "mi.category_id = ?";
        $params[] = (int)$_GET['category'];
    }
    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= " ORDER BY mi.item_name ASC LIMIT " . $pagination['per_page'] . " OFFSET " . $pagination['offset'];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $inventory_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Inventory fetch error: ' . $e->getMessage());
    $categories = [];
}
?>

<div class="container">
    <div class="card mb-3 stat-card">
        <div class="card-body">
            <h5 class="card-title">Search & Filter</h5>
            <form method="GET" action="">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Search by item name..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    <button class="btn btn-primary" type="submit">Search</button>
                    <select name="category" class="form-select ms-2" style="max-width:220px;">
                        <option value="">All categories</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?php echo $c['category_id']; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $c['category_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['category_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <a href="<?php echo BASE_URL; ?>admin-inventory" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4">Medication & Supply Inventory</h1>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card mb-3 stat-card">
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
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">-- Select category --</option>
                                    <?php foreach ($categories as $c): ?>
                                        <option value="<?php echo $c['category_id']; ?>"><?php echo htmlspecialchars($c['category_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Batch Number</label>
                                <input type="text" name="batch_number" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Expiry Date</label>
                                <input type="date" name="expiry_date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stock Alert Limit</label>
                                <input type="number" name="stock_alert_limit" class="form-control" min="0" step="1" value="10">
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
            <div class="card mb-3 stat-card">
                <div class="card-header">Current Stock</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Description</th>
                                    <th style="width:120px;">Quantity</th>
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
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2 text-muted small"><?php echo htmlspecialchars($item['category_name'] ?? ''); ?></div>
                                                        <div><?php echo htmlspecialchars($item['item_name'] ?? ''); ?></div>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($item['description'] ?? ''); ?></td>
                                                <td>
                                                    <?php
                                                        $q = (int)($item['quantity_in_stock'] ?? 0);
                                                        if ($q <= 0) { $b = 'badge badge-out'; }
                                                        elseif ($q <= 5) { $b = 'badge badge-low'; }
                                                        else { $b = 'badge bg-success'; }
                                                    ?>
                                                    <span class="<?php echo $b; ?>"><?php echo htmlspecialchars($q); ?></span>
                                                </td>
                                                <td><?php echo htmlspecialchars($item['unit'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($item['last_restock'] ?? ''); ?></td>
                                                <td>
                                                <div class="inventory-actions">
                                                    <a href="<?php echo BASE_URL; ?>admin-inventory-edit?id=<?php echo $item['item_id']; ?>" class="btn btn-outline-secondary btn-sm">Edit</a>
                                                    <form action="<?php echo BASE_URL; ?>?action=delete-inventory-item" method="POST" class="d-inline" onsubmit="return confirmDelete(event);">
                                                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id'] ?? ''); ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <?php echo render_pagination($pagination, get_pagination_base_url()); ?>
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
