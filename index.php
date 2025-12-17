<?php
// Start the session ONCE for all requests
session_start();

// Include the Global Config (BASE_URL)
require_once 'config/config.php';

// Include the database connection ONCE for all requests
require_once 'config/database.php';

// --- Check for an "action" request (e.g., form submission) ---
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $actionPath = __DIR__ . '/actions/';

    // Whitelist of allowed action files (all in one array)
    $allowedActions = [
        // Auth (BHW)
        'login-bhw' => $actionPath . 'login_bhw_action.php',
        'register-bhw' => $actionPath . 'register_bhw_action.php',
        'logout' => $actionPath . 'logout.php',

        // Patient management
        'save-patient' => $actionPath . 'patient_save.php',
        'delete-patient' => $actionPath . 'patient_delete.php',

        // Inventory
        'save-inventory-item' => $actionPath . 'inventory_save.php',
        'delete-inventory-item' => $actionPath . 'inventory_delete.php',
        'update-inventory-item' => $actionPath . 'inventory_update.php',
        // Inventory categories
        'save-inventory-category' => $actionPath . 'inventory_category_save.php',
        'delete-inventory-category' => $actionPath . 'inventory_category_delete.php',

        // Vitals & Visits
        'save-vital' => $actionPath . 'vital_save.php',
        'save-visit' => $actionPath . 'visit_save.php',
        // Medicine dispensing
        'medicine-dispense-save' => $actionPath . 'medicine_dispense_save.php',

        // Programs
        'save-program' => $actionPath . 'program_save.php',
        'update-program' => $actionPath . 'program_update.php',
        'delete-program' => $actionPath . 'program_delete.php',

        // Announcements
        'save-announcement' => $actionPath . 'announcement_save.php',
        'update-announcement' => $actionPath . 'announcement_update.php',
        'delete-announcement' => $actionPath . 'announcement_delete.php',

        // Chatbot APIs
        'chatbot-api' => $actionPath . 'chatbot_api.php',
        'chatbot-portal-api' => $actionPath . 'chatbot_portal_api.php',
        // SMS Actions (manual triggers)
        'send-broadcast' => $actionPath . 'sms_actions.php',
        'resend-sms' => $actionPath . 'sms_actions.php',

        // Email verification
        'verify-bhw-email' => $actionPath . 'verify_bhw_email.php',

        // Reports
        'report-patient-list' => $actionPath . 'report_patient_list.php',
        'report-inventory-stock' => $actionPath . 'report_inventory.php',
        'report-chronic-disease' => $actionPath . 'report_chronic.php',
        'report-my-record' => $actionPath . 'report_my_record.php',
        'report-bhw-record' => $actionPath . 'report_bhw_record.php',
        'report-health-records' => $actionPath . 'report_health_records.php',

        // Patient portal auth/actions
        'register-patient' => $actionPath . 'register_patient_action.php',
        'login-patient' => $actionPath . 'login_patient_action.php',
        'logout-patient' => $actionPath . 'logout_patient.php',

        // Admin profile & BHW management
        'update-profile' => $actionPath . 'update_profile.php',
        'change-password' => $actionPath . 'change_password.php',
        'update-bhw' => $actionPath . 'bhw_update.php',

        // Dashboard/chart data
        'get-chart-data' => $actionPath . 'chart_data.php',
        
        // Preferences and notifications
        'save-preferences' => $actionPath . 'save_preferences.php',
        'notification-api' => $actionPath . 'notification_api.php',

        // Health Records Actions
        'save-pregnancy-tracking' => $actionPath . 'health_records_save.php',
        'delete-pregnancy-tracking' => $actionPath . 'health_records_delete.php',
        'save-childcare-record' => $actionPath . 'health_records_save.php',
        'delete-childcare-record' => $actionPath . 'health_records_delete.php',
        'save-natality-record' => $actionPath . 'health_records_save.php',
        'delete-natality-record' => $actionPath . 'health_records_delete.php',
        'save-mortality-record' => $actionPath . 'health_records_save.php',
        'delete-mortality-record' => $actionPath . 'health_records_delete.php',
        'save-chronic-disease' => $actionPath . 'health_records_save.php',
        'delete-chronic-disease' => $actionPath . 'health_records_delete.php',
        'save-ntp-client' => $actionPath . 'health_records_save.php',
        'delete-ntp-client' => $actionPath . 'health_records_delete.php',
        'save-wra-tracking' => $actionPath . 'health_records_save.php',
        'delete-wra-tracking' => $actionPath . 'health_records_delete.php',
    ];

    if (array_key_exists($action, $allowedActions) && file_exists($allowedActions[$action])) {
        require $allowedActions[$action]; // All actions run here
    } else {
        // Handle unknown action
        die('Invalid action request.');
    }
    exit(); // Stop script after action is performed
}

