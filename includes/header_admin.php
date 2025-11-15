<?php
// Admin header with sidebar and top navbar
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin - E-BHM-CONNECT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar { width: 240px; min-height:100vh; display:flex; flex-direction:column; }
        .content { flex: 1; }
        .hover-bg-secondary:hover { background: rgba(255,255,255,0.03); }
    </style>
</head>
<body class="d-flex">

    <aside class="sidebar vh-100 bg-success text-white p-3">
        <a href="<?php echo BASE_URL; ?>admin-dashboard" class="text-decoration-none text-white d-block mb-3 fs-4 fw-bold">Admin Portal</a>

        <ul class="nav nav-pills flex-column">
            <li class="nav-item"><a class="nav-link text-white" href="<?php echo BASE_URL; ?>admin-dashboard">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?php echo BASE_URL; ?>admin-patients">Patients</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?php echo BASE_URL; ?>admin-inventory">Inventory</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?php echo BASE_URL; ?>admin-reports">Reports</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?php echo BASE_URL; ?>admin-programs">Programs</a></li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?php echo BASE_URL; ?>admin-bhw-users">BHW Management</a>
            </li>
        </ul>

        <div class="p-3 text-white text-center">
            <h5 id="live-clock-time" class="mb-0 fs-4 fw-bold"></h5>
            <span id="live-clock-date" class="fs-6"></span>
        </div>
        <hr class="text-white mt-0">

        <div class="mt-auto p-2">
            <hr class="text-white">
            <a href="<?php echo BASE_URL; ?>admin-profile" class="d-flex align-items-center text-white text-decoration-none p-2 rounded hover-bg-secondary">
                <img src="https://via.placeholder.com/40" alt="Profile" width="40" height="40" class="rounded-circle me-2">
                <strong><?php echo htmlspecialchars($_SESSION['bhw_full_name'] ?? 'Profile'); ?></strong>
            </a>
        </div>
    </aside>

    <div class="content d-flex flex-column" style="min-height:100vh">
        <nav class="navbar navbar-light bg-light shadow-sm">
            <div class="container-fluid">
                <span class="navbar-text">Welcome, <?php echo isset($_SESSION['bhw_full_name']) ? htmlspecialchars($_SESSION['bhw_full_name']) : 'BHW'; ?></span>
                <div class="d-flex align-items-center">
                    <a class="nav-link me-2" href="<?php echo BASE_URL; ?>" target="_blank">View Public Site</a>
                    <a class="btn btn-outline-secondary btn-sm" href="<?php echo BASE_URL; ?>?action=logout">Logout</a>
                </div>
            </div>
        </nav>

        <main class="p-4">
<?php
// Admin header + nav (placeholder)
?>
<!-- <!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin - E-BHM-CONNECT</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
</header>
<main> -->
