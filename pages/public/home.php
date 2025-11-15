<!DOCTYPE html>
<?php
// Include the public header
include_once __DIR__ . '/../../includes/header_public.php';
?>

<div class="p-5 mb-4 bg-light rounded-3 shadow-sm">
  <div class="container-fluid py-5 text-center">
    <h1 class="display-5 fw-bold text-primary">Welcome to E-BHM Connect</h1>
    <p class="fs-4 text-muted mt-3">Digitizing health services for efficiency, accuracy, and accessibility in Barangay Bacong.</p>
    <a href="login-patient" class="btn btn-success btn-lg mt-4">
      Access Your Patient Portal
    </a>
  </div>
</div>

<div class="row g-4">
  <div class="col-md-4">
    <div class="card h-100 shadow-sm">
      <div class="card-body">
        <h2 class="card-title text-primary-emphasis">For Residents</h2>
        <p class="card-text">Access your health records, view immunization history, and get updates from your BHW.</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100 shadow-sm">
      <div class="card-body">
        <h2 class="card-title text-primary-emphasis">For BHWs</h2>
        <p class="card-text">Manage patient records, track supplies, and monitor health programs all in one place.</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100 shadow-sm">
      <div class="card-body">
        <h2 class="card-title text-primary-emphasis">Stay Informed</h2>
        <p class="card-text">Get the latest health announcements and program schedules for our barangay.</p>
      </div>
    </div>
  </div>
</div>

<?php
// Include the public footer
include_once __DIR__ . '/../../includes/footer_public.php';
?>