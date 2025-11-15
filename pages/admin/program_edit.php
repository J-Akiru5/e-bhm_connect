<?php
// pages/admin/program_edit.php
include_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_GET['id']) || trim($_GET['id']) === '') {
    $_SESSION['form_error'] = 'No program ID provided.';
    header('Location: ' . BASE_URL . 'admin-programs');
    exit();
}

$program_id = (int) $_GET['id'];

try {
    $stmt = $pdo->prepare('SELECT * FROM health_programs WHERE program_id = :id LIMIT 1');
    $stmt->execute([':id' => $program_id]);
    $program = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$program) {
        $_SESSION['form_error'] = 'Program not found.';
        header('Location: ' . BASE_URL . 'admin-programs');
        exit();
    }
} catch (Throwable $e) {
    error_log('Program edit load error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'An error occurred.';
    header('Location: ' . BASE_URL . 'admin-programs');
    exit();
}
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Edit Health Program</h1>
        <a href="<?php echo BASE_URL; ?>admin-programs" class="btn btn-secondary btn-sm">Back to Programs</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="post" action="<?php echo BASE_URL; ?>?action=update-program">
                <input type="hidden" name="program_id" value="<?php echo htmlspecialchars($program['program_id']); ?>">

                <div class="mb-3">
                    <label class="form-label">Program Name</label>
                    <input type="text" name="program_name" class="form-control" value="<?php echo htmlspecialchars($program['program_name'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($program['description'] ?? ''); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($program['start_date'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($program['end_date'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <input type="text" name="status" class="form-control" value="<?php echo htmlspecialchars($program['status'] ?? ''); ?>">
                    </div>
                </div>

                <div class="d-grid">
                    <button class="btn btn-primary" type="submit">Update Program</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
