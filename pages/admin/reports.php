<?php
// pages/admin/reports.php
include_once __DIR__ . '/../../includes/header_admin.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Report Generation</h1>
    </div>

    <div class="card">
        <div class="card-header">Available Reports</div>
        <div class="card-body">
            <div class="list-group">
                <a href="<?php echo BASE_URL; ?>?action=report-patient-list" target="_blank" class="list-group-item list-group-item-action">Full Patient List</a>
                <a href="<?php echo BASE_URL; ?>?action=report-inventory-stock" target="_blank" class="list-group-item list-group-item-action">Current Inventory Stock</a>
                <a href="<?php echo BASE_URL; ?>?action=report-chronic-disease" target="_blank" class="list-group-item list-group-item-action">Patients with Chronic Disease</a>
            </div>
        </div>
    </div>

</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
<?php
// Admin: Reports (placeholder)
require_once __DIR__ . '/../../includes/header_admin.php';
?>
<?php require_once __DIR__ . '/../../includes/footer_admin.php';
