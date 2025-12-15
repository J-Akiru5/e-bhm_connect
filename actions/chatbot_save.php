<?php
// actions/chatbot_save.php
// Save chatbot conversation history to DB

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
	exit();
}

// Ensure session is available
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Expect JSON payload
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
	http_response_code(400);
	echo json_encode(['status' => 'error', 'message' => 'Invalid JSON payload']);
	exit();
}

$message = isset($data['message']) ? trim($data['message']) : '';
$response = isset($data['response']) ? trim($data['response']) : '';

if ($message === '') {
	http_response_code(400);
	echo json_encode(['status' => 'error', 'message' => 'Missing message']);
	exit();
}

// Determine user_id if logged in
$user_id = null;
if (!empty($_SESSION['user_id'])) {
	$user_id = (int) $_SESSION['user_id'];
}

try {
	// $pdo is provided by the app bootstrap (config/database.php)
	global $pdo;

	$stmt = $pdo->prepare('INSERT INTO chatbot_history (user_id, prompt_text, response_text) VALUES (:user_id, :prompt, :response)');
	// bind user_id as NULL or int
	if ($user_id === null) {
		$stmt->bindValue(':user_id', null, PDO::PARAM_NULL);
	} else {
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
	}
	$stmt->bindValue(':prompt', $message, PDO::PARAM_STR);
	$stmt->bindValue(':response', $response, PDO::PARAM_STR);

	$stmt->execute();

	echo json_encode(['status' => 'success']);
	exit();
} catch (Throwable $e) {
	error_log('chatbot_save error: ' . $e->getMessage());
	http_response_code(500);
	if (defined('APP_ENV') && APP_ENV === 'development') {
		echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Internal server error']);
	}
	exit();
}
