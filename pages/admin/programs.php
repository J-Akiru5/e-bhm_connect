<?php
// pages/admin/programs.php
include_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';

$programs = [];
try {
    $stmt = $pdo->query('SELECT * FROM health_programs ORDER BY start_date DESC');
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Programs fetch error: ' . $e->getMessage());
}
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Health Program Monitoring</h1>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Add New Health Program</div>
                <div class="card-body">
                    <form method="post" action="<?php echo BASE_URL; ?>?action=save-program">
                        <div class="mb-3">
                            <label class="form-label">Program Name</label>
                            <input type="text" name="program_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <input type="text" name="status" class="form-control" placeholder="Active, Completed">
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-primary" type="submit">Save Program</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Current & Past Programs</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Program Name</th>
                                    <th>Description</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($programs)): ?>
                                    <tr><td colspan="6">No programs found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($programs as $p): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($p['program_name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($p['description'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($p['start_date'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($p['end_date'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($p['status'] ?? ''); ?></td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>admin-program-edit?id=<?php echo $p['program_id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                                <form action="<?php echo BASE_URL; ?>?action=delete-program" method="POST" class="d-inline" onsubmit="return confirmDelete(event);">
                                                    <input type="hidden" name="program_id" value="<?php echo htmlspecialchars($p['program_id'] ?? ''); ?>">
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
