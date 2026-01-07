<?php
/**
 * Vital Update Action - Edit existing patient vital signs
 * E-BHM Connect
 * 
 * Handles editing vital sign records with audit logging
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/security_helper.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Validate CSRF token
if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

// Verify permissions: superadmin or has manage_patients permission
if (!is_superadmin() && !has_permission('manage_patients')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'You do not have permission to edit vitals']);
    exit;
}

// Get parameters
$vital_id = isset($_POST['vital_id']) ? (int) $_POST['vital_id'] : 0;
$blood_pressure = isset($_POST['blood_pressure']) ? trim($_POST['blood_pressure']) : null;
$heart_rate = isset($_POST['heart_rate']) && $_POST['heart_rate'] !== '' ? (int) $_POST['heart_rate'] : null;
$temperature = isset($_POST['temperature']) && $_POST['temperature'] !== '' ? (float) $_POST['temperature'] : null;
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

// Validate required fields
if ($vital_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid vital ID']);
    exit;
}

try {
    // Get old values for audit trail
    $stmt = $pdo->prepare("SELECT * FROM patient_vitals WHERE vital_id = ?");
    $stmt->execute([$vital_id]);
    $old_record = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$old_record) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Vital record not found']);
        exit;
    }
    
    // Update the vital signs
    $stmt = $pdo->prepare("
        UPDATE patient_vitals 
        SET blood_pressure = ?, 
            heart_rate = ?, 
            temperature = ?,
            notes = ?
        WHERE vital_id = ?
    ");
    
    $stmt->execute([
        $blood_pressure,
        $heart_rate,
        $temperature,
        $notes,
        $vital_id
    ]);
    
    // Log the change to audit_logs
    $old_values = [
        'blood_pressure' => $old_record['blood_pressure'],
        'heart_rate' => $old_record['heart_rate'],
        'temperature' => $old_record['temperature'],
        'notes' => $old_record['notes']
    ];
    
    $new_values = [
        'blood_pressure' => $blood_pressure,
        'heart_rate' => $heart_rate,
        'temperature' => $temperature,
        'notes' => $notes
    ];
    
    log_audit(
        'update_patient_vital',
        'patient_vital',
        $vital_id,
        [
            'patient_id' => $old_record['patient_id'],
            'old_values' => $old_values,
            'new_values' => $new_values
        ]
    );
    
    echo json_encode([
        'success' => true, 
        'message' => 'Vital signs updated successfully',
        'data' => [
            'blood_pressure' => $blood_pressure,
            'heart_rate' => $heart_rate,
            'temperature' => $temperature,
            'notes' => $notes
        ]
    ]);
    
} catch (Throwable $e) {
    error_log('Vital update error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while updating the vital signs'
    ]);
}
exit;
