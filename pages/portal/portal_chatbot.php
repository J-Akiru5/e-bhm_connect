<?php
// pages/portal/portal_chatbot.php
// Patient portal chat with Gabby - Modernized
include_once __DIR__ . '/../../includes/header_portal.php';

$patient_id = $_SESSION['patient_id'] ?? 0;
$patient_user_id = $_SESSION['patient_user_id'] ?? $patient_id; // Use either ID
$patient_name = $_SESSION['patient_full_name'] ?? 'there';

// Get chat history - columns are prompt_text (user) and response_text (bot)
$stmt = $pdo->prepare("SELECT prompt_text, response_text, timestamp FROM chatbot_history WHERE user_id = ? ORDER BY timestamp ASC LIMIT 50");
$stmt->execute([$patient_user_id]);
$chat_history_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convert to format JS expects
$chat_history = [];
foreach ($chat_history_raw as $row) {
    if (!empty($row['prompt_text'])) {
        $chat_history[] = ['text' => $row['prompt_text'], 'sender' => 'user'];
    }
    if (!empty($row['response_text'])) {
        $chat_history[] = ['text' => $row['response_text'], 'sender' => 'bot'];
    }
}
?>

<style>
/* Modern Chat Styles */
.chat-container {
    max-width: 900px;
    margin: 0 auto;
}

.chat-hero {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: var(--radius-2xl);
    padding: var(--space-6);
    margin-bottom: var(--space-4);
    color: white;
    display: flex;
    align-items: center;
    gap: var(--space-4);
    position: relative;
    overflow: hidden;
}

.chat-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 40%;
    height: 200%;
    background: rgba(255,255,255,0.08);
    transform: rotate(15deg);
    pointer-events: none;
}

.gabby-avatar-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 3px solid rgba(255,255,255,0.3);
    object-fit: cover;
    background: rgba(255,255,255,0.1);
}

.chat-hero-info h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: var(--space-1);
}

.chat-hero-info p {
    opacity: 0.9;
    font-size: var(--font-size-sm);
    margin: 0;
}

.chat-card {
    background: var(--white);
    border-radius: var(--radius-xl);
    border: 1px solid var(--gray-200);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
}

.chat-messages {
    height: 450px;
    overflow-y: auto;
    padding: var(--space-6);
    background: var(--gray-50);
}

.chat-message {
    display: flex;
    gap: var(--space-3);
    margin-bottom: var(--space-4);
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.chat-message.user {
    flex-direction: row-reverse;
}

.chat-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    flex-shrink: 0;
    object-fit: cover;
}

.chat-bubble {
    max-width: 70%;
    padding: var(--space-4);
    border-radius: var(--radius-xl);
    font-size: var(--font-size-sm);
    line-height: 1.5;
}

.chat-message.bot .chat-bubble {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-top-left-radius: var(--radius-sm);
    box-shadow: var(--shadow-sm);
}

.chat-message.user .chat-bubble {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    border-top-right-radius: var(--radius-sm);
}

/* Markdown content styling inside chat bubbles */
.chat-bubble h1, .chat-bubble h2, .chat-bubble h3 {
    margin: 0 0 var(--space-2);
    font-weight: 700;
    color: var(--gray-800);
    line-height: 1.3;
}
.chat-bubble h1 { font-size: 1.15rem; }
.chat-bubble h2 { font-size: 1.05rem; }
.chat-bubble h3 { font-size: 0.95rem; }

.chat-bubble p { margin: 0 0 var(--space-3); }
.chat-bubble p:last-child { margin-bottom: 0; }

.chat-bubble ul, .chat-bubble ol {
    margin: var(--space-2) 0 var(--space-3);
    padding-left: var(--space-5);
}
.chat-bubble li {
    margin-bottom: var(--space-1);
    line-height: 1.5;
}
.chat-bubble li::marker { color: var(--primary); }

.chat-bubble strong { font-weight: 600; color: var(--gray-800); }
.chat-bubble em { font-style: italic; }

.chat-bubble code {
    background: rgba(32, 201, 151, 0.1);
    padding: 2px 6px;
    border-radius: var(--radius-sm);
    font-family: 'Courier New', monospace;
    font-size: 0.85em;
    color: var(--primary-dark);
}

.chat-bubble pre {
    background: var(--gray-100);
    padding: var(--space-3);
    border-radius: var(--radius-md);
    overflow-x: auto;
    margin: var(--space-3) 0;
}
.chat-bubble pre code {
    background: none;
    padding: 0;
    font-size: 0.8rem;
}

.chat-bubble blockquote {
    border-left: 3px solid var(--primary);
    padding-left: var(--space-3);
    margin: var(--space-3) 0;
    color: var(--gray-600);
    font-style: italic;
}

.chat-bubble hr {
    border: none;
    border-top: 1px solid var(--gray-200);
    margin: var(--space-3) 0;
}

