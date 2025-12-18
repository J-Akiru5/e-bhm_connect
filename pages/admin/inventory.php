<?php
// Medication & Supply Inventory (Admin)
// Auth enforced by router; header/footer provide layout and SweetAlert
include_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../includes/pagination_helper.php';
?>

<?php
$inventory_items = [];
$pagination = ['current_page' => 1, 'total_pages' => 1, 'total_records' => 0];
$per_page = 10;

// Stats
$total_items = 0;
$low_stock = 0;
$out_of_stock = 0;
$categories_count = 0;

try {
    // Load categories for dropdown
    $catsStmt = $pdo->query("SELECT category_id, category_name FROM inventory_categories ORDER BY category_name ASC");
    $categories = $catsStmt->fetchAll(PDO::FETCH_ASSOC);
    $categories_count = count($categories);

    // Get stats
    $statsStmt = $pdo->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN quantity_in_stock <= stock_alert_limit AND quantity_in_stock > 0 THEN 1 ELSE 0 END) as low_stock,
        SUM(CASE WHEN quantity_in_stock = 0 THEN 1 ELSE 0 END) as out_of_stock
        FROM medication_inventory");
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    $total_items = (int) $stats['total'];
    $low_stock = (int) $stats['low_stock'];
    $out_of_stock = (int) $stats['out_of_stock'];

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
    $current_page = isset($_GET['pg']) ? max(1, (int) $_GET['pg']) : 1;
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
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Inventory Management</h1>
            <p class="page-subtitle">Manage medication and supplies stock</p>
        </div>
        <?php if (has_permission('manage_inventory')): ?>
        <button class="btn-primary-glass" onclick="openModal()">
            <i class="fas fa-plus"></i>
            Add New Item
        </button>
        <?php endif; ?>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <!-- ... stats ... -->
        <div class="glass-card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-value"><?php echo $total_items; ?></div>
            <div class="stat-label">Total Items</div>
        </div>
        <div class="glass-card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-value"><?php echo $low_stock; ?></div>
            <div class="stat-label">Low Stock</div>
        </div>
        <div class="glass-card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-value"><?php echo $out_of_stock; ?></div>
            <div class="stat-label">Out of Stock</div>
        </div>
        <div class="glass-card stat-card">
            <div class="stat-icon info">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stat-value"><?php echo $categories_count; ?></div>
            <div class="stat-label">Categories</div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="glass-card filter-bar">
        <!-- ... form ... -->
        <form method="GET" action="">
            <div class="filter-row">
                <input type="text" name="search" class="glass-input" style="flex: 1;" placeholder="Search items by name..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <select name="category" class="glass-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?php echo $c['category_id']; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $c['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-primary-glass">
                    <i class="fas fa-search"></i>
                    Search
                </button>
                <a href="<?php echo BASE_URL; ?>admin-inventory" class="btn-secondary-glass">
                    <i class="fas fa-times"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Inventory Table -->
    <div class="glass-card table-container">
        <div class="table-responsive">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Description</th>
                        <th>Stock</th>
                        <th>Unit</th>
                        <th>Batch</th>
                        <th>Expiry</th>
                        <?php if (has_permission('manage_inventory')): ?>
                        <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inventory_items)): ?>
                        <tr>
                            <td colspan="<?php echo has_permission('manage_inventory') ? '7' : '6'; ?>">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                    <p>No inventory items found</p>
                                    <?php if (has_permission('manage_inventory')): ?>
                                    <button class="btn-primary-glass" onclick="openModal()">Add First Item</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($inventory_items as $item): ?>
                            <?php
                                $q = (int)($item['quantity_in_stock'] ?? 0);
                                $alert = (int)($item['stock_alert_limit'] ?? 10);
                                if ($q <= 0) { $badgeClass = 'danger'; }
                                elseif ($q <= $alert) { $badgeClass = 'warning'; }
                                else { $badgeClass = 'success'; }
                            ?>
                            <tr>
                                <td data-label="Item">
                                    <div class="item-cell">
                                        <span class="item-name"><?php echo htmlspecialchars($item['item_name'] ?? ''); ?></span>
                                        <span class="item-category"><?php echo htmlspecialchars($item['category_name'] ?? 'Uncategorized'); ?></span>
                                    </div>
                                </td>
                                <td data-label="Description"><?php echo htmlspecialchars($item['description'] ?? '-'); ?></td>
                                <td data-label="Stock">
                                    <span class="stock-badge <?php echo $badgeClass; ?>">
                                        <?php echo $q; ?>
                                        <?php if ($q <= 0): ?>
                                            <i class="fas fa-exclamation-circle ms-1"></i>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td data-label="Unit"><?php echo htmlspecialchars($item['unit'] ?? '-'); ?></td>
                                <td data-label="Batch"><?php echo htmlspecialchars($item['batch_number'] ?? '-'); ?></td>
                                <td data-label="Expiry"><?php echo htmlspecialchars($item['expiry_date'] ?? '-'); ?></td>
                                <?php if (has_permission('manage_inventory')): ?>
                                <td data-label="Actions">
                                    <div class="actions-cell">
                                        <a href="<?php echo BASE_URL; ?>admin-inventory-edit?id=<?php echo $item['item_id']; ?>" class="btn-secondary-glass btn-sm-glass">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo BASE_URL; ?>?action=delete-inventory-item" method="POST" class="d-inline" onsubmit="return confirmDelete(event);">
                                            <?php echo csrf_input(); ?>
                                            <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id'] ?? ''); ?>">
                                            <button type="submit" class="btn-danger-glass btn-sm-glass">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="pagination-container">
            <?php echo render_pagination($pagination, get_pagination_base_url()); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Item Modal -->
