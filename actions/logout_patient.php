<?php
// actions/logout_patient.php
// (index.php handles session_start())
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';


// Unset all session variables
$_SESSION = array();

// Destroy the session
if (session_id() !== '') {
    session_destroy();
}

// Redirect to public home
header('Location: ' . BASE_URL . 'home');
exit();

?>
