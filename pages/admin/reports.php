<?php
// Modern Report Generation Page with Glassmorphism
include_once __DIR__ . '/../../includes/header_admin.php';

// Check permission - if user doesn't have view_reports, show access denied
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
            <p class="page-subtitle">Generate and export comprehensive health records</p>
        </div>
    </div>

    <div class="reports-grid">
        <!-- Patient List Report -->
        <div class="glass-card report-card" onclick="openCustomizeModal('patient-list')">
            <div class="report-icon patients">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="report-title">Patient List Report</h3>
            <p class="report-description">Comprehensive list of all registered patients with demographic information</p>
            <div class="report-actions">
                <a href="<?php echo BASE_URL; ?>?action=report-patient-list" target="_blank" class="btn-report-glass primary" onclick="event.stopPropagation();">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </a>
                <button class="btn-report-glass" onclick="openCustomizeModal('patient-list'); event.stopPropagation();">
                    <i class="fas fa-sliders-h"></i> Customize
                </button>
            </div>
        </div>

        <!-- Inventory Stock Report -->
        <div class="glass-card report-card" onclick="openCustomizeModal('inventory')">
            <div class="report-icon inventory">
                <i class="fas fa-box"></i>
            </div>
            <h3 class="report-title">Inventory Stock Report</h3>
            <p class="report-description">Current stock levels and inventory status of all medical supplies</p>
            <div class="report-actions">
                <a href="<?php echo BASE_URL; ?>?action=report-inventory-stock" target="_blank" class="btn-report-glass primary" onclick="event.stopPropagation();">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </a>
                <button class="btn-report-glass" onclick="openCustomizeModal('inventory'); event.stopPropagation();">
                    <i class="fas fa-sliders-h"></i> Customize
                </button>
            </div>
        </div>

        <!-- Chronic Disease Report -->
        <div class="glass-card report-card" onclick="openCustomizeModal('chronic')">
            <div class="report-icon chronic">
                <i class="fas fa-heartbeat"></i>
            </div>
            <h3 class="report-title">Chronic Disease Report</h3>
            <p class="report-description">Patients with chronic conditions and ongoing treatment records</p>
            <div class="report-actions">
                <a href="<?php echo BASE_URL; ?>?action=report-chronic-disease" target="_blank" class="btn-report-glass primary" onclick="event.stopPropagation();">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </a>
                <button class="btn-report-glass" onclick="openCustomizeModal('chronic'); event.stopPropagation();">
                    <i class="fas fa-sliders-h"></i> Customize
                </button>
            </div>
        </div>

        <!-- BHW Activity Report -->
        <div class="glass-card report-card" onclick="openCustomizeModal('bhw')">
            <div class="report-icon bhw">
                <i class="fas fa-user-nurse"></i>
            </div>
            <h3 class="report-title">BHW Activity Report</h3>
            <p class="report-description">Community health worker activities and patient visit summaries</p>
            <div class="report-actions">
                <a href="<?php echo BASE_URL; ?>?action=report-bhw-record" target="_blank" class="btn-report-glass primary" onclick="event.stopPropagation();">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </a>
                <button class="btn-report-glass" onclick="openCustomizeModal('bhw'); event.stopPropagation();">
                    <i class="fas fa-sliders-h"></i> Customize
                </button>
            </div>
        </div>

        <!-- Visit Records Report -->
        <div class="glass-card report-card" onclick="openCustomizeModal('visits')">
            <div class="report-icon visits">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h3 class="report-title">Visit Records Report</h3>
            <p class="report-description">Detailed log of all patient visits and consultations</p>
            <div class="report-actions">
                <a href="<?php echo BASE_URL; ?>?action=report-patient-record" target="_blank" class="btn-report-glass primary" onclick="event.stopPropagation();">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </a>
                <button class="btn-report-glass" onclick="openCustomizeModal('visits'); event.stopPropagation();">
                    <i class="fas fa-sliders-h"></i> Customize
                </button>
            </div>
        </div>

        <!-- Medicine Dispensing Report -->
        <div class="glass-card report-card" onclick="openCustomizeModal('medicines')">
            <div class="report-icon medicines">
                <i class="fas fa-pills"></i>
            </div>
            <h3 class="report-title">Medicine Dispensing Report</h3>
            <p class="report-description">Track medicine distribution and medication usage patterns</p>
            <div class="report-actions">
                <button class="btn-report-glass primary" onclick="alert('This report is coming soon!'); event.stopPropagation();">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </button>
                <button class="btn-report-glass" onclick="openCustomizeModal('medicines'); event.stopPropagation();">
                    <i class="fas fa-sliders-h"></i> Customize
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Customize Report Modal -->
<div class="modal-overlay" id="customizeModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="fas fa-sliders-h"></i> Customize Report
            </h2>
            <button class="modal-close" onclick="closeCustomizeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="customizeForm" onsubmit="generateCustomReport(event)">
                <input type="hidden" id="reportType" name="reportType">
                
                <div class="filter-group">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar"></i> Start Date
                        </label>
                        <input type="date" class="glass-input" id="startDate" name="startDate">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar"></i> End Date
                        </label>
                        <input type="date" class="glass-input" id="endDate" name="endDate">
                    </div>
                </div>

                <div class="form-group" id="statusFilterGroup" style="display: none;">
                    <label class="form-label">
                        <i class="fas fa-filter"></i> Filter by Status
                    </label>
                    <select class="glass-select" id="statusFilter" name="status">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="form-group" id="categoryFilterGroup" style="display: none;">
                    <label class="form-label">
                        <i class="fas fa-filter"></i> Filter by Category
                    </label>
                    <select class="glass-select" id="categoryFilter" name="category">
                        <option value="">All Categories</option>
                        <option value="medicines">Medicines</option>
                        <option value="equipment">Equipment</option>
                        <option value="supplies">Supplies</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-file-export"></i> Export Format
                    </label>
                    <select class="glass-select" id="exportFormat" name="format">
                        <option value="pdf">PDF Document</option>
                        <option value="excel">Excel Spreadsheet</option>
                        <option value="csv">CSV File</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-cog"></i> Include Options
                        </label>
                    <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 8px;">
                        <label style="display: flex; align-items: center; gap: 8px; color: rgba(255,255,255,0.8); cursor: pointer;">
                            <input type="checkbox" name="includeCharts" style="width: 18px; height: 18px;">
                            <span>Include Charts & Graphs</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; color: rgba(255,255,255,0.8); cursor: pointer;">
                            <input type="checkbox" name="includeSummary" checked style="width: 18px; height: 18px;">
                            <span>Include Summary Statistics</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; color: rgba(255,255,255,0.8); cursor: pointer;">
                            <input type="checkbox" name="includeDetails" checked style="width: 18px; height: 18px;">
                            <span>Include Detailed Records</span>
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary-glass" onclick="closeCustomizeModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="submit" form="customizeForm" class="btn-primary-glass">
                <i class="fas fa-download"></i> Generate Report
            </button>
        </div>
    </div>
