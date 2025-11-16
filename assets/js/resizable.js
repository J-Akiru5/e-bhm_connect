// Makes an element resizable by dragging.
function makeResizable(element, handle) {
    let isResizing = false;
    let originalX = 0;
    let originalY = 0;
    let originalWidth = 0;
    let originalHeight = 0;

    handle.addEventListener('mousedown', (e) => {
        e.preventDefault();
        isResizing = true;
        originalX = e.clientX;
        originalY = e.clientY;
        originalWidth = element.offsetWidth;
        originalHeight = element.offsetHeight;
        
        // Add listeners to the whole window
        window.addEventListener('mousemove', resize);
        window.addEventListener('mouseup', stopResize);
    });

    function resize(e) {
        if (!isResizing) return;

        // Calculate new dimensions
        const newWidth = originalWidth - (e.clientX - originalX);
        const newHeight = originalHeight - (e.clientY - originalY);
        
        // Apply new dimensions (with min/max)
        if (newWidth > 300) {
            element.style.width = newWidth + 'px';
        }
        if (newHeight > 250) {
            element.style.height = newHeight + 'px';
        }
    }

    function stopResize() {
        isResizing = false;
        // Remove listeners from the whole window
        window.removeEventListener('mousemove', resize);
        window.removeEventListener('mouseup', stopResize);
    }
}

// Wait for DOM to load, then find chat windows and make them resizable
document.addEventListener('DOMContentLoaded', () => {
    const chatWindow = document.getElementById('chat-window');
    const resizeHandle = document.getElementById('chat-resize-handle');

    if (chatWindow && resizeHandle) {
        makeResizable(chatWindow, resizeHandle);
    }
});
