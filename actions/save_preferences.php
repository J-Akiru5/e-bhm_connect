<?php
/**
 * Save User Preferences API
 * Handles AJAX requests for user preference updates (theme, language)
 */

session_start();

// Check if logged in
if (!isset($_SESSION['bhw_id']) && !isset($_SESSION['patient_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_helpers.php';

header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

// Get current user info
$userId = $_SESSION['bhw_id'] ?? $_SESSION['patient_id'];
$userType = isset($_SESSION['bhw_id']) ? 'bhw' : 'patient';

// Get current preferences
$currentPrefs = get_user_preferences($userId, $userType);

// Update with new values
$newPrefs = [
    'theme' => $input['theme'] ?? $currentPrefs['theme'],
    'language' => $input['language'] ?? $currentPrefs['language'],
    'notifications_enabled' => $input['notifications_enabled'] ?? $currentPrefs['notifications_enabled'],
    'email_notifications' => $input['email_notifications'] ?? $currentPrefs['email_notifications'],
    'dashboard_widgets' => $input['dashboard_widgets'] ?? $currentPrefs['dashboard_widgets'],
];

// Save preferences
$success = save_user_preferences($newPrefs, $userId, $userType);

if ($success) {
    // Update session
    $_SESSION['theme'] = $newPrefs['theme'];
    $_SESSION['language'] = $newPrefs['language'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Preferences saved successfully',
        'preferences' => $newPrefs
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save preferences']);
}
