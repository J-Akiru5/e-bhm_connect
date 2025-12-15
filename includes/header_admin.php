<?php
// (index.php handles session_start() and $page variable)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - E-BHM Connect</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/chatbot.css" media="print" onload="this.media='all'">
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Sidebar and top-nav theme moved to assets/css/admin.css -->

</head>
<body class="bg-light">

    <div id="admin-sidebar" class="d-flex flex-column p-3 text-white">
        <a href="<?php echo BASE_URL; ?>admin-dashboard" class="d-flex align-items-center mb-3 text-white text-decoration-none">
            <span class="fs-4">E-BHM Connect</span>
        </a>
        <hr>

        <div class="d-flex align-items-center mb-3">
            <img src="<?php echo BASE_URL; ?>assets/images/gabby_avatar.png" alt="Gabby" class="sidebar-gabby me-2">
            <div>
                <div style="color:rgba(255,255,255,0.95); font-weight:700"><?php echo htmlspecialchars($_SESSION['bhw_full_name'] ?? 'Admin'); ?></div>
                <div style="font-size:12px; color:rgba(255,255,255,0.8)">Administrator</div>
            </div>
        </div>
        <hr>

        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a class="nav-link text-white <?php echo ($page === 'admin-dashboard') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin-dashboard">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo ($page === 'admin-patients') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin-patients">Patients</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo ($page === 'admin-messages') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin-messages">Messages</a>
            </li>
             <li class="nav-item">
                <a class="nav-link text-white <?php echo ($page === 'admin-inventory') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin-inventory">Inventory</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo ($page === 'admin-inventory-categories') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin-inventory-categories">Inventory Categories</a>
            </li>
             <li class="nav-item">
                <a class="nav-link text-white <?php echo ($page === 'admin-announcements') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin-announcements">Announcements</a>
            </li>
             <li class="nav-item">
                <a class="nav-link text-white <?php echo ($page === 'admin-reports') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin-reports">Reports</a>
            </li>
             <li class="nav-item">
                <a class="nav-link text-white <?php echo ($page === 'admin-programs') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin-programs">Programs</a>
            </li>
             <li class="nav-item">
                <a class="nav-link text-white <?php echo ($page === 'admin-bhw-users') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin-bhw-users">BHW Management</a>
            </li>
        </ul>
        
        <div class="mt-auto">
            <hr>
            <a href="<?php echo BASE_URL; ?>admin-profile" class="d-flex align-items-center text-white text-decoration-none p-2 rounded <?php echo ($page === 'admin-profile') ? 'active' : ''; ?>">
                <img src="https://via.placeholder.com/40" alt="Profile" width="40" height="40" class="rounded-circle me-2">
                <strong><?php echo htmlspecialchars($_SESSION['bhw_full_name']); ?></strong>
            </a>
        </div>
    </div>

    <div id="main-content-wrapper">

        <nav id="admin-top-nav" class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <!-- Sidebar toggle for small screens -->
            <button id="sidebarToggle" class="btn btn-outline-secondary d-lg-none me-2" aria-label="Toggle sidebar">☰</button>
            <div class="container-fluid">
                <div class="navbar-nav ms-auto d-flex flex-row align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>" target="_blank">View Public Site</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>?action=logout">Logout</a>
                    </li>
                </div>
                <div id="sidebar-backdrop"></div>
            </div>
        </nav>

        <div id="main-content">
