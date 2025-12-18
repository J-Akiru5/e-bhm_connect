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
    <!-- Tour Restart Button -->
    <button id="tour-restart-btn" onclick="startPortalTour()" aria-label="Restart Tour" title="Restart Portal Tour">
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
    
    .driver-popover.driverjs-theme .driver-popover-arrow-side-left.driver-popover-arrow { border-left-color: rgba(15, 23, 42, 0.95); }
    .driver-popover.driverjs-theme .driver-popover-arrow-side-right.driver-popover-arrow { border-right-color: rgba(15, 23, 42, 0.95); }
    .driver-popover.driverjs-theme .driver-popover-arrow-side-top.driver-popover-arrow { border-top-color: rgba(15, 23, 42, 0.95); }
    .driver-popover.driverjs-theme .driver-popover-arrow-side-bottom.driver-popover-arrow { border-bottom-color: rgba(15, 23, 42, 0.95); }
    
    /* Highlight Path styling */
    .driver-overlay path {
        fill: rgba(0, 0, 0, 0.85) !important;
    }
    </style>

    <script>
    function startPortalTour() {
        const driver = window.driver.js.driver;
        const driverObj = driver({
            showProgress: true,
            animate: true,
            allowClose: true,
            overlayClickNext: false,
            popoverClass: 'driverjs-theme',
            steps: [
                { 
                    element: '.navbar-container', 
                    popover: { 
                        title: 'Resident Portal', 
                        description: 'Manage your health records and appointments here.', 
                        side: "bottom", 
                        align: 'start' 
                    } 
                },
                { 
                    element: '.nav-links', 
                    popover: { 
                        title: 'Quick Access', 
                        description: 'Navigate to Chat, Profile, or back to Dashboard.', 
                        side: "bottom", 
                        align: 'start' 
                    } 
                },
                { 
                    element: '#tour-restart-btn', 
                    popover: { 
                        title: 'Need Help?', 
                        description: 'Click here to take this tour again.', 
                        side: "right", 
                        align: 'end' 
                    } 
                }
            ],
            onDestroyed: () => {
                localStorage.setItem('ebhm_portal_tour_seen', 'true');
            }
        });

        driverObj.drive();
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (!localStorage.getItem('ebhm_portal_tour_seen')) {
            setTimeout(() => {
                startPortalTour();
            }, 1000);
        }
    });
    </script>
</body>
</html>
