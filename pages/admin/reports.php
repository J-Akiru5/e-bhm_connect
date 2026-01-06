<?php
// Modern Report Generation Page - Simplified
include_once __DIR__ . '/../../includes/header_admin.php';

// Check permission
if (!has_permission('view_reports')):
?>
<div class="container" style="padding: 24px;">
    <div class="glass-card text-center py-5">
        <div class="empty-state-icon mb-4">
            <i class="fas fa-lock" style="font-size: 48px; color: var(--text-muted);"></i>
        </div>
        <h3>Access Restricted</h3>
        <p class="text-muted">You do not have permission to access Reports. Please contact your administrator.</p>
        <a href="<?php echo BASE_URL; ?>admin-dashboard" class="btn-primary-glass">Return to Dashboard</a>
    </div>
</div>
<?php
include_once __DIR__ . '/../../includes/footer_admin.php';
exit;
endif;
?>

<div class="container" style="padding: 24px;">
    <div class="page-header">
        <div>
            <h1 class="page-title">📊 Report Generation</h1>
            <p class="page-subtitle">Generate and export comprehensive health records as PDF</p>
        </div>
    </div>

    <!-- Date Filter Section -->
    <div class="glass-card mb-4" style="padding: 16px 24px;">
        <form id="reportFilterForm" class="d-flex flex-wrap align-items-end gap-3">
            <div>
                <label class="form-label small mb-1">Start Date</label>
                <input type="date" class="form-control" id="startDate" name="start_date">
            </div>
            <div>
                <label class="form-label small mb-1">End Date</label>
                <input type="date" class="form-control" id="endDate" name="end_date">
            </div>
            <button type="button" class="btn btn-secondary" onclick="clearDates()">
                <i class="fas fa-times"></i> Clear
            </button>
        </form>
    </div>

    <h5 class="mb-3" style="color: var(--text-secondary);"><i class="fas fa-folder-open me-2"></i>General Reports</h5>
    <div class="reports-grid mb-4">
        <!-- Patient List Report -->
        <div class="glass-card report-card">
            <div class="report-icon patients">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="report-title">Patient List Report</h3>
            <p class="report-description">All registered patients with demographic information</p>
            <div class="report-actions">
                <button class="btn-report-glass primary" onclick="generateReport('report-patient-list')">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </button>
            </div>
        </div>

        <!-- Inventory Stock Report -->
        <div class="glass-card report-card">
            <div class="report-icon inventory">
                <i class="fas fa-box"></i>
            </div>
            <h3 class="report-title">Inventory Stock Report</h3>
            <p class="report-description">Current stock levels and inventory status</p>
            <div class="report-actions">
                <button class="btn-report-glass primary" onclick="generateReport('report-inventory-stock')">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </button>
            </div>
        </div>

        <!-- Medicine Dispensing Report -->
        <div class="glass-card report-card">
            <div class="report-icon medicines">
                <i class="fas fa-pills"></i>
            </div>
            <h3 class="report-title">Medicine Dispensing Report</h3>
            <p class="report-description">Track medicine distribution to patients</p>
            <div class="report-actions">
                <button class="btn-report-glass primary" onclick="generateReport('report-medicine-dispensing')">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </button>
            </div>
        </div>

        <!-- BHW Activity Report -->
        <div class="glass-card report-card">
            <div class="report-icon bhw">
                <i class="fas fa-user-nurse"></i>
            </div>
            <h3 class="report-title">BHW Activity Report</h3>
            <p class="report-description">Health worker activities and patient visits</p>
            <div class="report-actions">
                <button class="btn-report-glass primary" onclick="generateReport('report-bhw-activity')">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </button>
            </div>
        </div>

        <!-- Visit Records Report -->
        <div class="glass-card report-card">
            <div class="report-icon visits">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h3 class="report-title">Visit Records Report</h3>
            <p class="report-description">Detailed log of all patient visits</p>
            <div class="report-actions">
                <button class="btn-report-glass primary" onclick="generateReport('report-visit-records')">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </button>
            </div>
        </div>

        <!-- Chronic Disease Report -->
        <div class="glass-card report-card">
            <div class="report-icon chronic">
                <i class="fas fa-heartbeat"></i>
            </div>
            <h3 class="report-title">Chronic Disease Report</h3>
            <p class="report-description">Patients with chronic conditions and treatments</p>
            <div class="report-actions">
                <button class="btn-report-glass primary" onclick="generateReport('report-chronic-disease')">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </button>
            </div>
        </div>
    </div>

    <h5 class="mb-3" style="color: var(--text-secondary);"><i class="fas fa-notes-medical me-2"></i>Health Records Reports</h5>
    <div class="reports-grid">
        <!-- Pregnancy Tracking Report -->
        <div class="glass-card report-card">
            <div class="report-icon" style="background: linear-gradient(135deg, #ec4899, #f472b6);">
                <i class="fas fa-baby"></i>
            </div>
            <h3 class="report-title">Pregnancy Tracking Report</h3>
            <p class="report-description">Monitor pregnant women from identification to delivery</p>
            <div class="report-actions">
                <button class="btn-report-glass primary" onclick="generateReport('report-pregnancy')">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </button>
            </div>
        </div>

        <!-- Child Care Report -->
        <div class="glass-card report-card">
            <div class="report-icon" style="background: linear-gradient(135deg, #06b6d4, #22d3ee);">
                <i class="fas fa-child"></i>
            </div>
            <h3 class="report-title">Child Care Report</h3>
            <p class="report-description">Child immunization and supplementation records (12-59 months)</p>
            <div class="report-actions">
                <button class="btn-report-glass primary" onclick="generateReport('report-child-care')">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </button>
            </div>
        </div>

        <!-- Natality Records Report -->
        <div class="glass-card report-card">
            <div class="report-icon" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);">
                <i class="fas fa-birthday-cake"></i>
            </div>
            <h3 class="report-title">Natality Records Report</h3>
            <p class="report-description">All birth records in the barangay</p>
            <div class="report-actions">
                <button class="btn-report-glass primary" onclick="generateReport('report-natality')">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </button>
            </div>
        </div>

        <!-- WRA Tracking Report -->
        <div class="glass-card report-card">
            <div class="report-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
                <i class="fas fa-female"></i>
            </div>
            <h3 class="report-title">WRA Tracking Report</h3>
            <p class="report-description">Women of Reproductive Age tracking</p>
            <div class="report-actions">
                <button class="btn-report-glass primary" onclick="generateReport('report-wra')">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function generateReport(action) {
    let url = '<?php echo BASE_URL; ?>?action=' + action;
    
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (startDate) {
        url += '&start_date=' + startDate;
    }
    if (endDate) {
        url += '&end_date=' + endDate;
    }
    
    window.open(url, '_blank');
}

function clearDates() {
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
}
</script>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
