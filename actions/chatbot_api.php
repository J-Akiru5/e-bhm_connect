<?php
// actions/chatbot_api.php
// Backend endpoint to relay user messages to Gemini API and return a simple reply

header('Content-Type: application/json');

// Ensure request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	echo json_encode(['reply' => 'Invalid request method.']);
	exit();
}

// Read JSON payload
$body = file_get_contents('php://input');
$data = json_decode($body, true);
$user_message = trim($data['message'] ?? '');

if ($user_message === '') {
	echo json_encode(['reply' => 'No message received.']);
	exit();
}

// Load API key from config if available
if (!defined('GEMINI_API_KEY') && defined('GEMINI_API_KEY') === false) {
	// Try to load config
	if (file_exists(__DIR__ . '/../config/config.php')) {
		require_once __DIR__ . '/../config/config.php';
	}
}

$api_key = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : (defined('GEMINI_API_KEY') ? GEMINI_API_KEY : null);

if (empty($api_key)) {
	// If API key missing, return canned response (safe fallback)
	echo json_encode(['reply' => "(No API key configured) Gabby: Sorry, the chat service is not available right now."]); 
	exit();
}

// Build prompt
$prompt = "You are 'Gabby', a helpful health assistant for Barangay Bacong. Answer briefly and friendly. Do not provide medical diagnoses. If asked for a diagnosis, advise the user to see a BHW. Question: " . $user_message;

// Gemini REST endpoint (using API key param)
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . urlencode($api_key);

$payload = [
	'contents' => [
		[
			'parts' => [
				['text' => $prompt]
			]
		]
	]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
// For local development, skip SSL verification if necessary. In production remove following line
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($response === false || $err) {
	error_log('Chatbot API request error: ' . $err);
	echo json_encode(['reply' => 'Sorry, I could not contact the chat service.']);
	exit();
}

$response_data = json_decode($response, true);

// Try extracting the reply (best effort)
$reply = null;
if (isset($response_data['candidates'][0]['content']['parts'][0]['text'])) {
	$reply = $response_data['candidates'][0]['content']['parts'][0]['text'];
} elseif (isset($response_data['candidates'][0]['output'])) {
	// another possible structure
	$reply = is_string($response_data['candidates'][0]['output']) ? $response_data['candidates'][0]['output'] : json_encode($response_data['candidates'][0]['output']);
}

if (!$reply) {
	$reply = 'Sorry, I could not process that request.';
}

echo json_encode(['reply' => $reply]);
exit();
