<?php
// Modern Report Generation Page with Glassmorphism
include_once __DIR__ . '/../../includes/header_admin.php';
?>
<style>
/* Glassmorphism Report Design */
.glass-card { background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 16px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); transition: transform 0.25s ease, box-shadow 0.25s ease; }
.glass-card:hover { transform: translateY(-4px); box-shadow: 0 16px 48px rgba(0, 0, 0, 0.16); }
.page-header { display: flex; flex-direction: column; gap: 16px; margin-bottom: 32px; }
@media (min-width: 768px) { .page-header { flex-direction: row; justify-content: space-between; align-items: center; } }
.page-title { font-size: 1.75rem; font-weight: 700; color: #ffffff; margin: 0; }
.page-subtitle { color: rgba(255, 255, 255, 0.6); font-size: 0.875rem; margin-top: 4px; }
.reports-grid { display: grid; grid-template-columns: repeat(1, 1fr); gap: 20px; margin-bottom: 24px; }
@media (min-width: 768px) { .reports-grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1024px) { .reports-grid { grid-template-columns: repeat(3, 1fr); } }
.report-card { padding: 24px; cursor: pointer; position: relative; overflow: hidden; }
.report-card:before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: linear-gradient(180deg, #20c997, #0f5132); transition: width 0.3s ease; }
.report-card:hover:before { width: 100%; opacity: 0.05; }
.report-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 16px; }
.report-icon.patients { background: rgba(32, 201, 151, 0.2); color: #20c997; }
.report-icon.inventory { background: rgba(99, 102, 241, 0.2); color: #6366f1; }
.report-icon.chronic { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
.report-icon.bhw { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.report-icon.visits { background: rgba(139, 92, 246, 0.2); color: #8b5cf6; }
.report-icon.medicines { background: rgba(236, 72, 153, 0.2); color: #ec4899; }
.report-title { font-size: 1.125rem; font-weight: 600; color: #ffffff; margin-bottom: 8px; }
.report-description { color: rgba(255, 255, 255, 0.6); font-size: 0.875rem; line-height: 1.5; margin-bottom: 20px; }
.report-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.btn-report-glass { padding: 10px 20px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 10px; color: #ffffff; font-weight: 500; font-size: 0.8rem; cursor: pointer; transition: all 0.25s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-report-glass:hover { background: rgba(32, 201, 151, 0.2); border-color: #20c997; color: #20c997; }
.btn-report-glass.primary { background: linear-gradient(135deg, #20c997, #0f5132); border: none; box-shadow: 0 4px 12px rgba(32, 201, 151, 0.3); }
.btn-report-glass.primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(32, 201, 151, 0.4); color: #ffffff; }
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 1000; opacity: 0; visibility: hidden; transition: all 0.3s ease; padding: 16px; }
.modal-overlay.active { opacity: 1; visibility: visible; }
.modal-content { background: rgba(30, 41, 59, 0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 24px; width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto; transform: translateY(20px) scale(0.95); transition: all 0.3s ease; box-shadow: 0 24px 64px rgba(0, 0, 0, 0.4); }
.modal-overlay.active .modal-content { transform: translateY(0) scale(1); }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 24px 28px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
.modal-title { font-size: 1.25rem; font-weight: 600; color: #ffffff; margin: 0; }
.modal-close { width: 36px; height: 36px; border-radius: 10px; background: rgba(255, 255, 255, 0.1); border: none; color: rgba(255, 255, 255, 0.7); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; }
.modal-close:hover { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
.modal-body { padding: 28px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 12px; padding: 20px 28px; border-top: 1px solid rgba(255, 255, 255, 0.1); }
.form-group { margin-bottom: 20px; }
.form-label { display: block; font-weight: 500; color: rgba(255, 255, 255, 0.9); font-size: 0.875rem; margin-bottom: 8px; }
.glass-input { width: 100%; padding: 12px 16px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 12px; color: #ffffff; font-size: 1rem; transition: all 0.25s ease; }
.glass-input:hover { background: rgba(255, 255, 255, 0.12); border-color: rgba(255, 255, 255, 0.25); }
.glass-input:focus { outline: none; background: rgba(255, 255, 255, 0.15); border: 2px solid #20c997; box-shadow: 0 0 0 4px rgba(32, 201, 151, 0.15); }
.glass-input::placeholder { color: rgba(255, 255, 255, 0.4); }
.glass-select { width: 100%; padding: 12px 16px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 12px; color: #ffffff; font-size: 1rem; transition: all 0.25s ease; cursor: pointer; }
.glass-select:focus { outline: none; background: rgba(255, 255, 255, 0.15); border: 2px solid #20c997; box-shadow: 0 0 0 4px rgba(32, 201, 151, 0.15); }
.glass-select option { background: #1e293b; color: #ffffff; }
.btn-secondary-glass { padding: 12px 24px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 12px; color: #ffffff; font-weight: 500; cursor: pointer; transition: all 0.25s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
.btn-secondary-glass:hover { background: rgba(255, 255, 255, 0.15); border-color: rgba(255, 255, 255, 0.25); color: #ffffff; }
.btn-primary-glass { padding: 12px 24px; background: linear-gradient(135deg, #20c997, #0f5132); border: none; border-radius: 12px; color: #ffffff; font-weight: 600; cursor: pointer; transition: all 0.25s ease; box-shadow: 0 4px 16px rgba(32, 201, 151, 0.35); display: inline-flex; align-items: center; gap: 8px; }
.btn-primary-glass:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(32, 201, 151, 0.45); color: #ffffff; }
.filter-group { display: grid; grid-template-columns: repeat(1, 1fr); gap: 16px; }
@media (min-width: 768px) { .filter-group { grid-template-columns: repeat(2, 1fr); } }
</style>

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
