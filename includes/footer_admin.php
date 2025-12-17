        </main>
        
        <!-- Footer -->
        <footer id="admin-footer">
            <div class="footer-copyright">
                © <?php echo date('Y'); ?> E-BHM Connect. <?php echo __('footer.all_rights_reserved'); ?>
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
            title: '<?php echo __('dialogs.confirm_delete_title'); ?>',
            text: '<?php echo __('dialogs.confirm_delete_text'); ?>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<?php echo __('dialogs.yes_delete'); ?>',
            cancelButtonText: '<?php echo __('dialogs.cancel'); ?>'
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
    // Notification Dropdown
    (function(){
        const btn = document.getElementById('notificationBtn');
        const dropdown = document.getElementById('notificationDropdown');
        const markAllBtn = document.getElementById('markAllRead');
        
        if(btn && dropdown){
            btn.addEventListener('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                dropdown.classList.toggle('show');
                
                // Load notifications if opening
                if(dropdown.classList.contains('show')){
                    loadNotifications();
                }
            });
            
            // Close on outside click
            document.addEventListener('click', function(e){
                if(!dropdown.contains(e.target) && !btn.contains(e.target)){
                    dropdown.classList.remove('show');
                }
            });
        }
        
        if(markAllBtn){
            markAllBtn.addEventListener('click', function(e){
                e.preventDefault();
                markAllNotificationsRead();
            });
        }
        
        function loadNotifications(){
            const list = document.getElementById('notificationList');
            if(!list) return;
            
            fetch('<?php echo BASE_URL; ?>actions/notification_api.php?action=list')
                .then(r => r.json())
                .then(data => {
                    if(data.success && data.notifications.length > 0){
                        list.innerHTML = data.notifications.map(n => `
                            <div class="notification-item ${n.is_read ? '' : 'unread'}" data-id="${n.notification_id}">
                                <div class="notification-icon ${n.type}">${getNotificationIcon(n.type)}</div>
                                <div class="notification-content">
                                    <div class="notification-text">${escapeHtml(n.title)}</div>
                                    <div class="notification-time">${formatTimeAgo(n.created_at)}</div>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        list.innerHTML = '<div class="notification-item" style="text-align:center;padding:32px;"><span style="color:var(--text-muted);"><?php echo __('notifications.no_notifications'); ?></span></div>';
                    }
                })
                .catch(err => {
                    console.error('Error loading notifications:', err);
                    list.innerHTML = '<div class="notification-item" style="text-align:center;padding:32px;"><span style="color:var(--text-muted);"><?php echo __('notifications.no_notifications'); ?></span></div>';
                });
        }
        
        function markAllNotificationsRead(){
            fetch('<?php echo BASE_URL; ?>actions/notification_api.php?action=mark_all_read', {method: 'POST'})
                .then(r => r.json())
                .then(data => {
                    if(data.success){
                        const badge = document.querySelector('.topnav-action-badge');
                        if(badge) badge.remove();
                        document.querySelectorAll('.notification-item.unread').forEach(el => el.classList.remove('unread'));
                    }
                });
        }
        
        function getNotificationIcon(type){
            const icons = {
                'info': '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
                'success': '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
                'warning': '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
                'alert': '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>'
            };
            return icons[type] || icons['info'];
        }
        
        function formatTimeAgo(dateStr){
            const date = new Date(dateStr);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);
            
            if(seconds < 60) return '<?php echo __('time.just_now'); ?>';
            if(seconds < 3600) return Math.floor(seconds/60) + ' <?php echo __('time.minutes_ago'); ?>';
            if(seconds < 86400) return Math.floor(seconds/3600) + ' <?php echo __('time.hours_ago'); ?>';
            return Math.floor(seconds/86400) + ' <?php echo __('time.days_ago'); ?>';
        }
        
        function escapeHtml(text){
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
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

</body>
</html>
