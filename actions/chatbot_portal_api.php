<?php
// actions/chatbot_portal_api.php
header('Content-Type: application/json');

// Ensure POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['reply' => 'Invalid request method.']);
    exit();
}

// Session and DB are provided by index.php router
$patient_user_id = $_SESSION['patient_user_id'] ?? 0;
if (empty($patient_user_id)) {
    echo json_encode(['reply' => 'Error: You are not logged in.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$user_message = trim($data['message'] ?? '');
if ($user_message === '') {
    echo json_encode(['reply' => 'No message received.']);
    exit();
}

// Ensure API key available
$api_key = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : null;
if (empty($api_key)) {
    echo json_encode(['reply' => "(No API key configured) Gabby: Sorry, the chat service is not available right now."]); 
    exit();
}

// Build prompt
$prompt = "You are 'Gabby', a helpful health assistant for Barangay Bacong. Answer briefly and friendly. Do not provide medical diagnoses. If asked for a diagnosis, advise the user to see a BHW. Question: " . $user_message;

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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // local dev only

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($response === false || $err) {
    error_log('Chatbot portal API request error: ' . $err);
    echo json_encode(['reply' => 'Sorry, I could not contact the chat service.']);
    exit();
}

$response_data = json_decode($response, true);
$bot_reply = $response_data['candidates'][0]['content']['parts'][0]['text'] ?? ($response_data['candidates'][0]['output'] ?? 'Sorry, I could not process that request.');

// Save to DB
try {
    $stmt = $pdo->prepare("INSERT INTO chatbot_history (user_id, prompt_text, response_text) VALUES (?, ?, ?)");
    $stmt->execute([$patient_user_id, $user_message, $bot_reply]);
} catch (PDOException $e) {
    error_log('Chatbot portal save failed: ' . $e->getMessage());
}

echo json_encode(['reply' => $bot_reply]);
exit();

?>
