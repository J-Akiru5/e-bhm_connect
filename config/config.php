<?php
// config/config.php
// General configuration and bootstrap for the application.

// Application settings
define('APP_NAME', 'E-BHM-CONNECT');
define('APP_ENV', 'development');
define('BASE_URL', 'http://localhost/e-bhm_connect/');

// Include database connection (creates $pdo)
require_once __DIR__ . '/database.php';

// You can add other API keys or constants below, e.g.:
// define('GEMINI_API_KEY', 'your-key-here');

// Export a function to get the PDO instance if needed
function get_db()
{
	global $pdo;
	return $pdo;
}