.chat-bubble a {
    color: var(--primary);
    text-decoration: underline;
}

/* Dark mode markdown */
[data-theme="dark"] .chat-bubble h1,
[data-theme="dark"] .chat-bubble h2,
[data-theme="dark"] .chat-bubble h3,
[data-theme="dark"] .chat-bubble strong { color: var(--text-primary); }
[data-theme="dark"] .chat-bubble pre { background: rgba(0,0,0,0.2); }
[data-theme="dark"] .chat-bubble code { background: rgba(32, 201, 151, 0.15); }

.chat-timestamp {
    font-size: var(--font-size-xs);
    color: var(--gray-400);
    margin-top: var(--space-1);
}

.chat-message.user .chat-timestamp {
    text-align: right;
    color: rgba(255,255,255,0.7);
}

.chat-input-area {
    display: flex;
    gap: var(--space-3);
    padding: var(--space-4) var(--space-6);
    background: var(--white);
    border-top: 1px solid var(--gray-200);
}

.chat-input {
    flex: 1;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-full);
    padding: var(--space-3) var(--space-5);
    font-size: var(--font-size-sm);
    transition: all var(--transition-fast);
}

.chat-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(32, 201, 151, 0.15);
}

.chat-send-btn {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition-fast);
}

.chat-send-btn:hover {
    transform: scale(1.05);
    box-shadow: var(--shadow-lg);
}

.chat-send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.typing-indicator {
    display: none;
    gap: var(--space-1);
    padding: var(--space-3) var(--space-4);
    background: var(--white);
    border-radius: var(--radius-xl);
    border: 1px solid var(--gray-200);
    width: fit-content;
}

.typing-indicator.show {
    display: flex;
}

.typing-dot {
    width: 8px;
    height: 8px;
    background: var(--gray-400);
    border-radius: 50%;
    animation: typingBounce 1.4s infinite ease-in-out both;
}

.typing-dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes typingBounce {
    0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
    40% { transform: scale(1); opacity: 1; }
}

/* Quick suggestions */
.quick-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-2);
    margin-top: var(--space-3);
}

