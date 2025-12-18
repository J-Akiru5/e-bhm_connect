        </main>
        
        <!-- Footer -->
        <footer id="admin-footer">
            <div class="footer-copyright">
                <span style="color:var(--primary); font-weight:600;">E-BHM Connect</span> &copy; <?php echo date('Y'); ?>.
                <?php echo __('footer.all_rights_reserved'); ?>
            </div>
            <div class="footer-links">
                <a href="<?php echo BASE_URL; ?>?page=about"><?php echo __('footer.about'); ?></a>
                <a href="<?php echo BASE_URL; ?>?page=privacy"><?php echo __('footer.privacy'); ?></a>
                <a href="<?php echo BASE_URL; ?>?page=help"><?php echo __('footer.help'); ?></a>
            </div>
        </footer>

    </div><!-- /#main-content-wrapper -->

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/chatbot.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/resizable.js"></script>

    <script>
    // Live Clock
    function updateLiveClock() {
        const timeElement = document.getElementById('live-clock-time');
        const dateElement = document.getElementById('live-clock-date');
        if (timeElement && dateElement) {
            const now = new Date();
            const locale = '<?php echo isset($currentLang) && $currentLang === 'tl' ? 'fil-PH' : 'en-US'; ?>';
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            timeElement.innerText = now.toLocaleTimeString(locale, timeOptions);
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            dateElement.innerText = now.toLocaleDateString(locale, dateOptions);
        }
    }
    updateLiveClock();
    setInterval(updateLiveClock, 1000);
    </script>
    
    <script>
    // Delete Confirmation
    function confirmDelete(event) {
        event.preventDefault();
        const form = event.target;
        Swal.fire({
            title: <?php echo json_encode(__('dialogs.confirm_delete_title')); ?>,
            text: <?php echo json_encode(__('dialogs.confirm_delete_text')); ?>,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: <?php echo json_encode(__('dialogs.yes_delete')); ?>,
            cancelButtonText: <?php echo json_encode(__('dialogs.cancel')); ?>
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
        return false;
    }
    </script>

    <script>
    // Sidebar Toggle (Mobile)
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
                e.preventDefault();
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

    <script>
    // Sidebar Scroll Position Persistence
    (function(){
        const sidebar = document.getElementById('admin-sidebar');
        const sidebarNav = sidebar ? sidebar.querySelector('.sidebar-nav') : null;
        const STORAGE_KEY = 'ebhm_sidebar_scroll';
        
        if(sidebarNav){
            // Restore scroll position after layout is ready (use requestAnimationFrame)
            requestAnimationFrame(function(){
                const savedScroll = localStorage.getItem(STORAGE_KEY);
                if(savedScroll){
                    sidebarNav.scrollTop = parseInt(savedScroll, 10);
                }
            });
            
            // Debounced scroll save (reduce writes)
            let scrollTimeout;
            sidebarNav.addEventListener('scroll', function(){
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(function(){
                    localStorage.setItem(STORAGE_KEY, sidebarNav.scrollTop);
                }, 100);
            });
            
            // Save immediately before page unload
            window.addEventListener('beforeunload', function(){
                localStorage.setItem(STORAGE_KEY, sidebarNav.scrollTop);
            });
            
            // Also save when clicking any link (before fade-out transition)
            document.addEventListener('click', function(e){
                const link = e.target.closest('a');
                if(link && link.href && link.hostname === window.location.hostname){
                    localStorage.setItem(STORAGE_KEY, sidebarNav.scrollTop);
                }
            }, true); // Capture phase to run before navigation
        }
    })();
    </script>

    <script>
    // Language Selector
    (function(){
        const selector = document.getElementById('langSelector');
        if(selector){
            selector.addEventListener('click', function(){
                const currentLang = '<?php echo $currentLang; ?>';
                const newLang = currentLang === 'en' ? 'tl' : 'en';
                
                // Save preference via AJAX
                fetch('<?php echo BASE_URL; ?>actions/save_preferences.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({language: newLang})
                }).then(() => {
                    // Reload page to apply
                    window.location.reload();
                });
            });
        }
    })();
    </script>

    <!-- Chat Widget -->
    <div id="chat-bubble">💬</div>

    <div id="chat-window">
        <div id="chat-resize-handle"></div>
        <div id="chat-header">
            <div class="chat-title">
                <img src="<?php echo BASE_URL; ?>assets/images/gabby_avatar.png" alt="Gabby" style="width:32px;height:32px;border-radius:8px;border:2px solid rgba(255,255,255,0.12);" />
                <div>Gabby — E-BHM Connect</div>
            </div>
            <span id="chat-close">✕</span>
        </div>
        <div id="chat-messages">
            <div class="chat-message bot">
                <?php echo __('chatbot.greeting'); ?>
            </div>
        </div>
        <div id="chat-input-area">
            <input type="text" id="chat-input" placeholder="<?php echo __('chatbot.placeholder'); ?>">
            <button id="chat-send-btn">→</button>
        </div>
    </div>

    <!-- Page Transition Script -->
    <script>
    (function() {
        // Fade in the page when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('page-loaded');
        });
        
        // Fallback: fade in after a short delay in case DOMContentLoaded already fired
        setTimeout(function() {
            document.body.classList.add('page-loaded');
        }, 50);
        
        // Fade out when navigating to a new page
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && 
                link.href && 
                !link.href.startsWith('#') && 
                !link.href.startsWith('javascript:') &&
                !link.target && 
                !e.ctrlKey && 
                !e.metaKey && 
                !e.shiftKey &&
                link.hostname === window.location.hostname) {
                e.preventDefault();
                document.body.classList.add('page-leaving');
                document.body.classList.remove('page-loaded');
                setTimeout(function() {
                    window.location.href = link.href;
                }, 150);
            }
        });
        
        // Handle form submissions with fade out
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.method !== 'get' || !form.action.startsWith('javascript:')) {
                document.body.classList.add('page-leaving');
                document.body.classList.remove('page-loaded');
            }
        });
    })();
    </script>

    <!-- Tour Restart Button -->
    <button id="tour-restart-btn" onclick="startAdminTour()" aria-label="Restart Tour" title="Restart Admin Tour">
        <i class="fas fa-question"></i>
    </button>

    <style>
    /* Floating Tour Button */
    #tour-restart-btn {
        position: fixed;
        bottom: 20px;
        right: 100px; /* Moved beside Gabby */
        left: auto;   /* Remove left positioning */
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
    function startAdminTour() {
        const driver = window.driver.js.driver;
        const driverObj = driver({
            showProgress: true,
            animate: true,
            allowClose: true,
            overlayClickNext: false,
            popoverClass: 'driverjs-theme',
            steps: [
                { 
                    element: '#admin-sidebar', 
                    popover: { 
                        title: 'Dashboard Navigation', 
                        description: 'This is your command center. Use this sidebar to access all management tools.', 
                        side: "right", 
                        align: 'start' 
                    } 
                },
                { 
                    element: '.sidebar-nav-link[href*="admin-dashboard"]', 
                    popover: { 
                        title: 'Overview', 
                        description: 'Click here to see real-time statistics and recent activity.', 
                        side: "right", 
                        align: 'center' 
                    } 
                },
                { 
                    element: '.sidebar-nav-link[href*="admin-patients"]', 
                    popover: { 
                        title: 'Manage Patients', 
                        description: 'View, add, and update resident health records here.', 
                        side: "right", 
                        align: 'center' 
                    } 
                },
                { 
                    element: '.sidebar-nav-link[href*="admin-messages"]', 
                    popover: { 
                        title: 'Messages', 
                        description: 'Communicate directly with residents via SMS or in-app chat.', 
                        side: "right", 
                        align: 'center' 
                    } 
                },
                { 
                    element: '.sidebar-nav-link[href*="admin-reports"]', 
                    popover: { 
                        title: 'Reports & Analytics', 
                        description: 'Generate comprehensive health reports and visualize data trends.', 
                        side: "right", 
                        align: 'center' 
                    } 
                },
                { 
                    element: '#admin-top-nav', 
                    popover: { 
                        title: 'Quick Settings', 
                        description: 'Toggle the theme, switch languages, or view your profile from the top bar.', 
                        side: "bottom", 
                        align: 'start' 
                    } 
                },
                { 
                    element: '#tour-restart-btn', 
                    popover: { 
                        title: 'Tour Guide', 
                        description: 'Click this button anytime if you need a refresher on these features.', 
                        side: "left", 
                        align: 'end' 
                    } 
                }
            ],
            onDestroyed: () => {
                localStorage.setItem('ebhm_admin_tour_seen', 'true');
            }
        });

        driverObj.drive();
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (!localStorage.getItem('ebhm_admin_tour_seen')) {
            setTimeout(() => {
                startAdminTour();
            }, 1000);
        }
    });
    </script>
</body>
</html>
