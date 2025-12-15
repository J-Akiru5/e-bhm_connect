<?php
/**
 * Medicine Dispensing Save Action
 * Handles medicine dispensing with proper transaction management
 * - Checks stock availability
 * - Deducts stock using row-level locking
 * - Logs dispensing record
 * - Handles errors with rollback
 */

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Only allow BHWs (admin) to record dispensing
if (!isset($_SESSION['bhw_id'])) {
    $_SESSION['flash_error'] = 'You must be logged in to record dispensing.';
    header('Location: ' . BASE_URL . 'login-bhw');
    exit;
}

// Validate and sanitize input
$resident_id = isset($_POST['resident_id']) ? (int)$_POST['resident_id'] : 0;
$item_id = isset($_POST['item_id']) && $_POST['item_id'] !== '' ? (int)$_POST['item_id'] : null;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$notes = isset($_POST['notes']) ? trim((string)$_POST['notes']) : '';
$bhw_id = (int)$_SESSION['bhw_id'];

// Validate required fields
if ($resident_id <= 0 || $quantity <= 0) {
    $_SESSION['flash_error'] = 'Invalid resident or quantity.';
    header('Location: ' . BASE_URL . 'admin-patient-view?id=' . $resident_id);
    exit;
}

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    // If an item_id is provided, check and deduct stock
    if ($item_id !== null && $item_id > 0) {
        // Lock the inventory row for update to prevent race conditions
        $stockStmt = $pdo->prepare("SELECT quantity FROM inventory WHERE item_id = :item_id FOR UPDATE");
        $stockStmt->execute([':item_id' => $item_id]);
        $stockRow = $stockStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$stockRow) {
            throw new Exception('Inventory item not found.');
        }
        
        $currentStock = (int)$stockRow['quantity'];
        
        // Check if sufficient stock is available
        if ($currentStock < $quantity) {
            throw new Exception('Insufficient stock. Available: ' . $currentStock . ', Requested: ' . $quantity);
        }
        
        // Deduct stock
        $newStock = $currentStock - $quantity;
        $updateStmt = $pdo->prepare("UPDATE inventory SET quantity = :quantity WHERE item_id = :item_id");
        $updateStmt->execute([
            ':quantity' => $newStock,
            ':item_id' => $item_id
        ]);
    }
    
    // Insert dispensing log
    $logStmt = $pdo->prepare(
        "INSERT INTO medicine_dispensing_log (resident_id, item_id, quantity, bhw_id, dispensed_at, notes) 
         VALUES (:resident_id, :item_id, :quantity, :bhw_id, NOW(), :notes)"
    );
    $logStmt->execute([
        ':resident_id' => $resident_id,
        ':item_id' => $item_id,
        ':quantity' => $quantity,
        ':bhw_id' => $bhw_id,
        ':notes' => $notes
    ]);
    
    // Commit transaction
    $pdo->commit();
    
    $_SESSION['flash_success'] = 'Medicine dispensed successfully. Stock updated.';
    
} catch (Exception $e) {
    // Rollback transaction on any error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log('Medicine dispense error: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Failed to dispense medicine: ' . $e->getMessage();
    
} catch (PDOException $e) {
    // Rollback transaction on database error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log('Medicine dispense database error: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Database error occurred. Please contact the administrator.';
}

// Redirect back to patient view
header('Location: ' . BASE_URL . 'admin-patient-view?id=' . $resident_id);
exit;