// --- Regular "page" request (if no action) ---
$page = isset($_GET['page']) && $_GET['page'] !== '' ? $_GET['page'] : 'home';
$basePath = __DIR__ . '/pages/';

// Whitelist of all allowed pages and their file paths
// **NEW: We've added a 'secure' => true flag for admin pages**
$allowedPages = [
    // Public Pages
    'home' => ['file' => $basePath . 'public/home.php', 'secure' => false],
    'contact' => ['file' => $basePath . 'public/contact.php', 'secure' => false],
    'mission-vision' => ['file' => $basePath . 'public/mission_vision.php', 'secure' => false],
    'announcements' => ['file' => $basePath . 'public/announcements.php', 'secure' => false],
    'about' => ['file' => $basePath . 'public/about.php', 'secure' => false],
    'privacy' => ['file' => $basePath . 'public/privacy.php', 'secure' => false],
    'help' => ['file' => $basePath . 'public/help.php', 'secure' => false],

    // Login/Register
    'login-bhw' => ['file' => $basePath . 'login_bhw.php', 'secure' => false],
    'register-bhw' => ['file' => $basePath . 'register_bhw.php', 'secure' => false],
    // Patient portal public pages
    'register-patient' => ['file' => $basePath . 'register_patient.php', 'secure' => false],
    'login-patient' => ['file' => $basePath . 'login_patient.php', 'secure' => false],

    // BHW Admin Portal
    'admin-dashboard' => ['file' => $basePath . 'admin/dashboard.php', 'secure' => true],
    'admin-patients' => ['file' => $basePath . 'admin/patients.php', 'secure' => true],
    'admin-patient-view' => ['file' => $basePath . 'admin/patient_view.php', 'secure' => true],
    'admin-patient-form' => ['file' => $basePath . 'admin/patient_form.php', 'secure' => true],
    'admin-inventory' => ['file' => $basePath . 'admin/inventory.php', 'secure' => true],
    'admin-inventory-categories' => ['file' => $basePath . 'admin/inventory_categories.php', 'secure' => true],
    'admin-inventory-edit' => ['file' => $basePath . 'admin/inventory_edit.php', 'secure' => true],
    'admin-programs' => ['file' => $basePath . 'admin/programs.php', 'secure' => true],
    'admin-program-edit' => ['file' => $basePath . 'admin/program_edit.php', 'secure' => true],
    'admin-announcements' => ['file' => $basePath . 'admin/announcements.php', 'secure' => true],
    'admin-announcement-edit' => ['file' => $basePath . 'admin/announcement_edit.php', 'secure' => true],
    'admin-messages' => ['file' => $basePath . 'admin/messages.php', 'secure' => true],
    'admin-reports' => ['file' => $basePath . 'admin/reports.php', 'secure' => true],
    'admin-bhw-users' => ['file' => $basePath . 'admin/bhw_users.php', 'secure' => true],
    'admin-bhw-edit' => ['file' => $basePath . 'admin/bhw_edit.php', 'secure' => true],
    'admin-user-management' => ['file' => $basePath . 'admin/user_management.php', 'secure' => 'superadmin'],
    'admin-profile' => ['file' => $basePath . 'admin/profile.php', 'secure' => true],
    'admin-account-settings' => ['file' => $basePath . 'admin/account_settings.php', 'secure' => true],
    'admin-app-settings' => ['file' => $basePath . 'admin/app_settings.php', 'secure' => 'superadmin'],
    'admin-audit-logs' => ['file' => $basePath . 'admin/audit_logs.php', 'secure' => 'admin'],

    // Health Records Module
    'admin-health-records' => ['file' => $basePath . 'admin/health_records/index.php', 'secure' => true],
    'admin-health-records-pregnancy' => ['file' => $basePath . 'admin/health_records/pregnancy_tracking.php', 'secure' => true],
    'admin-health-records-childcare' => ['file' => $basePath . 'admin/health_records/child_care.php', 'secure' => true],
    'admin-health-records-natality' => ['file' => $basePath . 'admin/health_records/natality.php', 'secure' => true],
    'admin-health-records-mortality' => ['file' => $basePath . 'admin/health_records/mortality.php', 'secure' => true],
    'admin-health-records-chronic' => ['file' => $basePath . 'admin/health_records/chronic_diseases.php', 'secure' => true],
    'admin-health-records-ntp' => ['file' => $basePath . 'admin/health_records/ntp_monitoring.php', 'secure' => true],
    'admin-health-records-wra' => ['file' => $basePath . 'admin/health_records/wra_tracking.php', 'secure' => true],

    // Patient Portal
    'portal-dashboard' => ['file' => $basePath . 'portal/portal_dashboard.php', 'secure' => 'patient'], // Example for patient auth
    'portal-chatbot' => ['file' => $basePath . 'portal/portal_chatbot.php', 'secure' => 'patient'],

    // Error
    '404' => ['file' => $basePath . 'public/404.php', 'secure' => false]
];

