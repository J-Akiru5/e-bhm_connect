<?php
/**
 * E-BHM Connect - Admin Header
 * Glassmorphism Design with Dark/Light Theme Support
 */

// Prevent browser caching of admin pages
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Include helpers
require_once __DIR__ . '/auth_helpers.php';
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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/chatbot.css" media="print" onload="this.media='all'">
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/theme-switcher.js" defer></script>
    
    <!-- Prevent FOUC (Flash of Unstyled Content) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || '<?php echo $currentTheme; ?>';
            if (savedTheme === 'dark' || (savedTheme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.setAttribute('data-theme', 'dark');
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
        <div class="sidebar-user">
            <img src="<?php echo BASE_URL; ?>assets/images/gabby_avatar.png" alt="<?php echo htmlspecialchars($currentUserName); ?>" class="sidebar-user-avatar">
            <div class="sidebar-user-info">
                <div class="sidebar-user-name"><?php echo htmlspecialchars($currentUserName); ?></div>
                <div class="sidebar-user-role <?php echo $currentUserRole === 'superadmin' ? 'superadmin' : ''; ?>">
                    <?php echo get_role_display_name(); ?>
                </div>
            </div>
        </div>

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
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-inventory-categories" class="sidebar-nav-link <?php echo ($page === 'admin-inventory-categories') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                            </svg>
                            <?php echo __('nav.inventory_categories'); ?>
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
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-reports" class="sidebar-nav-link <?php echo ($page === 'admin-reports') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
                            </svg>
                            <?php echo __('nav.reports'); ?>
                        </a>
                    </li>
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
                        <a href="<?php echo BASE_URL; ?>admin-user-management" class="sidebar-nav-link <?php echo ($page === 'admin-user-management') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            User Management
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>admin-app-settings" class="sidebar-nav-link <?php echo ($page === 'admin-app-settings') ? 'active' : ''; ?>">
                            <svg class="sidebar-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                            <?php echo __('nav.app_settings'); ?>
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
                    <span><?php echo strtoupper($currentLang); ?></span>
                </button>

                <!-- Theme Toggle -->
                <button class="theme-toggle" data-theme-cycle title="<?php echo __('settings.theme'); ?>">
                    <span class="theme-toggle-icon <?php echo $currentTheme === 'light' ? 'active' : ''; ?>" data-theme="light">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                        </svg>
                    </span>
                    <span class="theme-toggle-icon <?php echo $currentTheme === 'dark' ? 'active' : ''; ?>" data-theme="dark">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                        </svg>
                    </span>
                </button>

                <!-- Notifications -->
                <div style="position: relative;">
                    <button class="topnav-action" id="notificationBtn" title="<?php echo __('notifications.title'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        <?php if ($unreadNotifications > 0): ?>
                        <span class="topnav-action-badge"><?php echo $unreadNotifications > 99 ? '99+' : $unreadNotifications; ?></span>
                        <?php endif; ?>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <span class="notification-title"><?php echo __('notifications.title'); ?></span>
                            <a href="#" id="markAllRead" style="font-size: 0.75rem; color: var(--primary);"><?php echo __('notifications.mark_all_read'); ?></a>
                        </div>
                        <div class="notification-list" id="notificationList">
                            <div class="notification-item" style="text-align: center; padding: 32px;">
                                <span style="color: var(--text-muted);"><?php echo __('notifications.loading'); ?></span>
                            </div>
                        </div>
                        <div class="notification-footer">
                            <a href="<?php echo BASE_URL; ?>admin-notifications"><?php echo __('notifications.view_all'); ?></a>
                        </div>
                    </div>
                </div>

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
