<?php
// Handle BHW login (placeholder)
// Validate credentials, set session, redirect
session_start();
// TODO: implement real authentication
$_SESSION['bhw_logged_in'] = true;
header('Location: /pages/admin/dashboard.php');
exit;
