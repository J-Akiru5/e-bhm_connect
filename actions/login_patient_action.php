<?php
// Handle patient login (placeholder)
// Session is initialized by router; TODO: implement real authentication
$_SESSION['patient_logged_in'] = true;
header('Location: ' . BASE_URL . 'portal-dashboard');
exit;
