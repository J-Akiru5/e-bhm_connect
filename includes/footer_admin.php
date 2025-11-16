        </div> <footer id="admin-footer">
            © 2025 E-BHM Project. All Rights Reserved.
        </footer>

    </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/chatbot.js"></script>

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

    <div id="chat-bubble">💬</div>
    <div id="chat-window">
        </div>

</body>
</html>
