<?php
// Logout script
// Session is managed by the router; clear session data and redirect
// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to public home
header('Location: ' . BASE_URL . 'home');
exit();
