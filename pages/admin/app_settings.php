<?php
/**
 * Application Settings Page (Superadmin Only)
 * E-BHM Connect - Glassmorphism Design
 * 
 * System-wide configuration settings
 */
include __DIR__ . '/../../includes/header_admin.php';

// Double-check superadmin access
if (!is_superadmin()) {
    header('Location: ' . BASE_URL . 'admin-dashboard');
    exit;
}

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'save_general') {
        try {
            $settings = [
                'site_name' => trim($_POST['site_name'] ?? 'E-BHM Connect'),
                'site_tagline' => trim($_POST['site_tagline'] ?? 'Barangay Health Management System'),
                'contact_email' => trim($_POST['contact_email'] ?? ''),
                'contact_phone' => trim($_POST['contact_phone'] ?? ''),
                'barangay_name' => trim($_POST['barangay_name'] ?? ''),
                'municipality' => trim($_POST['municipality'] ?? ''),
                'province' => trim($_POST['province'] ?? ''),
            ];
            
            foreach ($settings as $key => $value) {
                set_app_setting($key, $value, 'string');
            }
            
            log_audit('update_general_settings', 'app_settings', null, ['changes' => array_keys($settings)]);
            $message = __('settings.settings_saved');
        } catch (Throwable $e) {
            error_log('Settings save error: ' . $e->getMessage());
            $error = __('error');
        }
    }
    
    if ($action === 'save_features') {
        try {
            $features = [
                'enable_sms' => isset($_POST['enable_sms']) ? '1' : '0',
                'enable_chatbot' => isset($_POST['enable_chatbot']) ? '1' : '0',
                'enable_patient_portal' => isset($_POST['enable_patient_portal']) ? '1' : '0',
                'enable_email_verification' => isset($_POST['enable_email_verification']) ? '1' : '0',
                'portal_registration_mode' => $_POST['portal_registration_mode'] ?? 'linked_only',
                'default_language' => $_POST['default_language'] ?? 'en',
                'default_theme' => $_POST['default_theme'] ?? 'dark',
            ];
            
            foreach ($features as $key => $value) {
                set_app_setting($key, $value, 'string');
            }
            
            log_audit('update_feature_settings', 'app_settings', null, ['changes' => array_keys($features)]);
            $message = __('settings.settings_saved');
        } catch (Throwable $e) {
            error_log('Features save error: ' . $e->getMessage());
            $error = __('error');
        }
    }
    
    if ($action === 'save_maintenance') {
        try {
            set_app_setting('maintenance_mode', isset($_POST['maintenance_mode']) ? '1' : '0', 'string');
            set_app_setting('maintenance_message', trim($_POST['maintenance_message'] ?? ''), 'string');
            
            log_audit('update_maintenance_settings', 'app_settings', null, ['enabled' => isset($_POST['maintenance_mode'])]);
            $message = __('settings.settings_saved');
        } catch (Throwable $e) {
            error_log('Maintenance save error: ' . $e->getMessage());
            $error = __('error');
        }
    }
}

// Get current settings
$settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM app_settings");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Throwable $e) {
    // Table may not exist yet
    error_log('App settings fetch error: ' . $e->getMessage());
}

// Default values
$defaults = [
    'site_name' => 'E-BHM Connect',
    'site_tagline' => 'Barangay Health Management System',
    'contact_email' => '',
    'contact_phone' => '',
    'barangay_name' => '',
    'municipality' => '',
    'province' => '',
    'enable_sms' => '1',
    'enable_chatbot' => '1',
    'enable_patient_portal' => '1',
    'enable_email_verification' => '1',
    'default_language' => 'en',
    'default_theme' => 'dark',
    'maintenance_mode' => '0',
    'maintenance_message' => 'We are currently performing maintenance. Please check back later.',
];

foreach ($defaults as $key => $default) {
    if (!isset($settings[$key])) {
        $settings[$key] = $default;
    }
}
?>

