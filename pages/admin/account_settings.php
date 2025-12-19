<?php
/**
 * Account Settings Page
 * E-BHM Connect - Glassmorphism Design
 * 
 * Allows users to manage their profile, change password, and preferences
 */
include __DIR__ . '/../../includes/header_admin.php';

// Get current user data
$user_id = $_SESSION['bhw_id'] ?? null;
$stmt = $pdo->prepare("SELECT *, contact as phone FROM bhw_users WHERE bhw_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: ' . BASE_URL . 'admin-dashboard');
    exit;
}

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        // Update profile information
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        
        if (empty($full_name) || empty($email)) {
            $error = __('settings.error_required_fields');
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE bhw_users SET full_name = ?, email = ?, contact = ?, address = ? WHERE bhw_id = ?");
                $stmt->execute([$full_name, $email, $phone, $address, $user_id]);
                
                // Update session
                $_SESSION['bhw_full_name'] = $full_name;
                
                // Log audit
                log_audit($user_id, 'update', 'profile', $user_id, 'Updated profile information');
                
                $message = __('settings.profile_updated');
                
                // Refresh user data
                $stmt = $pdo->prepare("SELECT *, contact as phone FROM bhw_users WHERE bhw_id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log('Profile update error: ' . $e->getMessage());
                $error = __('error');
            }
        }
    }
    
    if ($action === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = __('settings.error_required_fields');
        } elseif ($new_password !== $confirm_password) {
            $error = __('auth.password_mismatch');
        } elseif (strlen($new_password) < 8) {
            $error = __('auth.password_requirements');
        } elseif (!password_verify($current_password, $user['password_hash'] ?? '')) {
            $error = __('settings.incorrect_password');
        } else {
            try {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE bhw_users SET password_hash = ? WHERE bhw_id = ?");
                $stmt->execute([$hashed, $user_id]);
                
                // Log audit
                log_audit($user_id, 'update', 'password', $user_id, 'Changed password');
                
                $message = __('auth.password_changed');
            } catch (PDOException $e) {
                error_log('Password change error: ' . $e->getMessage());
                $error = __('error');
            }
        }
    }
    
    if ($action === 'save_preferences') {
        $theme = $_POST['theme'] ?? 'light';
        $language = $_POST['language'] ?? 'en';
        
        try {
            save_user_preferences($user_id, 'bhw', [
                'theme' => $theme,
                'language' => $language
            ]);
            
            // Update session
            $_SESSION['theme'] = $theme;
            $_SESSION['language'] = $language;
            $_SESSION['user_language'] = $language; // Sync for translation_helper
            
            // Reinitialize translations with new language
            init_translations($language);
            
            $message = __('settings.preferences_saved');
        } catch (Throwable $e) {
            error_log('Preferences save error: ' . $e->getMessage());
            $error = __('error');
        }
    }
}

// Get current preferences
$preferences = [];
try {
    $preferences = get_user_preferences($user_id, 'bhw');
} catch (Throwable $e) {
    // Table may not exist yet
}
$current_theme = $preferences['theme'] ?? $_SESSION['theme'] ?? 'light';
$current_lang = $preferences['language'] ?? $_SESSION['language'] ?? 'en';
?>

