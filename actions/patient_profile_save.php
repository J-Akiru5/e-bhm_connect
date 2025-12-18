<?php
/**
 * Patient Profile Save Action
 * Handles profile info update, photo upload, and password change
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_helpers.php';

// Only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'portal-profile');
    exit();
}

$patient_id = isset($_POST['patient_id']) ? (int)$_POST['patient_id'] : 0;
$type = isset($_GET['type']) ? trim($_GET['type']) : '';

// Verify patient owns this profile
if ($patient_id <= 0 || $patient_id !== ($_SESSION['patient_id'] ?? 0)) {
    $_SESSION['form_error'] = 'Unauthorized action.';
    header('Location: ' . BASE_URL . 'portal-profile');
    exit();
}

try {
    switch ($type) {
        case 'info':
            // Update personal information
            $full_name = trim($_POST['full_name'] ?? '');
            $birthdate = trim($_POST['birthdate'] ?? '') ?: null;
            $sex = trim($_POST['sex'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $contact = trim($_POST['contact'] ?? '');
            
            if (empty($full_name)) {
                throw new Exception('Full name is required.');
            }
            
            $stmt = $pdo->prepare("UPDATE patients SET full_name = ?, birthdate = ?, sex = ?, address = ?, contact = ? WHERE patient_id = ?");
            $stmt->execute([$full_name, $birthdate, $sex, $address, $contact, $patient_id]);
            
            // Update session name
            $_SESSION['patient_full_name'] = $full_name;
            
            log_audit('update_profile', 'patient', $patient_id, ['type' => 'info']);
            $_SESSION['form_success'] = 'Profile updated successfully!';
            break;
            
        case 'photo':
            // Handle photo upload
            if (!isset($_FILES['profile_photo']) || $_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Please select a valid image file.');
            }
            
            $file = $_FILES['profile_photo'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            // Validate file type
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            if (!in_array($mimeType, $allowedTypes)) {
                throw new Exception('Invalid file type. Please upload JPG, PNG, GIF, or WebP.');
            }
            
            // Validate file size
            if ($file['size'] > $maxSize) {
                throw new Exception('File too large. Maximum size is 5MB.');
            }
            
            // Create upload directory if not exists
            $uploadDir = __DIR__ . '/../uploads/profiles/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'patient_' . $patient_id . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;
            
            // Delete old photo if exists
            $stmt = $pdo->prepare("SELECT profile_photo FROM patients WHERE patient_id = ?");
            $stmt->execute([$patient_id]);
            $oldPhoto = $stmt->fetchColumn();
            if ($oldPhoto && file_exists($uploadDir . $oldPhoto)) {
                unlink($uploadDir . $oldPhoto);
            }
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new Exception('Failed to save photo. Please try again.');
            }
            
            // Update database
            $stmt = $pdo->prepare("UPDATE patients SET profile_photo = ? WHERE patient_id = ?");
            $stmt->execute([$filename, $patient_id]);
            
            log_audit('upload_photo', 'patient', $patient_id);
            $_SESSION['form_success'] = 'Profile photo updated!';
            break;
            
        case 'password':
            // Handle password change
            $current = $_POST['current_password'] ?? '';
            $new = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            
            if (empty($current) || empty($new) || empty($confirm)) {
                throw new Exception('All password fields are required.');
            }
            
            if ($new !== $confirm) {
                throw new Exception('New passwords do not match.');
            }
            
            if (strlen($new) < 6) {
                throw new Exception('Password must be at least 6 characters.');
            }
            
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM patients WHERE patient_id = ?");
            $stmt->execute([$patient_id]);
            $hash = $stmt->fetchColumn();
            
            if (!$hash || !password_verify($current, $hash)) {
                throw new Exception('Current password is incorrect.');
            }
            
            // Update password
            $newHash = password_hash($new, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE patients SET password = ? WHERE patient_id = ?");
            $stmt->execute([$newHash, $patient_id]);
            
            log_audit('change_password', 'patient', $patient_id);
            $_SESSION['form_success'] = 'Password changed successfully!';
            break;
            
        case 'email':
            // Handle email update
            $email = trim($_POST['email'] ?? '');
            
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Please enter a valid email address.');
            }
            
            $stmt = $pdo->prepare("UPDATE patients SET email = ? WHERE patient_id = ?");
            $stmt->execute([$email ?: null, $patient_id]);
            
            log_audit('update_email', 'patient', $patient_id);
            $_SESSION['form_success'] = 'Email updated successfully!';
            break;
            
        default:
            throw new Exception('Invalid action type.');
    }
} catch (Exception $e) {
    $_SESSION['form_error'] = $e->getMessage();
}

header('Location: ' . BASE_URL . 'portal-profile');
exit();
