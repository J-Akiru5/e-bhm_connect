    <!-- Modern Glassmorphism Footer -->
    <footer class="glass-footer">
        <div class="container">
            <div class="footer-grid">

                <div class="footer-section">
                    <h4>Barangay Information</h4>
                    <p><strong>Barangay:</strong> Bacong</p>
                    <p><strong>Location:</strong> Dumangas, Iloilo</p>
                    <p><strong>Population:</strong> 1,385+ residents</p>
                    <p><strong>ZIP Code:</strong> 5006</p>
                </div>

                <div class="footer-section">
                    <h4>Health Center</h4>
                    <p><strong>Name:</strong> Bacong Barangay Health Center</p>
                    <p><strong>Address:</strong> Bacong, Dumangas</p>
                    <p><strong>Contact:</strong> (033) 123-4567</p>
                    <p><strong>Email:</strong> healthcenter@bacong.gov</p>
                </div>

                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <p><a href="<?php echo BASE_URL; ?>?page=home">Home</a></p>
                    <p><a href="<?php echo BASE_URL; ?>?page=announcements">Announcements</a></p>
                    <p><a href="<?php echo BASE_URL; ?>?page=login-patient">Resident Portal</a></p>
                    <p><a href="<?php echo BASE_URL; ?>login-bhw">BHW Login</a></p>
                </div>

                <div class="footer-section">
                    <h4>Connect With Us</h4>
                    <p><strong>Barangay Hall:</strong> (033) 987-6543</p>
                    <p><strong>Email:</strong> barangaybacong@gmail.com</p>
                    <div style="margin-top: 1rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <a href="https://www.facebook.com/barangay.bacong.2025" target="_blank" class="btn btn-sm btn-glass" style="padding: 0.5rem 1rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                            Facebook
                        </a>
                        <a href="https://www.google.com/maps/search/?api=1&query=Barangay+Bacong+Dumangas+Iloilo" target="_blank" class="btn btn-sm btn-glass" style="padding: 0.5rem 1rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            Maps
                        </a>
                    </div>
                </div>

            </div>
            
            <div class="footer-bottom">
                <p style="margin: 0; color: var(--gray-500);">© 2025 E-BHM Connect. All Rights Reserved. Developed for Barangay Bacong, Dumangas.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- AOS JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <!-- Marked.js for Markdown -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <!-- Chatbot Styles and Scripts -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/chatbot.css">
    <script src="<?php echo BASE_URL; ?>assets/js/chatbot.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/resizable.js"></script>

    <!-- Chat Bubble -->
    <div id="chat-bubble" role="button" aria-label="Chat with Gabby" title="Chat with Gabby"></div>

    <!-- Chat Window -->
    <div id="chat-window">
        <div id="chat-resize-handle"></div>
        <div id="chat-header">
            <div style="display:flex;align-items:center;gap:.5rem;justify-content:center;">
                <img src="<?php echo BASE_URL; ?>assets/images/gabby_avatar.png" alt="Gabby" style="width:28px;height:28px;border-radius:8px;border:2px solid rgba(255,255,255,0.12);" />
                <div>Gabby — E-BHM Connect</div>
            </div>
            <span id="chat-close">✕</span>
        </div>
        <div id="chat-messages">
            <div class="chat-message bot">
                Hi! I'm Gabby, your virtual health assistant. How can I help you today?
            </div>
        </div>
        <div id="chat-input-area">
            <input type="text" id="chat-input" placeholder="Ask a question...">
            <button id="chat-send-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </button>
        </div>
    </div>

</body>
</html>
