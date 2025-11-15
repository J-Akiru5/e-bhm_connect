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

    // Whitelist of allowed action files
    $allowedActions = [
        'login-bhw' => $actionPath . 'login_bhw_action.php',
        'register-bhw' => $actionPath . 'register_bhw_action.php',
        'logout' => $actionPath . 'logout.php',
        'save-patient' => $actionPath . 'patient_save.php',
        'delete-patient' => $actionPath . 'patient_delete.php',
        'save-inventory-item' => $actionPath . 'inventory_save.php',
        'delete-inventory-item' => $actionPath . 'inventory_delete.php',
        'update-inventory-item' => $actionPath . 'inventory_update.php'
        ,
        'save-vital' => $actionPath . 'vital_save.php',
        'save-visit' => $actionPath . 'visit_save.php'
        // Add other actions here as we create them
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
    'announcements' => ['file' => $basePath . 'public/announcements.php', 'secure' => false],

    // Login/Register
    'login-bhw' => ['file' => $basePath . 'login_bhw.php', 'secure' => false],
    'register-bhw' => ['file' => $basePath . 'register_bhw.php', 'secure' => false],

    // BHW Admin Portal
    'admin-dashboard' => ['file' => $basePath . 'admin/dashboard.php', 'secure' => true],
    'admin-patients' => ['file' => $basePath . 'admin/patients.php', 'secure' => true],
    'admin-patient-view' => ['file' => $basePath . 'admin/patient_view.php', 'secure' => true],
    'admin-patient-form' => ['file' => $basePath . 'admin/patient_form.php', 'secure' => true],
    'admin-inventory' => ['file' => $basePath . 'admin/inventory.php', 'secure' => true],
    'admin-inventory-edit' => ['file' => $basePath . 'admin/inventory_edit.php', 'secure' => true],
    'admin-reports' => ['file' => $basePath . 'admin/reports.php', 'secure' => true],
    'admin-bhw-users' => ['file' => $basePath . 'admin/bhw_users.php', 'secure' => true],

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
    
    // (We can add a check for 'patient' security here later)

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