.suggestion-chip {
    padding: var(--space-2) var(--space-4);
    background: rgba(32, 201, 151, 0.1);
    color: var(--primary-dark);
    border: 1px solid var(--primary);
    border-radius: var(--radius-full);
    font-size: var(--font-size-xs);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.suggestion-chip:hover {
    background: var(--primary);
    color: white;
}
</style>

<div class="chat-container">
    <!-- Chat Hero -->
    <div class="chat-hero">
        <img src="<?php echo BASE_URL; ?>assets/images/gabby-head.png" alt="Gabby" class="gabby-avatar-large">
        <div class="chat-hero-info">
            <h1>Chat with Gabby</h1>
            <p>Your friendly health assistant. Ask me anything about general health topics!</p>
        </div>
    </div>

    <!-- Chat Card -->
    <div class="chat-card">
        <div id="chat-messages" class="chat-messages">
            <!-- Welcome message -->
            <div class="chat-message bot">
                <img src="<?php echo BASE_URL; ?>assets/images/gabby-head.png" alt="Gabby" class="chat-avatar">
                <div>
                    <div class="chat-bubble">
                        Hi <?php echo htmlspecialchars($patient_name); ?>! 👋 I'm Gabby, your health assistant. How can I help you today?
                        <div class="quick-suggestions">
                            <span class="suggestion-chip" onclick="sendSuggestion('What are symptoms of flu?')">Flu symptoms</span>
                            <span class="suggestion-chip" onclick="sendSuggestion('How to lower blood pressure?')">Blood pressure</span>
                            <span class="suggestion-chip" onclick="sendSuggestion('Tips for better sleep')">Sleep tips</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Typing indicator -->
            <div class="chat-message bot">
                <img src="<?php echo BASE_URL; ?>assets/images/gabby-head.png" alt="Gabby" class="chat-avatar">
                <div class="typing-indicator" id="typing-indicator">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
        </div>

        <div class="chat-input-area">
            <input type="text" id="chat-input" class="chat-input" placeholder="Type your health question..." autocomplete="off">
            <button id="chat-send-btn" class="chat-send-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
const chatMessages = document.getElementById('chat-messages');
const chatInput = document.getElementById('chat-input');
const sendBtn = document.getElementById('chat-send-btn');
const typingIndicator = document.getElementById('typing-indicator');
const gabbyAvatar = '<?php echo BASE_URL; ?>assets/images/gabby-head.png';
const baseUrl = '<?php echo BASE_URL; ?>';

// Simple markdown parser for chat messages
function parseMarkdown(text) {
    if (!text) return '';
    
    // Escape HTML first
    text = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    
    // Headers (## and ###)
    text = text.replace(/^### (.+)$/gm, '<h3>$1</h3>');
    text = text.replace(/^## (.+)$/gm, '<h2>$1</h2>');
    text = text.replace(/^# (.+)$/gm, '<h1>$1</h1>');
    
    // Bold and Italic
    text = text.replace(/\*\*\*(.+?)\*\*\*/g, '<strong><em>$1</em></strong>');
    text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    text = text.replace(/\*(.+?)\*/g, '<em>$1</em>');
    
    // Inline code
    text = text.replace(/`([^`]+)`/g, '<code>$1</code>');
    
    // Unordered lists (- or *)
    text = text.replace(/^[\-\*] (.+)$/gm, '<li>$1</li>');
    text = text.replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>');
    
    // Numbered lists
    text = text.replace(/^\d+\. (.+)$/gm, '<li>$1</li>');
    
    // Group consecutive <li> elements into <ul>
    text = text.replace(/(<li>[\s\S]*?<\/li>)(?=\s*<li>)/g, '$1');
    text = text.replace(/((?:<li>[\s\S]*?<\/li>\s*)+)/g, '<ul>$1</ul>');
    
    // Blockquotes
    text = text.replace(/^&gt; (.+)$/gm, '<blockquote>$1</blockquote>');
    
    // Horizontal rules
    text = text.replace(/^---$/gm, '<hr>');
    
    // Line breaks - convert double newlines to paragraphs
    text = text.replace(/\n\n+/g, '</p><p>');
    text = text.replace(/\n/g, '<br>');
    
    // Wrap in paragraph if not already wrapped
    if (!text.startsWith('<h') && !text.startsWith('<ul') && !text.startsWith('<ol') && !text.startsWith('<blockquote')) {
        text = '<p>' + text + '</p>';
    }
    
    // Clean up empty paragraphs
    text = text.replace(/<p><\/p>/g, '');
    text = text.replace(/<p>(<h[123]>)/g, '$1');
    text = text.replace(/(<\/h[123]>)<\/p>/g, '$1');
    text = text.replace(/<p>(<ul>)/g, '$1');
    text = text.replace(/(<\/ul>)<\/p>/g, '$1');
    text = text.replace(/<p>(<blockquote>)/g, '$1');
    text = text.replace(/(<\/blockquote>)<\/p>/g, '$1');
    text = text.replace(/<p><br>/g, '<p>');
    text = text.replace(/<br><\/p>/g, '</p>');
    
    return text;
}

// Load existing chat history
const chatHistory = <?php echo json_encode($chat_history); ?>;
if (chatHistory && chatHistory.length > 0) {
    chatHistory.forEach(msg => {
        if (msg.text) {
            addMessage(msg.text, msg.sender === 'user' ? 'user' : 'bot', false);
        }
    });
    scrollToBottom();
}

function addMessage(text, sender, animate = true) {
    const div = document.createElement('div');
    div.className = `chat-message ${sender}`;
    
    // Parse markdown for bot messages only
    const displayText = sender === 'bot' ? parseMarkdown(text) : text.replace(/</g, '&lt;').replace(/>/g, '&gt;');
    
    if (sender === 'bot') {
        div.innerHTML = `
            <img src="${gabbyAvatar}" alt="Gabby" class="chat-avatar">
            <div>
                <div class="chat-bubble">${displayText}</div>
            </div>
        `;
    } else {
        div.innerHTML = `
            <div>
                <div class="chat-bubble">${displayText}</div>
            </div>
        `;
    }
    
    // Insert before typing indicator
    typingIndicator.parentElement.insertAdjacentElement('beforebegin', div);
    
    if (animate) {
        scrollToBottom();
    }
}

function scrollToBottom() {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function showTyping() {
    typingIndicator.classList.add('show');
    scrollToBottom();
}

function hideTyping() {
    typingIndicator.classList.remove('show');
}

async function sendMessage(message) {
    if (!message.trim()) return;
    
    // Add user message
    addMessage(message, 'user');
    chatInput.value = '';
    sendBtn.disabled = true;
    
    // Show typing
    showTyping();
    
    try {
        const response = await fetch(baseUrl + '?action=chatbot-portal-api', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: message })
        });
        
        const data = await response.json();
        hideTyping();
        
        if (data.reply) {
            addMessage(data.reply, 'bot');
        } else {
            addMessage("I'm sorry, I couldn't process that request. Please try again.", 'bot');
        }
    } catch (error) {
        hideTyping();
        addMessage("I'm having trouble connecting. Please check your internet and try again.", 'bot');
    }
    
    sendBtn.disabled = false;
    chatInput.focus();
}

function sendSuggestion(text) {
    chatInput.value = text;
    sendMessage(text);
}

// Event listeners
sendBtn.addEventListener('click', () => sendMessage(chatInput.value));
chatInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') sendMessage(chatInput.value);
});

// Focus input on load
chatInput.focus();
</script>

<?php include_once __DIR__ . '/../../includes/footer_portal.php'; ?>
