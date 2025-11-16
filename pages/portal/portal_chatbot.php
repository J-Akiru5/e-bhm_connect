<?php
// pages/portal/portal_chatbot.php
// Patient portal chat with Gabby
include_once __DIR__ . '/../../includes/header_portal.php';

$patient_user_id = $_SESSION['patient_user_id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM chatbot_history WHERE user_id = ? ORDER BY timestamp ASC");
$stmt->execute([$patient_user_id]);
$chat_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/chatbot.css">

<div class="container mt-4">
    <div class="card shadow-sm">
        <div id="chat-header" class="card-header">
            Chat with Gabby
        </div>
        <div id="chat-messages" style="height: 500px; overflow:auto; padding:1rem;">
            <div class="chat-message bot">
                Hi <?php echo htmlspecialchars($_SESSION['patient_full_name'] ?? ''); ?>! Ask me any general health questions.
            </div>

            <script>
                const chatHistory = <?php echo json_encode($chat_history); ?>;
            </script>
        </div>
        <div id="chat-input-area" style="display:flex; gap:.5rem; padding:1rem;">
            <input type="text" id="chat-input" class="form-control" placeholder="Type your message...">
            <button id="chat-send-btn" class="btn btn-success">→</button>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer_portal.php'; ?>
