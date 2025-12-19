        </main>
        
        <!-- Footer -->
        <footer id="admin-footer">
            <div class="footer-copyright">
                <span class="footer-brand">E-BHM Connect</span> &copy; <?php echo date('Y'); ?>.
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
        const STORAGE_KEY = 'ebhm_sidebar_scroll';
        
        if(sidebar){
            // Restore scroll position IMMEDIATELY on script load
            const savedScroll = localStorage.getItem(STORAGE_KEY);
            if(savedScroll){
                sidebar.scrollTop = parseInt(savedScroll, 10);
            }
            
            // Debounced scroll save (reduce writes)
            let scrollTimeout;
            sidebar.addEventListener('scroll', function(){
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(function(){
                    localStorage.setItem(STORAGE_KEY, sidebar.scrollTop);
                }, 100);
            });
            
            // Save immediately before page unload
            window.addEventListener('beforeunload', function(){
                localStorage.setItem(STORAGE_KEY, sidebar.scrollTop);
            });
            
            // Also save when clicking any link (before navigation)
            document.addEventListener('click', function(e){
                const link = e.target.closest('a');
                if(link && link.href && link.hostname === window.location.hostname){
                    localStorage.setItem(STORAGE_KEY, sidebar.scrollTop);
                }
            }, true); // Capture phase to run before navigation
        }
    })();
    </script>

    <script>
    // Collapsible Sidebar Sections
    (function(){
        const COLLAPSED_KEY = 'ebhm_sidebar_collapsed';
        const sections = document.querySelectorAll('.sidebar-nav-section');
        
        // Load saved collapsed state
        let collapsedSections = [];
        try {
            collapsedSections = JSON.parse(localStorage.getItem(COLLAPSED_KEY)) || [];
        } catch(e) {}
        
        sections.forEach(function(section, idx) {
            const title = section.querySelector('.sidebar-nav-title');
            if (!title) return;
            
            // Restore collapsed state
            if (collapsedSections.includes(idx)) {
                section.classList.add('collapsed');
            }
            
            // Toggle on click
            title.addEventListener('click', function() {
                section.classList.toggle('collapsed');
                
                // Save collapsed state
                const collapsed = [];
                document.querySelectorAll('.sidebar-nav-section').forEach(function(s, i) {
                    if (s.classList.contains('collapsed')) {
                        collapsed.push(i);
                    }
                });
                localStorage.setItem(COLLAPSED_KEY, JSON.stringify(collapsed));
            });
        });
    })();
    </script>

    <script>
    // Resizable Sidebar
    (function(){
        const sidebar = document.getElementById('admin-sidebar');
        const handle = document.getElementById('sidebar-resize-handle');
        const mainContent = document.getElementById('main-content-wrapper');
        const bgOrbs = document.querySelector('.admin-bg-orbs');
        const WIDTH_KEY = 'ebhm_sidebar_width';
        const MIN_WIDTH = 200;
        const MAX_WIDTH = 400;
        
        if (!sidebar || !handle) return;
        
        // Restore saved width
        const savedWidth = localStorage.getItem(WIDTH_KEY);
        if (savedWidth) {
            const width = Math.max(MIN_WIDTH, Math.min(MAX_WIDTH, parseInt(savedWidth, 10)));
            setSidebarWidth(width);
        }
        
        function setSidebarWidth(width) {
            sidebar.style.width = width + 'px';
            if (mainContent) mainContent.style.left = width + 'px';
            if (bgOrbs) bgOrbs.style.left = width + 'px';
            document.documentElement.style.setProperty('--sidebar-width', width + 'px');
        }
        
        let isResizing = false;
        
        handle.addEventListener('mousedown', function(e) {
            isResizing = true;
            handle.classList.add('active');
            document.body.classList.add('sidebar-resizing');
            e.preventDefault();
        });
        
        document.addEventListener('mousemove', function(e) {
            if (!isResizing) return;
            let newWidth = e.clientX;
            newWidth = Math.max(MIN_WIDTH, Math.min(MAX_WIDTH, newWidth));
            setSidebarWidth(newWidth);
        });
        
        document.addEventListener('mouseup', function() {
            if (!isResizing) return;
            isResizing = false;
            handle.classList.remove('active');
            document.body.classList.remove('sidebar-resizing');
            // Save width
            localStorage.setItem(WIDTH_KEY, sidebar.style.width.replace('px', ''));
        });
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
    <div id="chat-bubble" role="button" aria-label="Chat with Gabby" title="Chat with Gabby"></div>

    <div id="chat-window">
        <div id="chat-resize-handle"></div>
        <div id="chat-header">
            <div class="chat-title">
                <img src="<?php echo BASE_URL; ?>assets/images/gabby-head.png" alt="Gabby" class="chat-header-avatar chat-header-avatar--lg" />
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
    <button id="tour-restart-btn" class="admin-tour-btn" onclick="startAdminTour()" aria-label="Restart Tour" title="Restart Admin Tour">
        <i class="fas fa-question"></i>
    </button>

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
