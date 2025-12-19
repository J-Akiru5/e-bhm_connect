<?php
// actions/medicine_dispense_save.php
// Handles medicine dispensing with stock checks and logging
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_helpers.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . (defined('BASE_URL') ? BASE_URL : '/e-bhm_connect/') . 'admin-patients');
    exit();
}

// Input sanitization
$patient_id = isset($_POST['patient_id']) ? (int) $_POST['patient_id'] : 0;
$medicine_id = 0;
if (isset($_POST['medicine_id'])) {
    $medicine_id = (int) $_POST['medicine_id'];
} elseif (isset($_POST['item_id'])) {
    $medicine_id = (int) $_POST['item_id'];
}
$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0;
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : null;
$bhw_id = isset($_SESSION['bhw_id']) ? (int) $_SESSION['bhw_id'] : null;

// Helper for Redirect
$redirect_url = (defined('BASE_URL') ? BASE_URL : '/e-bhm_connect/') . 'admin-patient-view?id=' . $patient_id;

// 4. Validation
if ($patient_id <= 0 || $medicine_id <= 0) {
    $_SESSION['form_error'] = 'Error: Missing patient or medicine selection.';
    header('Location: ' . $redirect_url);
    exit();
}

if ($quantity <= 0) {
    $_SESSION['form_error'] = 'Error: Quantity must be greater than zero.';
    header('Location: ' . $redirect_url);
    exit();
}

try {
    // 5. Database Transaction
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        throw new RuntimeException('Database connection not available.');
    }

    $pdo->beginTransaction();

    // Step A: Lock & Check Stock
    $sel = $pdo->prepare('SELECT quantity_in_stock FROM medication_inventory WHERE item_id = :id FOR UPDATE');
    $sel->execute([':id' => $medicine_id]);
    $row = $sel->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $pdo->rollBack();
        $_SESSION['form_error'] = 'Error: Medicine not found.';
        header('Location: ' . $redirect_url);
        exit();
    }

    if ((int)$row['quantity_in_stock'] < $quantity) {
        $pdo->rollBack();
        $_SESSION['form_error'] = 'Error: Insufficient stock.';
        header('Location: ' . $redirect_url);
        exit();
    }

    // Step B: Deduct Stock
    $upd = $pdo->prepare('UPDATE medication_inventory SET quantity_in_stock = quantity_in_stock - :qty WHERE item_id = :id');
    $upd->execute([':qty' => $quantity, ':id' => $medicine_id]);

    // Step C: Log Dispense
    // Use resident_id column to match actual database schema
    $ins = $pdo->prepare('INSERT INTO medicine_dispensing_log (resident_id, item_id, quantity, bhw_id, dispensed_at, notes) VALUES (:pid, :mid, :qty, :bid, NOW(), :notes)');
    $ins->execute([
        ':pid' => $patient_id,
        ':mid' => $medicine_id,
        ':qty' => $quantity,
        ':bid' => $bhw_id,
        ':notes' => $notes
    ]);

    // 6. Commit
    $pdo->commit();
    log_audit('dispense_medicine', 'inventory', $medicine_id, ['patient_id' => $patient_id, 'quantity' => $quantity]);

    $_SESSION['form_success'] = 'Success: Medicine dispensed.';
    header('Location: ' . $redirect_url);
    exit();

} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Log detailed error for debugging
    error_log('Dispense Error [medicine_dispense_save.php]: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
    // Show more detailed error during debugging (remove in production)
    $_SESSION['form_error'] = 'System Error: Could not dispense. Details: ' . $e->getMessage();
    header('Location: ' . $redirect_url);
    exit();
}
