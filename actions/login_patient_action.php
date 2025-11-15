<?php
// Handle patient login (placeholder)
session_start();
// TODO: implement real authentication
$_SESSION['patient_logged_in'] = true;
header('Location: /pages/portal/portal_dashboard.php');
exit;
