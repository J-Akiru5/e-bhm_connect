<?php
// Start the session on every page
session_start();

// Include the database connection (this creates $pdo)
require_once __DIR__ . '/config/database.php';

// --- Main Router ---

// Get the requested page from the URL.
// Use 'home' as the default page if 'page' is not set or empty.
$page = isset($_GET['page']) && $_GET['page'] !== '' ? $_GET['page'] : 'home';

// Define the base path for our page files
$basePath = __DIR__ . '/pages/';

// Whitelist of all allowed pages and their file paths
$allowedPages = [
	// Public Pages
	'home' => $basePath . 'public/home.php',
	'contact' => $basePath . 'public/contact.php',
	'announcements' => $basePath . 'public/announcements.php',

	// Login Pages
	'login-bhw' => $basePath . 'login_bhw.php',
	'login-patient' => $basePath . 'login_patient.php',

	// BHW Admin Portal
	'admin-dashboard' => $basePath . 'admin/dashboard.php',
	'admin-patients' => $basePath . 'admin/patients.php',
	'admin-patient-view' => $basePath . 'admin/patient_view.php',
	'admin-patient-form' => $basePath . 'admin/patient_form.php',
	'admin-inventory' => $basePath . 'admin/inventory.php',
	'admin-reports' => $basePath . 'admin/reports.php',
	'admin-bhw-users' => $basePath . 'admin/bhw_users.php',

	// Patient Portal
	'portal-dashboard' => $basePath . 'portal/portal_dashboard.php',
	'portal-chatbot' => $basePath . 'portal/portal_chatbot.php',

	// Action/Error
	'404' => $basePath . 'public/404.php' // A page to show if 'page' is not found
];

// Check if the requested page is in our whitelist
if (array_key_exists($page, $allowedPages)) {
	// Check if the file actually exists before including it
	if (file_exists($allowedPages[$page])) {
		include $allowedPages[$page];
	} else {
		// File not found, even though it's in the whitelist (developer error)
		error_log("Missing page file: " . $allowedPages[$page]);
		include $allowedPages['404'];
	}
} else {
	// Page not in whitelist, show 404
	include $allowedPages['404'];
}