</div>

<script>
function openCustomizeModal(reportType) {
    document.getElementById('reportType').value = reportType;
    document.getElementById('customizeModal').classList.add('active');
    
    // Show/hide relevant filters based on report type
    const statusGroup = document.getElementById('statusFilterGroup');
    const categoryGroup = document.getElementById('categoryFilterGroup');
    
    statusGroup.style.display = 'none';
    categoryGroup.style.display = 'none';
    
    if (reportType === 'patient-list') {
        statusGroup.style.display = 'block';
    } else if (reportType === 'inventory') {
        categoryGroup.style.display = 'block';
    }
}

function closeCustomizeModal() {
    document.getElementById('customizeModal').classList.remove('active');
    document.getElementById('customizeForm').reset();
}

function generateCustomReport(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const reportType = formData.get('reportType');
    
    // Build the query string with filters
    let url = '<?php echo BASE_URL; ?>?';
    
    switch(reportType) {
        case 'patient-list':
            url += 'action=report-patient-list';
            break;
        case 'inventory':
            url += 'action=report-inventory-stock';
            break;
        case 'chronic':
            url += 'action=report-chronic-disease';
            break;
        case 'bhw':
            url += 'action=report-bhw-record';
            break;
        case 'visits':
            url += 'action=report-patient-record';
            break;
        default:
            alert('This report is not yet available.');
            return;
    }
    
    // Add date filters if provided
    if (formData.get('startDate')) {
        url += '&start_date=' + formData.get('startDate');
    }
    if (formData.get('endDate')) {
        url += '&end_date=' + formData.get('endDate');
    }
    if (formData.get('status')) {
        url += '&status=' + formData.get('status');
    }
    if (formData.get('category')) {
        url += '&category=' + formData.get('category');
    }
    
    // Open in new tab
    window.open(url, '_blank');
    closeCustomizeModal();
}

// Close modal when clicking outside
document.getElementById('customizeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCustomizeModal();
    }
});
</script>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
