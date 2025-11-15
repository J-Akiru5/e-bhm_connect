    </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        function confirmDelete(event) {
            event.preventDefault(); // Stop the form from submitting immediately
            const form = event.target;

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, submit the form
                    form.submit();
                }
            });
            return false; // This is redundant but safe
        }
    </script>
    <script>
        // --- Live Clock Function ---
        function updateLiveClock() {
            const timeElement = document.getElementById('live-clock-time');
            const dateElement = document.getElementById('live-clock-date');

            if (timeElement && dateElement) {
                const now = new Date();

                // Format time: 11:17:30 PM
                const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
                timeElement.innerText = now.toLocaleTimeString('en-US', timeOptions);

                // Format date: Saturday, November 15, 2025
                const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                dateElement.innerText = now.toLocaleDateString('en-US', dateOptions);
            }
        }

        // Run the function immediately on page load
        updateLiveClock();

        // Run the function every second (1000 milliseconds)
        setInterval(updateLiveClock, 1000);
        // --- End Live Clock ---
    </script>
    <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/patient_edit.js"></script>
    </body>
    </html>
