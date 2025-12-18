<?php
// pages/admin/bhw_edit.php
// Edit BHW user form
include_once __DIR__ . '/../../includes/header_admin.php';

$bhw_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($bhw_id <= 0) {
    header('Location: ' . BASE_URL . 'admin-bhw-users');
    exit();
}

try {
    $stmt = $pdo->prepare('SELECT * FROM bhw_users WHERE bhw_id = ? LIMIT 1');
    $stmt->execute([$bhw_id]);
    $bhw = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Fetch BHW for edit error: ' . $e->getMessage());
    $bhw = false;
}

if (!$bhw) {
    header('Location: ' . BASE_URL . 'admin-bhw-users');
    exit();
}
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Edit BHW User</h3>
        </div>
        <div class="card-body">
            <form method="post" action="?action=update-bhw" enctype="multipart/form-data">
                <?php echo csrf_input(); ?>
                <input type="hidden" name="bhw_id" value="<?php echo htmlspecialchars($bhw['bhw_id']); ?>">

                <div class="row">
                    <!-- Left Column: Personal Info -->
                    <div class="col-md-6">
                        <h5 class="mb-3 text-primary">Personal Information</h5>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control glass-input" value="<?php echo htmlspecialchars($bhw['full_name']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control glass-input" value="<?php echo htmlspecialchars($bhw['username']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">BHW ID Number</label>
                            <input type="text" name="bhw_unique_id" class="form-control glass-input" value="<?php echo htmlspecialchars($bhw['bhw_unique_id']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control glass-input" value="<?php echo htmlspecialchars($bhw['address']); ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Birthdate</label>
                                <input type="date" name="birthdate" class="form-control glass-input" value="<?php echo htmlspecialchars($bhw['birthdate']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact</label>
                                <input type="text" name="contact" class="form-control glass-input" value="<?php echo htmlspecialchars($bhw['contact']); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Employment & Permissions -->
                    <div class="col-md-6">
                        <h5 class="mb-3 text-primary">Employment Details</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">Assigned Area</label>
                            <select name="assigned_area" class="form-control glass-select">
                                <option value="">-- Select Area --</option>
                                <?php 
                                $areas = ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5', 'Purok 6', 'Purok 7', 'Centro', 'Norte', 'Sur'];
                                foreach ($areas as $area) : 
                                    $selected = ($bhw['assigned_area'] === $area) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $area; ?>" <?php echo $selected; ?>><?php echo $area; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Employment Status</label>
                            <select name="employment_status" class="form-control glass-select">
                                <option value="Active" <?php echo ($bhw['employment_status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                                <option value="Inactive" <?php echo ($bhw['employment_status'] === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                <option value="On Leave" <?php echo ($bhw['employment_status'] === 'On Leave') ? 'selected' : ''; ?>>On Leave</option>
                                <option value="Resigned" <?php echo ($bhw['employment_status'] === 'Resigned') ? 'selected' : ''; ?>>Resigned</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Training Certificate</label>
                            <input type="file" name="training_cert_file" class="form-control glass-input" accept=".jpg,.jpeg,.png,.pdf">
                            <?php if (!empty($bhw['training_cert'])): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Current File:</small><br>
                                    <a href="<?php echo BASE_URL . $bhw['training_cert']; ?>" target="_blank" class="text-primary">
                                        <i class="fas fa-file-alt me-1"></i> View Certificate
                                    </a>
                                </div>
                            <?php endif; ?>
                            <!-- Keep old path if no new file uploaded -->
                            <input type="hidden" name="existing_cert" value="<?php echo htmlspecialchars($bhw['training_cert']); ?>">
                        </div>

                        <h5 class="mb-3 text-primary mt-4">Access Permissions</h5>
                        <div class="glass-card p-3">
                            <?php 
                            $perms = !empty($bhw['access_permissions']) ? json_decode($bhw['access_permissions'], true) : [];
                            if (!is_array($perms)) $perms = [];
                            
                            $permissions_list = [
                                'manage_patients' => 'Manage Patients (View/Add/Edit)',
                                'manage_inventory' => 'Manage Inventory',
                                'manage_programs' => 'Manage Health Programs',
                                'view_reports' => 'View & Generate Reports',
                                'use_messages' => 'Use Messaging System',
                                'manage_announcements' => 'Manage Announcements'
                            ];
                            ?>
                            <div class="row">
                                <?php foreach ($permissions_list as $key => $label): ?>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="<?php echo $key; ?>" id="perm_<?php echo $key; ?>" <?php echo in_array($key, $perms) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="perm_<?php echo $key; ?>"><?php echo $label; ?></label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary-glass btn-lg">
                        <i class="fas fa-save me-2"></i> Update BHW Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
