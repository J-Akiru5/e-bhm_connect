<?php
// Logout script
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to public home (relative as requested)
header('Location: ../home');
exit();