<div class="container-fluid py-4 fade-in">
    <!-- Page Header -->
    <div class="glass-card mb-4" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(32, 201, 151, 0.1));">
        <div class="glass-card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-1"><?php echo __('settings.app_settings'); ?></h1>
                <p class="text-secondary mb-0"><?php echo __('settings.app_settings_description') ?: 'Configure system-wide application settings'; ?></p>
            </div>
            <span class="badge badge-danger"><?php echo __('roles.superadmin'); ?> Only</span>
        </div>
    </div>

    <?php if ($message): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Success!',
            text: '<?php echo addslashes($message); ?>',
            icon: 'success',
            confirmButtonColor: 'var(--primary)',
            timer: 3000,
            timerProgressBar: true
        });
    });
    </script>
    <?php endif; ?>

    <?php if ($error): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Error',
            text: '<?php echo addslashes($error); ?>',
            icon: 'error',
            confirmButtonColor: 'var(--primary)'
        });
    });
    </script>
    <?php endif; ?>

    <div class="row g-4">
        <!-- General Settings -->
        <div class="col-12 col-lg-6">
            <div class="glass-card">
                <div class="glass-card-header">
                    <h5 class="glass-card-title mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                            <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                        </svg>
                        <?php echo __('settings.general'); ?>
                    </h5>
                </div>
                <div class="glass-card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="save_general">
                        
                        <div class="mb-3">
                            <label class="form-label"><?php echo __('app_name'); ?></label>
                            <input type="text" name="site_name" class="form-control" value="<?php echo htmlspecialchars($settings['site_name']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><?php echo __('app_tagline'); ?></label>
                            <input type="text" name="site_tagline" class="form-control" value="<?php echo htmlspecialchars($settings['site_tagline']); ?>">
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label"><?php echo __('email'); ?></label>
                                <input type="email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($settings['contact_email']); ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label"><?php echo __('phone'); ?></label>
                                <input type="tel" name="contact_phone" class="form-control" value="<?php echo htmlspecialchars($settings['contact_phone']); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Barangay Name</label>
                            <input type="text" name="barangay_name" class="form-control" value="<?php echo htmlspecialchars($settings['barangay_name']); ?>">
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Municipality</label>
                                <input type="text" name="municipality" class="form-control" value="<?php echo htmlspecialchars($settings['municipality']); ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Province</label>
                                <input type="text" name="province" class="form-control" value="<?php echo htmlspecialchars($settings['province']); ?>">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary"><?php echo __('save_changes'); ?></button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Feature Toggles -->
        <div class="col-12 col-lg-6">
            <div class="glass-card mb-4">
                <div class="glass-card-header">
                    <h5 class="glass-card-title mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                            <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/>
                        </svg>
                        <?php echo __('settings.features'); ?>
                    </h5>
                </div>
                <div class="glass-card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="save_features">
                        
                        <div class="feature-toggle-list">
                            <div class="feature-toggle d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--border-color);">
                                <div>
                                    <strong>SMS Notifications</strong>
                                    <p class="text-secondary small mb-0">Enable SMS notifications for patients and alerts</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="enable_sms" id="enable_sms" <?php echo $settings['enable_sms'] === '1' ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            
                            <div class="feature-toggle d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--border-color);">
                                <div>
                                    <strong>AI Chatbot (Gabby)</strong>
                                    <p class="text-secondary small mb-0">Enable the AI health assistant chatbot</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="enable_chatbot" id="enable_chatbot" <?php echo $settings['enable_chatbot'] === '1' ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            
                            <div class="feature-toggle d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--border-color);">
                                <div>
                                    <strong>Patient Portal</strong>
                                    <p class="text-secondary small mb-0">Allow patients to register and access their records</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="enable_patient_portal" id="enable_patient_portal" <?php echo $settings['enable_patient_portal'] === '1' ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            
                            <div class="feature-toggle d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--border-color);">
                                <div>
                                    <strong>Portal Registration Mode</strong>
                                    <p class="text-secondary small mb-0">Controls how residents can register for portal access</p>
                                </div>
                                <select name="portal_registration_mode" class="form-select" style="width:auto;">
                                    <option value="linked_only" <?php echo ($settings['portal_registration_mode'] ?? 'linked_only') === 'linked_only' ? 'selected' : ''; ?>>🔗 Existing Record Only</option>
                                    <option value="open" <?php echo ($settings['portal_registration_mode'] ?? 'linked_only') === 'open' ? 'selected' : ''; ?>>🌐 Open Registration</option>
                                </select>
                            </div>
                            
                            <div class="feature-toggle d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--border-color);">
                                <div>
                                    <strong>Email Verification</strong>
                                    <p class="text-secondary small mb-0">Require email verification for new BHW accounts</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="enable_email_verification" id="enable_email_verification" <?php echo $settings['enable_email_verification'] === '1' ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            
                            <div class="feature-toggle d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--border-color);">
                                <div>
                                    <strong>Default Language</strong>
                                    <p class="text-secondary small mb-0">Default language for new users</p>
                                </div>
                                <select name="default_language" class="form-select" style="width:auto;">
                                    <option value="en" <?php echo $settings['default_language'] === 'en' ? 'selected' : ''; ?>>English</option>
                                    <option value="tl" <?php echo $settings['default_language'] === 'tl' ? 'selected' : ''; ?>>Tagalog</option>
                                </select>
                            </div>
                            
                            <div class="feature-toggle d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <strong>Default Theme</strong>
                                    <p class="text-secondary small mb-0">Default color theme for new users</p>
                                </div>
                                <select name="default_theme" class="form-select" style="width:auto;">
                                    <option value="dark" <?php echo ($settings['default_theme'] ?? 'dark') === 'dark' ? 'selected' : ''; ?>>🌙 Dark</option>
                                    <option value="light" <?php echo ($settings['default_theme'] ?? 'dark') === 'light' ? 'selected' : ''; ?>>☀️ Light</option>
                                </select>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mt-3"><?php echo __('save_changes'); ?></button>
                    </form>
                </div>
            </div>

            <!-- Maintenance Mode -->
            <div class="glass-card">
                <div class="glass-card-header">
                    <h5 class="glass-card-title mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                        </svg>
                        <?php echo __('settings.maintenance'); ?>
                    </h5>
                </div>
                <div class="glass-card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="save_maintenance">
                        
                        <div class="alert <?php echo $settings['maintenance_mode'] === '1' ? 'alert-warning' : 'alert-info'; ?> mb-3">
                            <?php if ($settings['maintenance_mode'] === '1'): ?>
                            <strong>⚠️ Maintenance Mode is ACTIVE</strong><br>
                            Public users cannot access the system.
                            <?php else: ?>
                            <strong>ℹ️ System is Online</strong><br>
                            All services are available to users.
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenance_mode" <?php echo $settings['maintenance_mode'] === '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="maintenance_mode"><?php echo __('settings.enable_maintenance'); ?></label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><?php echo __('settings.maintenance_message'); ?></label>
                            <textarea name="maintenance_message" class="form-control" rows="3"><?php echo htmlspecialchars($settings['maintenance_message']); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary"><?php echo __('save_changes'); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="glass-card" style="border: 1px solid rgba(239, 68, 68, 0.3);">
                <div class="glass-card-header" style="border-bottom-color: rgba(239, 68, 68, 0.2);">
                    <h5 class="glass-card-title mb-0 text-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                        Danger Zone
                    </h5>
                </div>
                <div class="glass-card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <strong>Clear Audit Logs</strong>
                            <p class="text-secondary small mb-0">Permanently delete all audit log entries. This action cannot be undone.</p>
                        </div>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmClearLogs()">Clear Logs</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmClearLogs() {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This will permanently delete all audit logs. This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, clear all logs'
    }).then((result) => {
        if (result.isConfirmed) {
            // Send AJAX request to clear logs
            fetch('<?php echo BASE_URL; ?>?action=clear-audit-logs', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Cleared!', 'All audit logs have been deleted.', 'success');
                } else {
                    Swal.fire('Error', data.message || 'Failed to clear logs.', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'An error occurred.', 'error');
            });
        }
    });
}
</script>

<style>
.form-switch .form-check-input {
    width: 3em;
    height: 1.5em;
}
.form-switch .form-check-input:checked {
    background-color: var(--primary);
    border-color: var(--primary);
}
</style>

<?php include __DIR__ . '/../../includes/footer_admin.php'; ?>
