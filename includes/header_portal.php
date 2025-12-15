<?php
// includes/header_portal.php
// Simple header for logged-in patient portal
// (index.php handles session_start() and $page variable)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Portal - E-BHM Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --brand-primary: #20c997;
            --brand-dark: #0f5132;
            --brand-light: #e6fffa;
        }
        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex-grow: 1;
        }
        /* Ensure buttons use teal theme */
        .btn-primary {
            background-color: var(--brand-primary);
            border-color: var(--brand-primary);
        }
        .btn-primary:hover {
            background-color: var(--brand-dark);
            border-color: var(--brand-dark);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top" style="background-color: #20c997;">
        <div class="container">
            <a class="navbar-brand fs-4 fw-bold" href="<?php echo BASE_URL; ?>portal-dashboard">Patient Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#portalNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="portalNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page === 'portal-dashboard') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>portal-dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page === 'portal-chatbot') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>portal-chatbot">Chat with Gabby</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="<?php echo BASE_URL; ?>?action=logout-patient" class="btn btn-light">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    <main class="container mt-4">
<?php
// Patient portal header (placeholder)
?>
<!-- <!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Patient Portal - E-BHM-CONNECT</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header>
    <h1>Patient Portal</h1>
</header>
<main> -->
