<?php
/**
 * E-BHM Connect - Admin Header
 * Glassmorphism Design with Dark/Light Theme Support
 */

// Include helpers
require_once __DIR__ . '/auth_helpers.php';
require_once __DIR__ . '/security_helper.php';
require_once __DIR__ . '/translation_helper.php';

// Initialize user session preferences if not already done
if (!isset($GLOBALS['translations_loaded'])) {
    try {
        $userPrefs = get_user_preferences();
    } catch (Exception $e) {
        // Table may not exist yet, use defaults
        $userPrefs = ['theme' => 'light', 'language' => 'en'];
    }
    init_translations($userPrefs['language'] ?? 'en');
    $GLOBALS['translations_loaded'] = true;
}

// Get current user info
$currentUserName = $_SESSION['bhw_full_name'] ?? __('nav.admin');
$currentUserRole = $_SESSION['role'] ?? 'bhw';
$currentTheme = $_SESSION['theme'] ?? ($userPrefs['theme'] ?? 'light');
$currentLang = $_SESSION['language'] ?? ($userPrefs['language'] ?? 'en');

// Get unread notification count
$unreadNotifications = 0;
try {
    $unreadNotifications = get_unread_notification_count();
} catch (Exception $e) {
    // Silently fail if table doesn't exist yet
}

// --- PERMISSIONS FETCHING ---
// Re-fetch permissions on every page load to ensure Sidebar is dynamic/live
// Also fetch profile photo for sidebar display
$currentUserPhoto = null;
if (isset($_SESSION['bhw_id'])) {
    try {
        $stmtPerm = $pdo->prepare("SELECT access_permissions, profile_photo FROM bhw_users WHERE bhw_id = ?");
        $stmtPerm->execute([$_SESSION['bhw_id']]);
        $userData = $stmtPerm->fetch(PDO::FETCH_ASSOC);
        $jsonPerms = $userData['access_permissions'] ?? '';
        $currentUserPhoto = $userData['profile_photo'] ?? null;
        $currentPermissions = !empty($jsonPerms) ? json_decode($jsonPerms, true) : [];
        if (!is_array($currentPermissions)) $currentPermissions = [];
        $_SESSION['access_permissions'] = $currentPermissions;
    } catch (Exception $e) {
        // Fallback
        $_SESSION['access_permissions'] = [];
    }
}

// Determine profile photo path for sidebar
$sidebarPhotoPath = $currentUserPhoto 
    ? BASE_URL . 'uploads/profiles/' . $currentUserPhoto 
    : BASE_URL . 'assets/images/default-avatar.png';


