<?php
/**
 * Visit Update Action - Edit existing health visit
 * E-BHM Connect
 * 
 * Handles editing health visit records with audit logging
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
    echo json_encode(['success' => false, 'message' => 'You do not have permission to edit visits']);
    exit;
}

// Get parameters
$visit_id = isset($_POST['visit_id']) ? (int) $_POST['visit_id'] : 0;
$visit_date = isset($_POST['visit_date']) && $_POST['visit_date'] !== '' ? trim($_POST['visit_date']) : null;
$visit_type = isset($_POST['visit_type']) ? trim($_POST['visit_type']) : '';
$remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';

// Validate required fields
if ($visit_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid visit ID']);
    exit;
}

if (empty($visit_type)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Visit type is required']);
    exit;
}

// Validate visit type is from allowed list
$allowed_types = ['Home Visit', 'Healthcare Visit', 'Follow-up Visit', 'Emergency Visit', 'Prenatal Care'];
if (!in_array($visit_type, $allowed_types)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid visit type']);
    exit;
}

try {
    // Get old values for audit trail
    $stmt = $pdo->prepare("SELECT * FROM health_visits WHERE visit_id = ?");
    $stmt->execute([$visit_id]);
    $old_record = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$old_record) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Visit record not found']);
        exit;
    }
    
    // Update the visit
    $stmt = $pdo->prepare("
        UPDATE health_visits 
        SET visit_date = ?, 
            visit_type = ?, 
            remarks = ?
        WHERE visit_id = ?
    ");
    
    $stmt->execute([
        $visit_date,
        $visit_type,
        $remarks,
        $visit_id
    ]);
    
    // Log the change to audit_logs
    $old_values = [
        'visit_date' => $old_record['visit_date'],
        'visit_type' => $old_record['visit_type'],
        'remarks' => $old_record['remarks']
    ];
    
    $new_values = [
        'visit_date' => $visit_date,
        'visit_type' => $visit_type,
        'remarks' => $remarks
    ];
    
    log_audit(
        'update_health_visit',
        'health_visit',
        $visit_id,
        [
            'patient_id' => $old_record['patient_id'],
            'old_values' => $old_values,
            'new_values' => $new_values
        ]
    );
    
    echo json_encode([
        'success' => true, 
        'message' => 'Visit updated successfully',
        'data' => [
            'visit_date' => $visit_date,
            'visit_type' => $visit_type,
            'remarks' => $remarks
        ]
    ]);
    
} catch (Throwable $e) {
    error_log('Visit update error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while updating the visit'
    ]);
}
exit;
