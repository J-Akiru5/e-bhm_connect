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
            
            <div class="footer-bottom" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <p style="margin: 0; color: var(--gray-500);">
                    <span style="color: var(--primary); font-weight: 600;">E-BHM Connect</span> &copy; 2025. All Rights Reserved.
                </p>
                <div class="footer-legal-links">
                    <a href="<?php echo BASE_URL; ?>?page=about" style="color: #0d6efd; text-decoration: none; margin-left: 1rem; font-weight: 500;">About</a>
                    <a href="<?php echo BASE_URL; ?>?page=privacy" style="color: #0d6efd; text-decoration: none; margin-left: 1rem; font-weight: 500;">Privacy</a>
                    <a href="<?php echo BASE_URL; ?>?page=help" style="color: #0d6efd; text-decoration: none; margin-left: 1rem; font-weight: 500;">Help</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- AOS JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        // Initialize AOS animations
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 600,
                once: true,
                offset: 50
            });
        }
    </script>

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
            <div class="chat-title">
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

    <!-- Tour Restart Button -->
    <button id="tour-restart-btn" onclick="startGlobalTour()" aria-label="Restart Tour" title="Restart Site Tour">
        <i class="fas fa-question"></i>
    </button>

    <style>
    /* Floating Tour Button */
    #tour-restart-btn {
        position: fixed;
        bottom: 20px;
        left: 20px;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: rgba(32, 201, 151, 0.2);
        border: 1px solid rgba(32, 201, 151, 0.5);
        color: var(--primary);
        font-size: 1.2rem;
        cursor: pointer;
        z-index: 1000;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }

    #tour-restart-btn:hover {
        background: var(--primary);
        color: white;
        transform: scale(1.1) rotate(10deg);
        box-shadow: 0 0 20px rgba(32, 201, 151, 0.6);
    }

    /* Driver.js Glassmorphism Theme */
    .driver-popover.driverjs-theme {
        background: rgba(15, 23, 42, 0.95);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        padding: 20px;
    }

    .driver-popover.driverjs-theme .driver-popover-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 10px;
    }

    .driver-popover.driverjs-theme .driver-popover-description {
        font-family: 'Poppins', sans-serif;
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.8);
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .driver-popover.driverjs-theme .driver-popover-footer button {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        padding: 8px 16px;
        font-family: 'Poppins', sans-serif;
        font-size: 0.85rem;
        transition: all 0.2s;
        text-shadow: none;
    }

    .driver-popover.driverjs-theme .driver-popover-footer button:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .driver-popover.driverjs-theme .driver-popover-arrow-side-left.driver-popover-arrow {
        border-left-color: rgba(15, 23, 42, 0.95);
    }

    .driver-popover.driverjs-theme .driver-popover-arrow-side-right.driver-popover-arrow {
        border-right-color: rgba(15, 23, 42, 0.95);
    }

    .driver-popover.driverjs-theme .driver-popover-arrow-side-top.driver-popover-arrow {
        border-top-color: rgba(15, 23, 42, 0.95);
    }

    .driver-popover.driverjs-theme .driver-popover-arrow-side-bottom.driver-popover-arrow {
        border-bottom-color: rgba(15, 23, 42, 0.95);
    }
    
    /* Highlight Path styling */
    .driver-overlay {
        path {
            fill: rgba(0, 0, 0, 0.85) !important;
        }
    }
    </style>

    <script>
    // Global Tour Logic
    function startGlobalTour() {
        const driver = window.driver.js.driver;
        const driverObj = driver({
            showProgress: true,
            animate: true,
            popoverClass: 'driverjs-theme',
            steps: [
                { 
                    element: '.brand', 
                    popover: { 
                        title: 'Welcome to E-BHM Connect', 
                        description: 'Your digital gateway to community health services in Barangay Bacong.', 
                        side: "bottom", 
                        align: 'start' 
                    } 
                },
                { 
                    element: '.nav-links', 
                    popover: { 
                        title: 'Easy Navigation', 
                        description: 'Browse news, services, and community updates from the main menu.', 
                        side: "bottom", 
                        align: 'start' 
                    } 
                },
                { 
                    element: '.nav-actions', 
                    popover: { 
                        title: 'Secure Access', 
                        description: 'Log in to your personalized dashboard to view records or manage tasks.', 
                        side: "bottom", 
                        align: 'end' 
                    } 
                },
                { 
                    element: '#chat-bubble', 
                    popover: { 
                        title: 'Ask Gabby', 
                        description: 'Need help? Click the chat icon anytime for instant assistance.', 
                        side: "top", 
                        align: 'end' 
                    } 
                },
                { 
                    element: '#tour-restart-btn', 
                    popover: { 
                        title: 'Tour Replay', 
                        description: 'Click this button anytime if you want to see this tour again.', 
                        side: "right", 
                        align: 'end' 
                    } 
                }
            ],
            allowClose: true,
            overlayClickNext: false,
            onDestroyed: () => {
                // Set localStorage so it doesn't run automatically again
                localStorage.setItem('ebhm_tour_seen', 'true');
            }
        });

        driverObj.drive();
    }

    // Auto-start on first visit
    document.addEventListener('DOMContentLoaded', function() {
        if (!localStorage.getItem('ebhm_tour_seen')) {
            // Short delay to ensure elements are rendered
            setTimeout(() => {
                startGlobalTour();
            }, 1000);
        }
    });
    </script>
</body>
</html>
