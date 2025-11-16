        </div> <footer id="admin-footer">
            © 2025 E-BHM Project. All Rights Reserved.
        </footer>

    </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/chatbot.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/resizable.js"></script>

    <script>
    function updateLiveClock() {
        const timeElement = document.getElementById('live-clock-time');
        const dateElement = document.getElementById('live-clock-date');
        if (timeElement && dateElement) {
            const now = new Date();
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            timeElement.innerText = now.toLocaleTimeString('en-US', timeOptions);
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            dateElement.innerText = now.toLocaleDateString('en-US', dateOptions);
        }
    }
    updateLiveClock();
    setInterval(updateLiveClock, 1000);
    </script>
    
    <script>
    function confirmDelete(event) {
        event.preventDefault();
        const form = event.target;
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
        return false;
    }
    </script>

    <script>
    // Sidebar toggle/offcanvas handling for small screens
    (function(){
        const toggle = document.getElementById('sidebarToggle');
        const backdrop = document.getElementById('sidebar-backdrop');
        const body = document.body;

        function openSidebar(){
            body.classList.add('sidebar-open');
        }
        function closeSidebar(){
            body.classList.remove('sidebar-open');
        }

        if(toggle){
            toggle.addEventListener('click', function(e){
                if(body.classList.contains('sidebar-open')){ closeSidebar(); } else { openSidebar(); }
            });
        }

        if(backdrop){
            backdrop.addEventListener('click', function(){ closeSidebar(); });
        }

        // Close on ESC
        document.addEventListener('keydown', function(ev){ if(ev.key === 'Escape'){ closeSidebar(); } });
    })();
    </script>

    <div id="chat-bubble">💬</div>

    <div id="chat-window">
        <div id="chat-resize-handle"></div>
        <div id="chat-header">
            E-BHM Connect ("Gabby")
            <span id="chat-close">X</span>
        </div>
        <div id="chat-messages">
            <div class="chat-message bot">
                Hi! I'm Gabby. How can I help you today?
            </div>
        </div>
        <div id="chat-input-area">
            <input type="text" id="chat-input" placeholder="Ask a question...">
            <button id="chat-send-btn">→</button>
        </div>
    </div>

</body>
</html>
