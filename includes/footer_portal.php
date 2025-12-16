    </main>
    
    <!-- Portal Footer -->
    <footer style="background: var(--dark); padding: var(--space-6) var(--space-4); margin-top: var(--space-8);">
        <div style="max-width: 1280px; margin: 0 auto; text-align: center;">
            <p style="margin: 0; color: var(--gray-500); font-size: var(--font-size-sm);">
                © 2025 E-BHM Connect. All Rights Reserved. | <a href="<?php echo BASE_URL; ?>?page=home" style="color: var(--primary);">Visit Public Site</a>
            </p>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <?php if (isset($page) && $page === 'portal-chatbot'): ?>
        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
        <script src="<?php echo BASE_URL; ?>assets/js/chatbot_portal.js"></script>
    <?php endif; ?>
</body>
</html>
