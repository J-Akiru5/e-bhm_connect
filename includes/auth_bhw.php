<?php
// Security guard for BHW admin pages
session_start();

// If bhw_id is not present, redirect to login with a flash message
if (empty($_SESSION['bhw_id'])) {
    $_SESSION['login_error'] = 'You must be logged in to access this page.';
    header('Location: ../login-bhw');
    exit();
}
