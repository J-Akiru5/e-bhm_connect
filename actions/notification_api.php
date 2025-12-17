<?php
/**
 * Notification API
 * Handles CRUD operations for user notifications
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

// Get action from query parameter or POST
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        handleList();
        break;
        
    case 'mark_read':
        handleMarkRead();
        break;
        
    case 'mark_all_read':
        handleMarkAllRead();
        break;
        
    case 'delete':
        handleDelete();
        break;
        
    case 'count':
        handleCount();
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

/**
 * List notifications for current user
 */
function handleList()
{
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $unreadOnly = isset($_GET['unread_only']) && $_GET['unread_only'] === 'true';
    
    $notifications = get_notifications($limit, $unreadOnly);
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'count' => count($notifications)
    ]);
}

/**
 * Mark a single notification as read
 */
function handleMarkRead()
{
    $input = json_decode(file_get_contents('php://input'), true);
    $notificationId = $input['notification_id'] ?? $_POST['notification_id'] ?? 0;
    
    if (!$notificationId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Notification ID required']);
        return;
    }
    
    $success = mark_notification_read((int)$notificationId);
    
    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Notification marked as read' : 'Failed to mark notification'
    ]);
}

/**
 * Mark all notifications as read
 */
function handleMarkAllRead()
{
    $success = mark_all_notifications_read();
    
    echo json_encode([
        'success' => $success,
        'message' => $success ? 'All notifications marked as read' : 'Failed to mark notifications'
    ]);
}

/**
 * Delete a notification
 */
function handleDelete()
{
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $notificationId = $input['notification_id'] ?? $_POST['notification_id'] ?? 0;
    
    if (!$notificationId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Notification ID required']);
        return;
    }
    
    // Only allow deleting own notifications
    $userId = $_SESSION['bhw_id'] ?? $_SESSION['patient_id'];
    $userType = isset($_SESSION['bhw_id']) ? 'bhw' : 'patient';
    
    try {
        $stmt = $pdo->prepare("
            DELETE FROM notifications 
            WHERE notification_id = ? AND user_id = ? AND user_type = ?
        ");
        $success = $stmt->execute([$notificationId, $userId, $userType]);
        
        echo json_encode([
            'success' => $success && $stmt->rowCount() > 0,
            'message' => $success ? 'Notification deleted' : 'Failed to delete notification'
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

/**
 * Get unread notification count
 */
function handleCount()
{
    $count = get_unread_notification_count();
    
    echo json_encode([
        'success' => true,
        'count' => $count
    ]);
}
