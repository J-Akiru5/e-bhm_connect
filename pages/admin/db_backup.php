<?php
/**
 * Database Backup & Restore Page (Super Admin Only)
 * E-BHM Connect - Glassmorphism Design
 * 
 * Allows super admins to create, download, and restore database backups
 */

// Include config first (no output)
include_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth_helpers.php';

// Require superadmin access
if (!is_superadmin()) {
    $_SESSION['flash_error'] = 'You do not have permission to access this page.';
    header('Location: ' . BASE_URL . 'admin-dashboard');
    exit;
}

// Now include header (outputs HTML)
include_once __DIR__ . '/../../includes/header_admin.php';

// Backup directory (outside web root for security)
$backupDir = __DIR__ . '/../../backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Get list of existing backups
$backups = [];
if (is_dir($backupDir)) {
    $files = scandir($backupDir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            $filepath = $backupDir . '/' . $file;
            $backups[] = [
                'filename' => $file,
                'size' => filesize($filepath),
                'created' => filemtime($filepath)
            ];
        }
    }
    // Sort by date, newest first
    usort($backups, function($a, $b) {
        return $b['created'] - $a['created'];
    });
}

// Flash messages
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<div class="container-fluid py-4 fade-in">
    <!-- Page Header -->
    <div class="glass-card mb-4">
        <div class="glass-card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h4 class="mb-1"><?php echo __('backup.title') ?: 'Database Backup & Restore'; ?></h4>
                <p class="text-muted mb-0"><?php echo __('backup.description') ?: 'Create and manage database backups'; ?></p>
            </div>
            <div class="d-flex gap-2">
                <form method="POST" action="<?php echo BASE_URL; ?>actions/db_backup_action.php" class="d-inline">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        <?php echo __('backup.create_backup') ?: 'Create Backup'; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php if ($flash_success): ?>
    <div class="alert alert-success mb-4">
        <?php echo htmlspecialchars($flash_success); ?>
    </div>
    <?php endif; ?>

    <?php if ($flash_error): ?>
    <div class="alert alert-danger mb-4">
        <?php echo htmlspecialchars($flash_error); ?>
    </div>
    <?php endif; ?>

    <!-- Restore Section -->
    <div class="glass-card mb-4">
        <div class="glass-card-header">
            <h6 class="glass-card-title mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                    <path d="M3 3v5h5"/>
                </svg>
                <?php echo __('backup.restore_backup') ?: 'Restore from Backup'; ?>
            </h6>
        </div>
        <div class="glass-card-body">
            <form method="POST" action="<?php echo BASE_URL; ?>actions/db_backup_action.php" enctype="multipart/form-data" id="restoreForm">
                <input type="hidden" name="action" value="restore">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                
                <div class="row align-items-end g-3">
                    <div class="col-md-8">
                        <label class="form-label"><?php echo __('backup.select_file') ?: 'Select SQL Backup File'; ?></label>
                        <input type="file" name="backup_file" class="form-control" accept=".sql" required>
                        <small class="text-muted"><?php echo __('backup.file_hint') ?: 'Only .sql files are accepted'; ?></small>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-warning w-100" onclick="return confirmRestore()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                <path d="M3 3v5h5"/>
                            </svg>
                            <?php echo __('backup.restore_now') ?: 'Restore Now'; ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Existing Backups -->
    <div class="glass-card">
        <div class="glass-card-header">
            <h6 class="glass-card-title mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                    <ellipse cx="12" cy="5" rx="9" ry="3"/>
                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                </svg>
                <?php echo __('backup.existing_backups') ?: 'Existing Backups'; ?>
            </h6>
        </div>
        <div class="glass-card-body p-0">
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th><?php echo __('backup.filename') ?: 'Filename'; ?></th>
                            <th><?php echo __('backup.size') ?: 'Size'; ?></th>
                            <th><?php echo __('backup.created_at') ?: 'Created'; ?></th>
                            <th class="text-end"><?php echo __('actions') ?: 'Actions'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($backups)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                <?php echo __('backup.no_backups') ?: 'No backups found. Create your first backup!'; ?>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($backups as $backup): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                        </svg>
                                        <span><?php echo htmlspecialchars($backup['filename']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo number_format($backup['size'] / 1024, 2); ?> KB</td>
                                <td><?php echo date('M d, Y H:i', $backup['created']); ?></td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="<?php echo BASE_URL; ?>actions/db_backup_action.php?action=download&file=<?php echo urlencode($backup['filename']); ?>" 
                                           class="btn btn-sm btn-glass" title="<?php echo __('backup.download_backup') ?: 'Download'; ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                <polyline points="7 10 12 15 17 10"/>
                                                <line x1="12" y1="15" x2="12" y2="3"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="<?php echo BASE_URL; ?>actions/db_backup_action.php" class="d-inline" onsubmit="return confirmDelete()">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="file" value="<?php echo htmlspecialchars($backup['filename']); ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                            <button type="submit" class="btn btn-sm btn-glass text-danger" title="<?php echo __('backup.delete_backup') ?: 'Delete'; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
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

<script>
function confirmRestore() {
    return confirm('<?php echo __('backup.confirm_restore') ?: 'WARNING: This will overwrite all current data! Are you sure you want to restore from this backup?'; ?>');
}

function confirmDelete() {
    return confirm('<?php echo __('backup.confirm_delete') ?: 'Are you sure you want to delete this backup?'; ?>');
}
</script>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
