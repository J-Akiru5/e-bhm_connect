<?php
// Simple Patient auth placeholder
session_start();
if (empty($_SESSION['patient_logged_in'])) {
    header('Location: /pages/login_patient.php');
    exit;
}
