<?php
// Logout script
// Session is managed by the router; clear session data and redirect
// Unset all session variables
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to public home
header('Location: ' . BASE_URL . 'home');
exit();
