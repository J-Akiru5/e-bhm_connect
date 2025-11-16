document.addEventListener('DOMContentLoaded', () => {

    // --- NEW CODE BLOCK ---
    // Get the messages container first
    const initialChatMessages = document.getElementById('chat-messages');

    // Check if our chatHistory variable exists (from PHP)
    if (typeof chatHistory !== 'undefined' && initialChatMessages) {
        // Loop through the history and add it to the UI
        chatHistory.forEach(chat => {
            // Add the user's old prompt
            const userDiv = document.createElement('div');
            userDiv.classList.add('chat-message', 'user');
            userDiv.textContent = chat.prompt_text; // User text is always plain
            initialChatMessages.appendChild(userDiv);

            // Add the bot's old response
            const botDiv = document.createElement('div');
            botDiv.classList.add('chat-message', 'bot');
            botDiv.innerHTML = marked.parse(chat.response_text); // Use marked.js
            initialChatMessages.appendChild(botDiv);
        });
    }
    // --- END NEW CODE BLOCK ---

    const sendBtn = document.getElementById('chat-send-btn');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');

    if (!sendBtn) return; // Only run on chat page

    sendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    function sendMessage() {
        const messageText = chatInput.value.trim();
        if (messageText === '') return;

        addMessageToUI(messageText, 'user');
        chatInput.value = '';
        addMessageToUI('...', 'loading');

        fetch('?action=chatbot-portal-api', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: messageText })
        })
        .then(response => response.json())
        .then(data => {
            removeLoadingMessage();
            addMessageToUI(data.reply, 'bot');
        })
        .catch(error => {
            removeLoadingMessage();
            addMessageToUI('Sorry, I am having trouble connecting.', 'bot');
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
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function removeLoadingMessage() {
        const loadingMsg = chatMessages.querySelector('.chat-message.loading');
        if (loadingMsg) chatMessages.removeChild(loadingMsg);
    }

    // Scroll to bottom on page load
    chatMessages.scrollTop = chatMessages.scrollHeight;
});
