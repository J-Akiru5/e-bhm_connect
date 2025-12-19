<?php
require_once __DIR__ . '/auth_helpers.php';
$patientPortalEnabled = get_app_setting('enable_patient_portal', true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-BHM Connect - Barangay Bacong Health Management</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- AOS (Animate On Scroll) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    
    <!-- Poppins Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Glassmorphism Styles -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/mobile-utils.css">

    <!-- Driver.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>
    <!-- Driver.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>
</head>
<body class="dark-theme">

    <!-- Glassmorphism Navbar -->
    <nav class="glass-navbar" id="mainNavbar">
        <div class="navbar-container">
            <a class="brand" href="<?php echo BASE_URL; ?>?page=home">
                <img src="<?php echo BASE_URL; ?>assets/images/e-logo.png" alt="E-BHM Connect Logo" />
                <span>E-BHM Connect</span>
            </a>
            
            <ul class="nav-links">
                <li><a class="nav-link <?php echo (isset($page) && $page === 'home') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>?page=home">Home</a></li>
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>?page=home#about">About</a></li>
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>?page=home#services">Services</a></li>
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>?page=announcements">Announcements</a></li>
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>?page=home#contact">Contact</a></li>
            </ul>
            
            <div class="nav-actions">
                <?php if ($patientPortalEnabled): ?>
                <!-- Check for Patient Session -->
                <?php if (isset($_SESSION['patient_id'])): ?>
                    <a href="<?php echo BASE_URL; ?>?page=portal-dashboard" class="btn btn-glass btn-sm">My Portal</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>?page=login-patient" class="btn btn-glass btn-sm">Resident Portal</a>
                <?php endif; ?>
                <?php endif; ?>

                <!-- Check for BHW Session -->
                <?php if (isset($_SESSION['bhw_id'])): ?>
                    <a href="<?php echo BASE_URL; ?>?page=admin-dashboard" class="btn btn-primary btn-sm">Dashboard</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>login-bhw" class="btn btn-primary btn-sm">BHW Login</a>
                <?php endif; ?>
            </div>
            
            <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('mainNavbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
    
    // Mobile menu toggle
    document.getElementById('mobileMenuToggle').addEventListener('click', function() {
        document.getElementById('mainNavbar').classList.toggle('menu-open');
    });
    </script>
