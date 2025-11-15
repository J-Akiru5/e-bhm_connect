<?php
// Simple BHW auth placeholder
session_start();
if (empty($_SESSION['bhw_logged_in'])) {
    header('Location: /pages/login_bhw.php');
    exit;
}
