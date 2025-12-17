<?php
/**
 * Health Records - Unified Delete Action Handler
 * E-BHM Connect
 * 
 * Handles all health record deletion operations.
 */

require_once __DIR__ . '/../includes/auth_bhw.php';
require_once __DIR__ . '/../config/database.php';

// Get the action type from the URL
$action = $_GET['action'] ?? '';

// Map action to table and redirect
$actionMap = [
    'delete-pregnancy-tracking' => [
        'table' => 'pregnancy_tracking',
        'pk' => 'pregnancy_id',
        'redirect' => 'admin-health-records-pregnancy',
        'name' => 'Pregnancy'
    ],
    'delete-childcare-record' => [
        'table' => 'child_care_records',
        'pk' => 'child_care_id',
        'redirect' => 'admin-health-records-childcare',
        'name' => 'Child care'
    ],
    'delete-natality-record' => [
        'table' => 'natality_records',
        'pk' => 'natality_id',
        'redirect' => 'admin-health-records-natality',
        'name' => 'Natality'
    ],
    'delete-mortality-record' => [
        'table' => 'mortality_records',
        'pk' => 'mortality_id',
        'redirect' => 'admin-health-records-mortality',
        'name' => 'Mortality'
    ],
    'delete-chronic-disease' => [
        'table' => 'chronic_disease_masterlist',
        'pk' => 'chronic_id',
        'redirect' => 'admin-health-records-chronic',
        'name' => 'Chronic disease'
    ],
    'delete-ntp-client' => [
        'table' => 'ntp_client_monitoring',
        'pk' => 'ntp_id',
        'redirect' => 'admin-health-records-ntp',
        'name' => 'NTP client'
    ],
    'delete-wra-tracking' => [
        'table' => 'wra_tracking',
        'pk' => 'wra_id',
        'redirect' => 'admin-health-records-wra',
        'name' => 'WRA tracking'
    ]
];

if (!array_key_exists($action, $actionMap)) {
    $_SESSION['error'] = 'Invalid delete action.';
    header('Location: ' . BASE_URL . 'admin-health-records');
    exit;
}

$config = $actionMap[$action];
$id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : null);

if (!$id) {
    $_SESSION['error'] = 'Invalid record ID.';
    header('Location: ' . BASE_URL . $config['redirect']);
    exit;
}

try {
    $sql = "DELETE FROM {$config['table']} WHERE {$config['pk']} = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = "{$config['name']} record deleted successfully.";
    } else {
        $_SESSION['error'] = 'Record not found or already deleted.';
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
}

header('Location: ' . BASE_URL . $config['redirect']);
exit;
