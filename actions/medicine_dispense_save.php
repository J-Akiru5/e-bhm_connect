<?php
// Save medicine dispensing record
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Only allow BHWs (admin) to record dispensing
if (!isset($_SESSION['bhw_id'])) {
    $_SESSION['flash_error'] = 'You must be logged in to record dispensing.';
    header('Location: ' . BASE_URL . 'login-bhw');
    exit;
}

$resident_id = isset($_POST['resident_id']) ? (int)$_POST['resident_id'] : 0;
$item_id = isset($_POST['item_id']) && $_POST['item_id'] !== '' ? (int)$_POST['item_id'] : null;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$notes = isset($_POST['notes']) ? trim((string)$_POST['notes']) : '';
$bhw_id = (int)$_SESSION['bhw_id'];

if ($resident_id <= 0 || $quantity <= 0) {
    $_SESSION['flash_error'] = 'Invalid resident or quantity.';
    header('Location: ' . BASE_URL . 'admin-patient-view?id=' . $resident_id);
    exit;
}

try {
    // Try to insert the record (table may not exist)
    $sql = "INSERT INTO medicine_dispensing_log (resident_id, item_id, quantity, bhw_id, dispensed_at, notes) VALUES (:resident_id, :item_id, :quantity, :bhw_id, NOW(), :notes)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':resident_id' => $resident_id,
        ':item_id' => $item_id,
        ':quantity' => $quantity,
        ':bhw_id' => $bhw_id,
        ':notes' => $notes
    ]);

    $_SESSION['flash_success'] = 'Dispensing record saved.';
} catch (PDOException $e) {
    error_log('medicine_dispense_save error: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Failed to save dispensing record. Please ensure the `medicine_dispensing_log` table exists.';
}

header('Location: ' . BASE_URL . 'admin-patient-view?id=' . $resident_id);
exit;
