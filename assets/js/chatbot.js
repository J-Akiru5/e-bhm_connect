document.addEventListener('DOMContentLoaded', () => {
    const chatBubble = document.getElementById('chat-bubble');
    const chatWindow = document.getElementById('chat-window');
    const closeBtn = document.getElementById('chat-close');
    const sendBtn = document.getElementById('chat-send-btn');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');

    if (!chatBubble || !chatWindow) return;

    chatBubble.addEventListener('click', () => {
        chatWindow.style.display = 'flex';
        chatBubble.style.display = 'none';
    });

    closeBtn.addEventListener('click', () => {
        chatWindow.style.display = 'none';
        chatBubble.style.display = 'block';
    });

    sendBtn.addEventListener('click', () => {
        sendMessage();
    });

    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    function sendMessage() {
        const messageText = chatInput.value.trim();
        if (messageText === '') return;

        // 1. Add user's message to UI
        addMessageToUI(messageText, 'user');
        chatInput.value = '';

        // 2. Show loading indicator
        addMessageToUI('...', 'loading');

        // 3. Send to backend
        fetch('?action=chatbot-api', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ message: messageText })
        })
        .then(response => response.json())
        .then(data => {
            // Remove loading indicator
            removeLoadingMessage();
            // Add bot's response
            const botReply = data.reply || 'Sorry, no reply.';
            addMessageToUI(botReply, 'bot');

            // Fire-and-forget: save conversation to server-side history
            try {
                fetch('actions/chatbot_save.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: messageText, response: botReply })
                }).catch(err => {
                    // Non-blocking; log but do not surface to user
                    console.warn('Failed to save chat log:', err);
                });
            } catch (e) {
                console.warn('Chat log save error:', e);
            }
        })
        .catch(error => {
            removeLoadingMessage();
            addMessageToUI('Sorry, I am having trouble connecting.', 'bot');
            console.error('Error:', error);
        });
    }

    function addMessageToUI(text, type) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('chat-message', type);

        if (type === 'bot') {
            // Use marked.js to parse Markdown from the bot
            messageDiv.innerHTML = marked.parse(text);
        } else {
            // Use textContent for user/loading messages for security
            messageDiv.textContent = text;
        }

        chatMessages.appendChild(messageDiv);
        // Scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function removeLoadingMessage() {
        const loadingMsg = chatMessages.querySelector('.chat-message.loading');
        if (loadingMsg) {
            chatMessages.removeChild(loadingMsg);
        }
    }
});