// Get page title based on current page
$pageTitles = [
    'admin-dashboard' => __('nav.dashboard'),
    'admin-patients' => __('nav.patients'),
    'admin-messages' => __('nav.messages'),
    'admin-inventory' => __('nav.inventory'),
    'admin-inventory-categories' => __('nav.inventory_categories'),
    'admin-announcements' => __('nav.announcements'),
    'admin-reports' => __('nav.reports'),
    'admin-programs' => __('nav.programs'),
    'admin-bhw-users' => __('nav.bhw_management'),
    'admin-profile' => __('nav.profile'),
    'admin-settings' => __('nav.settings'),
    'admin-account-settings' => __('nav.account_settings'),
    'admin-app-settings' => __('nav.app_settings'),
];
$pageTitle = $pageTitles[$page] ?? __('nav.dashboard');
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>" data-theme="<?php echo htmlspecialchars($currentTheme); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <title><?php echo htmlspecialchars($pageTitle); ?> - E-BHM Connect</title>
    
    <!-- Preload critical resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/glass-components.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/shared-components.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/mobile-utils.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/chatbot.css" media="print" onload="this.media='all'">
    
    <!-- Driver.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>
    <!-- Driver.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/theme-switcher.js" defer></script>
    
    <!-- Prevent FOUC (Flash of Unstyled Content) -->
    <script>
        (function() {
            // Immediately apply theme from localStorage before any paint
            const savedTheme = localStorage.getItem('theme');
            const serverTheme = '<?php echo $currentTheme; ?>';
            const effectiveTheme = savedTheme || serverTheme;

            if (effectiveTheme === 'dark' ||
                (effectiveTheme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
</head>
<body>
    <!-- Animated Background Orbs -->
    <div class="admin-bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop"></div>

    <!-- Sidebar Navigation -->
    <aside id="admin-sidebar">
        <!-- Resize Handle -->
        <div class="sidebar-resize-handle" id="sidebar-resize-handle"></div>
        <!-- Brand -->
        <a href="<?php echo BASE_URL; ?>admin-dashboard" class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
            </div>
            <div>
                <div class="sidebar-brand-text">E-BHM Connect</div>
                <div class="sidebar-brand-tagline"><?php echo __('app.tagline'); ?></div>
            </div>
        </a>

        <!-- User Info -->
        <a href="<?php echo BASE_URL; ?>admin-account-settings" class="sidebar-user" style="text-decoration: none; display: flex;">
            <img src="<?php echo htmlspecialchars($sidebarPhotoPath); ?>" alt="<?php echo htmlspecialchars($currentUserName); ?>" class="sidebar-user-avatar">
            <div class="sidebar-user-info">
                <div class="sidebar-user-name"><?php echo htmlspecialchars($currentUserName); ?></div>
                <div class="sidebar-user-role <?php echo $currentUserRole === 'superadmin' ? 'superadmin' : ''; ?>">
                    <?php echo get_role_display_name(); ?>
                </div>
            </div>
        </a>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <!-- Main Navigation -->
            <div class="sidebar-nav-section">
                <div class="sidebar-nav-title"><?php echo __('nav.main_menu'); ?></div>
                <ul class="sidebar-nav-item">
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-dashboard" class="sidebar-nav-link <?php echo ($page === 'admin-dashboard') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/>
                            </svg>
                            <?php echo __('nav.dashboard'); ?>
                        </a>
                    </li>
                    
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-patients" class="sidebar-nav-link <?php echo ($page === 'admin-patients') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <?php echo __('nav.patients'); ?>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-messages" class="sidebar-nav-link <?php echo ($page === 'admin-messages') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            <?php echo __('nav.messages'); ?>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Health Records Section -->
            <div class="sidebar-nav-section">
                <div class="sidebar-nav-title">Health Records</div>
                <ul class="sidebar-nav-item">
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-health-records" class="sidebar-nav-link <?php echo ($page === 'admin-health-records') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                            </svg>
                            All Records
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-health-records-pregnancy" class="sidebar-nav-link <?php echo ($page === 'admin-health-records-pregnancy') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/>
                            </svg>
                            Pregnancy
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-health-records-childcare" class="sidebar-nav-link <?php echo ($page === 'admin-health-records-childcare') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 12h.01"/><path d="M15 12h.01"/><path d="M10 16c.5.3 1.2.5 2 .5s1.5-.2 2-.5"/><path d="M19 6.3a9 9 0 0 1 1.8 3.9 2 2 0 0 1 0 3.6 9 9 0 0 1-17.6 0 2 2 0 0 1 0-3.6A9 9 0 0 1 12 3c2 0 3.5 1.1 3.5 2.5s-.9 2.5-2 2.5c-.8 0-1.5-.4-1.5-1"/>
                            </svg>
                            Child Care
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-health-records-chronic" class="sidebar-nav-link <?php echo ($page === 'admin-health-records-chronic') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19.5 12.572l-7.5 7.428l-7.5-7.428a5 5 0 1 1 7.5-6.566a5 5 0 1 1 7.5 6.572"/>
                            </svg>
                            Chronic Diseases
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-health-records-wra" class="sidebar-nav-link <?php echo ($page === 'admin-health-records-wra') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/><path d="M12 13v8"/>
                            </svg>
                            WRA Tracking
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Inventory Section -->
            <div class="sidebar-nav-section">
                <div class="sidebar-nav-title"><?php echo __('nav.inventory_section'); ?></div>
                <ul class="sidebar-nav-item">
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-inventory" class="sidebar-nav-link <?php echo ($page === 'admin-inventory') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
                            </svg>
                            <?php echo __('nav.inventory'); ?>
                        </a>
                    </li>
                    <?php if (has_permission('manage_inventory')): ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-inventory-categories" class="sidebar-nav-link <?php echo ($page === 'admin-inventory-categories') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                            </svg>
                            <?php echo __('nav.inventory_categories'); ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-dispensation-history" class="sidebar-nav-link <?php echo ($page === 'admin-dispensation-history') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                            </svg>
                            Dispensation Log
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Content Section -->
            <div class="sidebar-nav-section">
                <div class="sidebar-nav-title"><?php echo __('nav.content_section'); ?></div>
                <ul class="sidebar-nav-item">
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-announcements" class="sidebar-nav-link <?php echo ($page === 'admin-announcements') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"/>
                            </svg>
                            <?php echo __('nav.announcements'); ?>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-programs" class="sidebar-nav-link <?php echo ($page === 'admin-programs') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <?php echo __('nav.programs'); ?>
                        </a>
                    </li>

                    <?php if (has_permission('view_reports')): ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-reports" class="sidebar-nav-link <?php echo ($page === 'admin-reports') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
                            </svg>
                            <?php echo __('nav.reports'); ?>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Administration Section (Admin/Superadmin only) -->
            <?php if (is_admin()): ?>
            <div class="sidebar-nav-section">
                <div class="sidebar-nav-title"><?php echo __('nav.administration'); ?></div>
                <ul class="sidebar-nav-item">
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-bhw-users" class="sidebar-nav-link <?php echo ($page === 'admin-bhw-users') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/>
                            </svg>
                            <?php echo __('nav.bhw_management'); ?>
                        </a>
                    </li>
                    <?php if (is_superadmin()): ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-app-settings" class="sidebar-nav-link <?php echo ($page === 'admin-app-settings') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                            <?php echo __('nav.app_settings'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-db-backup" class="sidebar-nav-link <?php echo ($page === 'admin-db-backup') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                            </svg>
                            <?php echo __('nav.db_backup') ?: 'DB Backup'; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-audit-logs" class="sidebar-nav-link <?php echo ($page === 'admin-audit-logs') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                            </svg>
                            <?php echo __('nav.audit_logs') ?: 'Audit Logs'; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-user-roles" class="sidebar-nav-link <?php echo ($page === 'admin-user-roles') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <?php echo __('nav.user_roles') ?: 'User Roles'; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>
        </nav>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <a href="<?php echo BASE_URL; ?>admin-account-settings" class="sidebar-nav-link <?php echo ($page === 'admin-account-settings') ? 'active' : ''; ?>">
                <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                <?php echo __('nav.account_settings'); ?>
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div id="main-content-wrapper">
        <!-- Top Navigation Bar -->
        <header id="admin-top-nav">
            <div class="topnav-left">
                <!-- Mobile Sidebar Toggle -->
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="<?php echo __('nav.toggle_sidebar'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                
                <!-- Page Title -->
                <h1 class="topnav-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
            </div>

            <div class="topnav-right">
                <!-- Language Selector -->
                <button class="lang-selector" id="langSelector" title="<?php echo __('settings.language'); ?>">
                    <span class="lang-selector-flag"><?php echo $currentLang === 'tl' ? '🇵🇭' : '🇺🇸'; ?></span>
                    <span><?php echo $currentLang === 'tl' ? 'Filipino' : 'English'; ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left:auto; opacity:0.5;">
                        <path d="m6 9 6 6 6-6" />
                    </svg>
                </button>

                <!-- Theme Toggle Switch -->
                <div class="theme-switch-wrapper" id="themeToggle" title="<?php echo __('settings.theme'); ?>">
                    <div class="theme-toggle-slider"></div>
                    <button class="theme-toggle-btn <?php echo $currentTheme === 'light' ? 'active' : ''; ?>" data-theme-val="light">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                        </svg>
                    </button>
                    <button class="theme-toggle-btn <?php echo $currentTheme === 'dark' ? 'active' : ''; ?>" data-theme-val="dark">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                        </svg>
                    </button>
                </div>

                <!-- Help / Admin Tour -->
                <button type="button" onclick="startAdminTour()" class="topnav-action" title="<?php echo __('nav.admin_tour') ?: 'Admin Tour'; ?>" style="background:none;border:none;cursor:pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </button>

                <!-- View Public Site -->
                <a href="<?php echo BASE_URL; ?>" target="_blank" class="topnav-action" title="<?php echo __('nav.view_public_site'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                </a>

                <!-- Logout -->
                <a href="<?php echo BASE_URL; ?>?action=logout" class="topnav-action" title="<?php echo __('auth.logout'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </a>
            </div>
        </header>

        <!-- Main Content Area -->
        <main id="main-content">