<div class="container-fluid py-4 fade-in">
    <!-- Page Header -->
    <div class="glass-card mb-4">
        <div class="glass-card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-1"><?php echo __('settings.account_settings'); ?></h1>
                <p class="text-secondary mb-0"><?php echo __('settings.manage_your_account'); ?></p>
            </div>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-12 col-lg-4">
            <div class="glass-card">
                <div class="glass-card-body text-center py-4">
                    <!-- Avatar -->
                    <div class="avatar-xl mx-auto mb-3" style="width:96px;height:96px;border-radius:20px;background:linear-gradient(135deg, var(--primary), var(--secondary));display:flex;align-items:center;justify-content:center;font-size:2rem;color:#fff;font-weight:700;">
                        <?php echo strtoupper(substr($user['full_name'] ?? 'U', 0, 2)); ?>
                    </div>
                    <h5 class="mb-1"><?php echo htmlspecialchars($user['full_name'] ?? 'User'); ?></h5>
                    <p class="text-secondary mb-3"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                    
                    <!-- Role Badge -->
                    <?php
                    $role = $user['role'] ?? 'bhw';
                    $roleBadge = match($role) {
                        'superadmin' => '<span class="badge badge-danger">Superadmin</span>',
                        'admin' => '<span class="badge badge-primary">Admin</span>',
                        default => '<span class="badge badge-secondary">BHW</span>'
                    };
                    echo $roleBadge;
                    ?>
                    
                    <!-- Account Info -->
                    <div class="mt-4 text-start">
                        <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid var(--border-color);">
                            <span class="text-secondary"><?php echo __('settings.account_created'); ?></span>
                            <span><?php echo date('M j, Y', strtotime($user['created_at'] ?? 'now')); ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid var(--border-color);">
                            <span class="text-secondary"><?php echo __('status'); ?></span>
                            <?php 
                            $accountStatus = $user['account_status'] ?? 'pending';
                            $isVerified = ($user['email_verified'] ?? 0) == 1;
                            if ($accountStatus === 'approved'): ?>
                            <span class="text-success"><?php echo __('settings.verified'); ?></span>
                            <?php elseif ($isVerified): ?>
                            <span class="text-info">Email Verified</span>
                            <?php else: ?>
                            <span class="text-warning"><?php echo __('settings.pending'); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-secondary"><?php echo __('settings.last_login'); ?></span>
                            <span><?php echo isset($user['last_login']) ? date('M j, Y g:i A', strtotime($user['last_login'])) : 'N/A'; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Forms -->
        <div class="col-12 col-lg-8">
            <!-- Profile Information -->
            <div class="glass-card mb-4">
                <div class="glass-card-header">
                    <h5 class="glass-card-title mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                        <?php echo __('settings.profile_information'); ?>
                    </h5>
                </div>
                <div class="glass-card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label"><?php echo __('patients.full_name'); ?> *</label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label"><?php echo __('email'); ?> *</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label"><?php echo __('phone'); ?></label>
                                <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label"><?php echo __('address'); ?></label>
                                <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary"><?php echo __('save_changes'); ?></button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="glass-card mb-4">
                <div class="glass-card-header">
                    <h5 class="glass-card-title mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <?php echo __('auth.change_password'); ?>
                    </h5>
                </div>
                <div class="glass-card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label"><?php echo __('auth.current_password'); ?> *</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label"><?php echo __('auth.new_password'); ?> *</label>
                                <input type="password" name="new_password" class="form-control" required minlength="8">
                                <small class="text-muted"><?php echo __('auth.password_requirements'); ?></small>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label"><?php echo __('auth.confirm_password'); ?> *</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary"><?php echo __('auth.change_password'); ?></button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preferences -->
            <div class="glass-card">
                <div class="glass-card-header">
                    <h5 class="glass-card-title mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                            <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                        </svg>
                        <?php echo __('settings.preferences'); ?>
                    </h5>
                </div>
                <div class="glass-card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="save_preferences">
                        
                        <div class="row g-4">
                            <!-- Theme Selection -->
                            <div class="col-12 col-md-6">
                                <label class="form-label"><?php echo __('settings.theme'); ?></label>
                                <div class="d-flex gap-3">
                                    <label class="theme-option">
                                        <input type="radio" name="theme" value="light" <?php echo $current_theme === 'light' ? 'checked' : ''; ?>>
                                        <span class="theme-card" style="background:#fff;border:2px solid var(--border-color);border-radius:12px;padding:1rem;display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                                            </svg>
                                            <span style="color:#1e293b;"><?php echo __('settings.light_mode'); ?></span>
                                        </span>
                                    </label>
                                    <label class="theme-option">
                                        <input type="radio" name="theme" value="dark" <?php echo $current_theme === 'dark' ? 'checked' : ''; ?>>
                                        <span class="theme-card" style="background:#1e293b;border:2px solid var(--border-color);border-radius:12px;padding:1rem;display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                                            </svg>
                                            <span style="color:#f8fafc;"><?php echo __('settings.dark_mode'); ?></span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <!-- Language Selection -->
                            <div class="col-12 col-md-6">
                                <label class="form-label"><?php echo __('settings.language'); ?></label>
                                <select name="language" class="form-select">
                                    <option value="en" <?php echo $current_lang === 'en' ? 'selected' : ''; ?>>🇺🇸 English</option>
                                    <option value="tl" <?php echo $current_lang === 'tl' ? 'selected' : ''; ?>>🇵🇭 Tagalog</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary"><?php echo __('settings.save_preferences'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.theme-option input[type="radio"] {
    display: none;
}
.theme-option input[type="radio"]:checked + .theme-card {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.2);
}
.theme-card {
    transition: all 0.25s ease;
}
.theme-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>

<script>
// Theme toggle functionality
document.querySelectorAll('input[name="theme"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const theme = this.value;
        // Apply theme immediately
        document.documentElement.setAttribute('data-theme', theme);
        // Save to localStorage
        localStorage.setItem('userTheme', theme);
    });
});

// Language change functionality
document.querySelector('select[name="language"]').addEventListener('change', function() {
    const language = this.value;
    // Save to localStorage
    localStorage.setItem('userLanguage', language);
});

// Load saved theme on page load
window.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('userTheme') || '<?php echo $current_theme; ?>';
    document.documentElement.setAttribute('data-theme', savedTheme);
});
</script>

<?php include __DIR__ . '/../../includes/footer_admin.php'; ?>
