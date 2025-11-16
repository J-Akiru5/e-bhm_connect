document.addEventListener('DOMContentLoaded', () => {
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
        messageDiv.textContent = text;
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
