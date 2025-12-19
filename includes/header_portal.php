<?php
// includes/header_portal.php
// Modern header for logged-in patient portal
// (index.php handles session_start() and $page variable)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Portal - E-BHM Connect</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Poppins Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/mobile-utils.css">

    <!-- Driver.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>
    <!-- Driver.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>
    
    <style>
        /* Portal-specific overrides */
        body {
            background: var(--gray-100);
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* Dark Mode Variables */
        body.dark-mode {
            --gray-100: #1a1a2e;
            --gray-50: #16213e;
            --gray-200: #2a2a4a;
            --gray-300: #3a3a5a;
            --gray-400: #6a6a8a;
            --gray-500: #8a8aaa;
            --gray-600: #a0a0c0;
            --gray-700: #c0c0d0;
            --gray-800: #e0e0f0;
            --gray-900: #f0f0ff;
            --white: #1e1e3f;
            background: #1a1a2e;
            color: #e0e0f0;
        }
        
        body.dark-mode .portal-card,
        body.dark-mode .dashboard-card,
        body.dark-mode .profile-section,
        body.dark-mode .chat-card,
        body.dark-mode .stat-card {
            background: #1e1e3f;
            border-color: #2a2a4a;
        }
        
        body.dark-mode .portal-card-body,
        body.dark-mode .dashboard-card-body,
        body.dark-mode .profile-section-body {
            color: #c0c0d0;
        }
        
        body.dark-mode .form-control {
            background: #16213e;
            border-color: #3a3a5a;
            color: #e0e0f0;
        }
        
        body.dark-mode .form-control:focus {
            background: #1e1e3f;
            border-color: var(--primary);
        }
        
        body.dark-mode .text-muted {
            color: #8a8aaa !important;
        }
        
        body.dark-mode .table {
            color: #c0c0d0;
        }
        
        body.dark-mode .table thead th {
            background: #16213e;
            border-color: #2a2a4a;
        }
        
        body.dark-mode .chat-messages {
            background: #16213e;
        }
        
        body.dark-mode .chat-bubble {
            background: #1e1e3f;
            border-color: #2a2a4a;
            color: #e0e0f0;
        }
        
        .portal-navbar {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: var(--space-3) 0;
            box-shadow: var(--shadow-md);
        }
        
        .portal-navbar .navbar-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 var(--space-6);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .portal-navbar .brand {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            color: var(--white);
            font-weight: 700;
            font-size: var(--font-size-lg);
            text-decoration: none;
        }
        
        .portal-navbar .brand img {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-lg);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .portal-navbar .nav-links {
            display: flex;
            align-items: center;
            gap: var(--space-2);
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .portal-navbar .nav-link {
            padding: var(--space-2) var(--space-4);
            color: rgba(255, 255, 255, 0.85);
            font-size: var(--font-size-sm);
            font-weight: 500;
            border-radius: var(--radius-lg);
            transition: all var(--transition-fast);
            text-decoration: none;
        }
        
        .portal-navbar .nav-link:hover,
        .portal-navbar .nav-link.active {
            color: var(--white);
            background: rgba(255, 255, 255, 0.15);
        }
        
        .portal-navbar .nav-actions {
            display: flex;
            align-items: center;
            gap: var(--space-3);
        }
        
        .portal-main {
            max-width: 1280px;
            margin: 0 auto;
            padding: var(--space-8) var(--space-6);
        }
        
        /* Portal Cards */
        .portal-card {
            background: var(--white);
            border-radius: var(--radius-2xl);
            border: 1px solid var(--gray-200);
            overflow: hidden;
            transition: all var(--transition-base);
        }
        
        .portal-card:hover {
            box-shadow: var(--shadow-lg);
        }
        
        .portal-card-header {
            padding: var(--space-4) var(--space-6);
            background: linear-gradient(135deg, var(--primary-light), var(--white));
            border-bottom: 1px solid var(--gray-200);
            font-weight: 600;
            color: var(--primary-dark);
        }
        
        .portal-card-body {
            padding: var(--space-6);
        }
        
        /* Mobile menu toggle */
        .portal-menu-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            padding: var(--space-2);
            background: transparent;
            border: none;
            cursor: pointer;
        }
        
        .portal-menu-toggle span {
            display: block;
            width: 24px;
            height: 2px;
            background: var(--white);
            border-radius: var(--radius-full);
        }
        
        @media (max-width: 768px) {
            .portal-navbar .nav-links {
                display: none;
            }
            
            .portal-menu-toggle {
                display: flex;
            }
            
            .portal-navbar.menu-open .nav-links {
                display: flex;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--primary-dark);
                padding: var(--space-4);
            }
            
            .portal-main {
                padding: var(--space-6) var(--space-4);
            }
        }
    </style>
</head>
<body>
    <nav class="portal-navbar" id="portalNav">
        <div class="navbar-container">
            <a class="brand" href="<?php echo BASE_URL; ?>portal-dashboard">
                <img src="<?php echo BASE_URL; ?>assets/images/e-logo.png" alt="Logo" />
                <span>E-BHM Connect</span>
            </a>
            
            <ul class="nav-links">
                <li>
                    <a class="nav-link <?php echo ($page === 'portal-dashboard') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>portal-dashboard">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px; vertical-align: middle;">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a class="nav-link <?php echo ($page === 'portal-chatbot') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>portal-chatbot">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px; vertical-align: middle;">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                        Chat with Gabby
                    </a>
                </li>
                <li>
                    <a class="nav-link <?php echo ($page === 'portal-profile') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>portal-profile">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px; vertical-align: middle;">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        My Profile
                    </a>
                </li>
            </ul>
            
            <div class="nav-actions">
                <!-- Theme Toggle -->
                <button id="themeToggle" class="btn btn-sm" style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.2); padding: 6px 10px; margin-right: 8px;" title="Toggle Dark Mode">
                    <svg id="sunIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                    <svg id="moonIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </button>
                
                <span style="color: rgba(255,255,255,0.85); font-size: var(--font-size-sm); margin-right: var(--space-2);">
                    <?php echo htmlspecialchars($_SESSION['patient_full_name'] ?? 'User'); ?>
                </span>
                <a href="<?php echo BASE_URL; ?>?action=logout-patient" class="btn btn-sm" style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.2);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px; vertical-align: middle;">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    Logout
                </a>
            </div>
            
            <button class="portal-menu-toggle" id="portalMenuToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>
    
    <main class="portal-main">

<script>
document.getElementById('portalMenuToggle')?.addEventListener('click', function() {
    document.getElementById('portalNav').classList.toggle('menu-open');
});

// Theme toggle functionality
(function() {
    const themeToggle = document.getElementById('themeToggle');
    const sunIcon = document.getElementById('sunIcon');
    const moonIcon = document.getElementById('moonIcon');
    
    // Check for saved theme preference or default to light
    const savedTheme = localStorage.getItem('portalTheme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        sunIcon.style.display = 'block';
        moonIcon.style.display = 'none';
    }
    
    themeToggle?.addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        const isDark = document.body.classList.contains('dark-mode');
        
        // Toggle icons
        sunIcon.style.display = isDark ? 'block' : 'none';
        moonIcon.style.display = isDark ? 'none' : 'block';
        
        // Save preference
        localStorage.setItem('portalTheme', isDark ? 'dark' : 'light');
    });
})();
</script>