<div class="modal-overlay" id="addItemModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-plus-circle me-2" style="color: #20c997;"></i>
                Add New Inventory Item
            </h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="post" action="?action=save-inventory-item">
            <div class="modal-body">
                <?php echo csrf_input(); ?>
                <div class="form-group">
                    <label class="form-label">Item Name *</label>
                    <input type="text" name="item_name" class="glass-input" required placeholder="Enter item name">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="glass-input" rows="3" placeholder="Enter item description"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Quantity In Stock</label>
                        <input type="number" name="quantity_in_stock" class="glass-input" min="0" step="1" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Unit</label>
                        <input type="text" name="unit" class="glass-input" placeholder="e.g., boxes, bottles">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="glass-input">
                            <option value="">-- Select category --</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?php echo $c['category_id']; ?>"><?php echo htmlspecialchars($c['category_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Batch Number</label>
                        <input type="text" name="batch_number" class="glass-input" placeholder="Enter batch number">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="expiry_date" class="glass-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stock Alert Limit</label>
                        <input type="number" name="stock_alert_limit" class="glass-input" min="0" step="1" value="10">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Last Restock Date</label>
                    <input type="date" name="last_restock" class="glass-input">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary-glass" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-primary-glass">
                    <i class="fas fa-save"></i>
                    Save Item
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Modal Functions
function openModal() {
    document.getElementById('addItemModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('addItemModal').classList.remove('active');
    document.body.style.overflow = '';
}

// Close modal on outside click
document.getElementById('addItemModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Delete confirmation
function confirmDelete(event) {
    event.preventDefault();
    Swal.fire({
        title: 'Delete Item?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
        background: 'rgba(30, 41, 59, 0.95)',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.submit();
        }
    });
    return false;
}
</script>

<?php
// Flash messages via SweetAlert2
if (isset($_SESSION['form_success'])) {
    $msg = json_encode($_SESSION['form_success']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'success', title: 'Success', text: $msg, background: 'rgba(30, 41, 59, 0.95)', color: '#ffffff'}); } });</script>";
    unset($_SESSION['form_success']);
}
if (isset($_SESSION['form_error'])) {
    $emsg = json_encode($_SESSION['form_error']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'error', title: 'Error', text: $emsg, background: 'rgba(30, 41, 59, 0.95)', color: '#ffffff'}); } });</script>";
    unset($_SESSION['form_error']);
}

include_once __DIR__ . '/../../includes/footer_admin.php';
?>
