<?php
/**
 * BHW Profile Save Action
 * Handles profile info update and photo upload for BHW users
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_helpers.php';

// Only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-profile');
    exit();
}

$bhw_id = isset($_POST['bhw_id']) ? (int)$_POST['bhw_id'] : 0;
$type = isset($_GET['type']) ? trim($_GET['type']) : '';

// Verify BHW owns this profile
if ($bhw_id <= 0 || $bhw_id !== ($_SESSION['bhw_id'] ?? 0)) {
    $_SESSION['form_error'] = 'Unauthorized action.';
    header('Location: ' . BASE_URL . 'admin-profile');
    exit();
}

try {
    switch ($type) {
        case 'info':
            // Update personal information
            $full_name = trim($_POST['full_name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $birthdate = trim($_POST['birthdate'] ?? '') ?: null;
            $sex = trim($_POST['sex'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $contact = trim($_POST['contact'] ?? '');
            $email = trim($_POST['email'] ?? '');
            
            if (empty($full_name)) {
                throw new Exception('Full name is required.');
            }
            
            if (empty($username)) {
                throw new Exception('Username is required.');
            }
            
            // Check if username is taken by another user
            $stmt = $pdo->prepare("SELECT bhw_id FROM bhw_users WHERE username = ? AND bhw_id != ?");
            $stmt->execute([$username, $bhw_id]);
            if ($stmt->fetch()) {
                throw new Exception('That username is already taken.');
            }
            
            $stmt = $pdo->prepare("UPDATE bhw_users SET full_name = ?, username = ?, birthdate = ?, sex = ?, address = ?, contact = ?, email = ? WHERE bhw_id = ?");
            $stmt->execute([$full_name, $username, $birthdate, $sex, $address, $contact, $email ?: null, $bhw_id]);
            
            // Update session name
            $_SESSION['bhw_full_name'] = $full_name;
            
            log_audit('update_profile', 'bhw', $bhw_id, ['type' => 'info']);
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
            $filename = 'bhw_' . $bhw_id . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;
            
            // Delete old photo if exists
            $stmt = $pdo->prepare("SELECT profile_photo FROM bhw_users WHERE bhw_id = ?");
            $stmt->execute([$bhw_id]);
            $oldPhoto = $stmt->fetchColumn();
            if ($oldPhoto && file_exists($uploadDir . $oldPhoto)) {
                unlink($uploadDir . $oldPhoto);
            }
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new Exception('Failed to save photo. Please try again.');
            }
            
            // Update database
            $stmt = $pdo->prepare("UPDATE bhw_users SET profile_photo = ? WHERE bhw_id = ?");
            $stmt->execute([$filename, $bhw_id]);
            
            log_audit('upload_photo', 'bhw', $bhw_id);
            $_SESSION['form_success'] = 'Profile photo updated!';
            break;
            
        default:
            throw new Exception('Invalid action type.');
    }
} catch (Exception $e) {
    $_SESSION['form_error'] = $e->getMessage();
}

// Redirect back to the referring page, or default to account-settings
$referer = $_SERVER['HTTP_REFERER'] ?? '';
if (strpos($referer, 'admin-profile') !== false) {
    header('Location: ' . BASE_URL . 'admin-profile');
} elseif (strpos($referer, 'admin-account-settings') !== false) {
    header('Location: ' . BASE_URL . 'admin-account-settings');
} else {
    header('Location: ' . BASE_URL . 'admin-account-settings');
}
exit();
