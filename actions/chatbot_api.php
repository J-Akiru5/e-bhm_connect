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

// --- NEW CONTEXT-AWARE PROMPT ---
$prompt = "";

// Ensure session is available (index.php normally starts session)
if (session_status() === PHP_SESSION_NONE) {
	@session_start();
}

if (isset($_SESSION['bhw_id'])) {
	// ---- BHW-SPECIFIC PROMPT ----
	$barangay_context = "
	You are 'Gabby', a helpful AI assistant for Barangay Bacong.
	You are currently speaking to a **Barangay Health Worker (BHW)**, who is a trained community health professional.
	Your tone should be helpful, professional, and peer-to-peer.
	- You CAN provide general health information and first-aid procedures, as you are speaking to someone with training.
	- DO NOT make specific diagnoses for patients the BHW might describe. Instead, provide information to help the BHW make their own assessment.
	- You can answer questions about the Health Center (Hours: 8 AM-5 PM, Mon-Fri; Contact: (033) 123-4567).
	";
	$prompt = $barangay_context . "\n    Based on all the rules and information above, please answer this BHW's question: " . $user_message;
    
} else {
	// ---- PUBLIC PROMPT (No Change) ----
	$barangay_context = "
	You are 'Gabby', a helpful health assistant for Barangay Bacong.
	Your purpose is to answer general health questions and provide information about the barangay health center.
	You must follow these rules:
	1.  **DO NOT** provide medical diagnoses or advice.
	2.  If asked for a diagnosis, advise the user to see a BHW (Barangay Health Worker) at the health center.
	3.  **DO NOT** pretend to have access to personal patient data.
	4.  Here is information about the barangay you can use:
		- Health Center: Bacong Barangay Health Center
		- Location: Bacong, Dumangas, Iloilo
		- Office Hours: Monday - Friday, 8:00 AM to 5:00 PM
		- Contact: (033) 123-4567
	";
	$prompt = $barangay_context . "\n    Based on all the rules and information above, please answer this user's question: " . $user_message;
}
// --- END NEW PROMPT ---

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
