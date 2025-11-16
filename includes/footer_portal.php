    </main>
    <footer class="bg-dark text-white text-center p-4">
        <div class="container">
            <p class="mb-0">© 2025 E-BHM Project. All Rights Reserved.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <?php if (isset($page) && $page === 'portal-chatbot'): ?>
        <script src="<?php echo BASE_URL; ?>assets/js/chatbot_portal.js"></script>
    <?php endif; ?>
</body>
</html>
