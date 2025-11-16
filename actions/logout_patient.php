<?php
// actions/logout_patient.php
// (index.php handles session_start())

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
