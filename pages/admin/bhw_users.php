<?php
// pages/admin/bhw_users.php
// BHW User Management list
include_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../includes/pagination_helper.php';

$bhw_users = [];
$pagination = ['current_page' => 1, 'total_pages' => 1, 'total_records' => 0];
$per_page = 10;

try {
    // Count total records first
    $count_sql = 'SELECT COUNT(*) FROM bhw_users';
    $params = [];

    if (!empty($_GET['search'])) {
        $search_term = '%' . $_GET['search'] . '%';
        $count_sql .= ' WHERE full_name LIKE ?';
        $params[] = $search_term;
    }
    
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_records = (int) $count_stmt->fetchColumn();

    // Calculate pagination
    $current_page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $pagination = paginate($total_records, $per_page, $current_page);

    // Base SQL
    $sql = 'SELECT bhw_id, full_name, username, bhw_unique_id, assigned_area FROM bhw_users';
    $params = [];

    if (!empty($_GET['search'])) {
        $search_term = '%' . $_GET['search'] . '%';
        $sql .= ' WHERE full_name LIKE ?';
        $params[] = $search_term;
    }

    $sql .= ' ORDER BY full_name ASC LIMIT ' . $pagination['per_page'] . ' OFFSET ' . $pagination['offset'];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $bhw_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('BHW users fetch error: ' . $e->getMessage());
}
?>

<div class="container">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Search & Filter</h5>
            <form method="GET" action="">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Search by BHW name..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    <button class="btn btn-primary" type="submit">Search</button>
                    <a href="<?php echo BASE_URL; ?>admin-bhw-users" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>BHW User Management</h1>
        <a href="<?php echo BASE_URL; ?>register-bhw" class="btn btn-success mb-3">Add New BHW</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>BHW ID</th>
                            <th>Assigned Area</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bhw_users)): ?>
                            <tr><td colspan="5">No BHW users found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($bhw_users as $bhw): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($bhw['full_name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($bhw['username'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($bhw['bhw_unique_id'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($bhw['assigned_area'] ?? ''); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>admin-bhw-edit?id=<?php echo $bhw['bhw_id']; ?>" class="btn btn-secondary btn-sm">Edit</a>

                                        <a href="<?php echo BASE_URL; ?>?action=report-bhw-record&id=<?php echo $bhw['bhw_id']; ?>" class="btn btn-info btn-sm" target="_blank">PDF</a>

                                        <form action="<?php echo BASE_URL; ?>?action=delete-bhw" method="POST" class="d-inline" onsubmit="return confirmDelete(event);">
                                            <input type="hidden" name="bhw_id" value="<?php echo htmlspecialchars($bhw['bhw_id'] ?? ''); ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
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

    <?php
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

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
