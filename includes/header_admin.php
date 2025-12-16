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
    <style>
        :root{ --brand-primary: #20c997; --brand-dark: #0f5132; --brand-light: #e6fffa; --card-radius:12px; --sidebar-width:280px; }
        body{ font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }
        /* Note: layout rules for sidebar and main content live in assets/css/admin.css. */
        /* Ensure top nav uses brand gradient and nav links are visible */
        #admin-top-nav { background: linear-gradient(90deg, var(--brand-primary), rgba(32, 201, 151, 0.85)) !important; box-shadow: none; }
        #admin-top-nav .nav-link { color: #fff !important; }
        /* Sidebar header */
        #admin-sidebar { background: linear-gradient(180deg, var(--brand-primary), rgba(32, 201, 151, 0.88)); }
        #admin-sidebar .fs-4{ font-weight:600; color:#fff; }
        /* Nav links */
        #admin-sidebar .nav-link{ color: rgba(255,255,255,0.92); background: transparent; border-radius:10px; transition: all .18s ease; }
        #admin-sidebar .nav-link:hover{ background: rgba(255,255,255,0.08); transform: translateX(6px); color:#fff; }
        #admin-sidebar .nav-link.active{ background: #fff; color: var(--brand-dark) !important; font-weight:700; }
        #admin-sidebar img.profile-avatar{ width:44px; height:44px; object-fit:cover; border-radius:10px; }
        /* Gabby mascot in sidebar profile */
        .sidebar-gabby{ width:48px; height:48px; border-radius:12px; object-fit:cover; border:2px solid rgba(255,255,255,0.12); box-shadow: 0 6px 18px rgba(4,15,35,0.06); }
        /* Top nav (additional safety) */
        #admin-top-nav{ box-shadow:none; }
        /* Cards and interactive elements */
        .card{ border-radius: var(--card-radius); }
        .clickable{ cursor:pointer; transition: transform .18s ease, box-shadow .18s ease; }
        .clickable:hover{ transform: translateY(-6px); box-shadow: 0 12px 30px rgba(16,24,32,0.08); }
        /* Table heading */
        .table thead th{ background: #f6f7f9; text-transform:uppercase; font-size:.8rem; letter-spacing:.02em; }
        /* Buttons */
        .btn-brand{ background: var(--brand-primary); border-color: var(--brand-primary); color: #fff; }
        .btn-outline-brand{ color: var(--brand-primary); border-color: rgba(32, 201, 151, 0.12); }
        /* small helpers */
        .icon-bubble{ width:48px;height:48px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;background:var(--brand-primary); }
    </style>

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