// --- Router Security Logic ---
if (array_key_exists($page, $allowedPages)) {
    
    $pageData = $allowedPages[$page];
    
    // **This is our new "React Hook" style guard**
    if ($pageData['secure'] === true && !isset($_SESSION['bhw_id'])) {
        // Page is for BHWs, but user is not logged in as a BHW
        $_SESSION['login_error'] = 'You must be logged in to access this page.';
        header('Location: ' . BASE_URL . 'login-bhw');
        exit();
    }
    
    // **Admin-only guard (requires admin or superadmin role)**
    if ($pageData['secure'] === 'admin') {
        if (!isset($_SESSION['bhw_id'])) {
            $_SESSION['login_error'] = 'You must be logged in to access this page.';
            header('Location: ' . BASE_URL . 'login-bhw');
            exit();
        }
        $userRole = $_SESSION['bhw_role'] ?? 'bhw';
        if (!in_array($userRole, ['admin', 'superadmin'])) {
            $_SESSION['login_error'] = 'You do not have permission to access this page.';
            header('Location: ' . BASE_URL . 'admin-dashboard');
            exit();
        }
    }
    
    // **Superadmin-only guard**
    if ($pageData['secure'] === 'superadmin') {
        if (!isset($_SESSION['bhw_id'])) {
            $_SESSION['login_error'] = 'You must be logged in to access this page.';
            header('Location: ' . BASE_URL . 'login-bhw');
            exit();
        }
        $userRole = $_SESSION['bhw_role'] ?? 'bhw';
        if ($userRole !== 'superadmin') {
            $_SESSION['login_error'] = 'Only Super Administrators can access this page.';
            header('Location: ' . BASE_URL . 'admin-dashboard');
            exit();
        }
    }
    
    // **This is our new 'Patient' guard**
    if ($pageData['secure'] === 'patient' && !isset($_SESSION['patient_id'])) {
        // Page is for Patients, but user is not logged in as a Patient
        $_SESSION['login_error'] = 'You must be logged in to access this page.';
        header('Location: ' . BASE_URL . 'login-patient');
        exit();
    }

    // If security checks pass, load the page
    if (file_exists($pageData['file'])) {
        include $pageData['file'];
    } else {
        include $allowedPages['404']['file'];
    }

} else {
    // Page not in whitelist, show 404
    include $allowedPages['404']['file'];
}