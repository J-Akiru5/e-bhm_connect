<?php
// Admin Dashboard - Glassmorphism Design
include __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../includes/admin_analytics_helper.php';

$data = [];
try {
    $data = fetch_dashboard_data($pdo);
} catch (Throwable $e) {
    error_log('Dashboard fetch error: ' . $e->getMessage());
    $data = [];
}

// Main stats
$total_patients = isset($data['total_patients']) ? (int)$data['total_patients'] : 0;
$total_bhws = isset($data['total_bhws']) ? (int)$data['total_bhws'] : 0;
$total_inventory = isset($data['total_inventory']) ? (int)$data['total_inventory'] : 0;
$low_stock = isset($data['low_stock_items']) ? (int)$data['low_stock_items'] : 0;
$sms_stats = isset($data['sms_stats']) && is_array($data['sms_stats']) ? $data['sms_stats'] : ['sent' => 0, 'failed' => 0, 'pending' => 0];
$sms_sent = isset($sms_stats['sent']) ? (int)$sms_stats['sent'] : 0;
$sms_failed = isset($sms_stats['failed']) ? (int)$sms_stats['failed'] : 0;
$sms_pending = isset($sms_stats['pending']) ? (int)$sms_stats['pending'] : 0;
$recent = isset($data['recent_registrations']) && is_array($data['recent_registrations']) ? $data['recent_registrations'] : [];

// Prepare chart data
$months = [];
$registrations = [];
foreach ($recent as $row) {
    if (isset($row['label'])) {
        $months[] = $row['label'];
    } elseif (isset($row['month'])) {
        $months[] = $row['month'];
    } else {
        $months[] = '';
    }
    $registrations[] = isset($row['count']) ? (int)$row['count'] : 0;
}

// Inventory chart data
$inventoryChart = [];
try {
    $inventoryChart = get_inventory_chart_data($pdo, 8);
} catch (Throwable $e) {
    error_log('Inventory chart error: ' . $e->getMessage());
}

// Recent visits
$recentVisits = [];
try {
    $recentVisits = get_recent_visits($pdo, 5);
} catch (Throwable $e) {
    error_log('Recent visits error: ' . $e->getMessage());
}

// Recent audit logs
$auditLogs = [];
try {
    $auditLogs = get_recent_audit_logs($pdo, 5);
} catch (Throwable $e) {
    error_log('Audit logs error: ' . $e->getMessage());
}

// Visits chart
$visitsChart = [];
try {
    $visitsChart = get_visits_chart_data($pdo);
} catch (Throwable $e) {
    error_log('Visits chart error: ' . $e->getMessage());
}

// Health Records Stats
$healthStats = [
    'pregnancy' => 0,
    'childcare' => 0,
    'natality' => 0,
    'mortality' => 0,
    'chronic' => 0,
    'ntp' => 0,
    'wra' => 0
];
try {
    // These tables might not exist yet, so check before querying
    $tables = ['pregnancy_tracking', 'child_care_records', 'natality_records', 'mortality_records', 
               'chronic_disease_masterlist', 'ntp_client_monitoring', 'wra_tracking'];
    $stmt = $pdo->query("SHOW TABLES");
    $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (in_array('pregnancy_tracking', $existingTables)) {
        $healthStats['pregnancy'] = (int)$pdo->query("SELECT COUNT(*) FROM pregnancy_tracking")->fetchColumn();
    }
    if (in_array('child_care_records', $existingTables)) {
        $healthStats['childcare'] = (int)$pdo->query("SELECT COUNT(*) FROM child_care_records")->fetchColumn();
    }
    if (in_array('natality_records', $existingTables)) {
        $healthStats['natality'] = (int)$pdo->query("SELECT COUNT(*) FROM natality_records")->fetchColumn();
    }
    if (in_array('mortality_records', $existingTables)) {
        $healthStats['mortality'] = (int)$pdo->query("SELECT COUNT(*) FROM mortality_records")->fetchColumn();
    }
    if (in_array('chronic_disease_masterlist', $existingTables)) {
        $healthStats['chronic'] = (int)$pdo->query("SELECT COUNT(*) FROM chronic_disease_masterlist")->fetchColumn();
    }
    if (in_array('ntp_client_monitoring', $existingTables)) {
        $healthStats['ntp'] = (int)$pdo->query("SELECT COUNT(*) FROM ntp_client_monitoring")->fetchColumn();
    }
    if (in_array('wra_tracking', $existingTables)) {
        $healthStats['wra'] = (int)$pdo->query("SELECT COUNT(*) FROM wra_tracking")->fetchColumn();
    }
} catch (Throwable $e) {
    error_log('Health records stats error: ' . $e->getMessage());
}
?>

<div class="container-fluid py-4 fade-in">
    <!-- Welcome Banner -->
    <div class="glass-card mb-4" style="background: linear-gradient(135deg, rgba(32, 201, 151, 0.15), rgba(99, 102, 241, 0.1));">
        <div class="glass-card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-1"><?php echo __('dashboard.welcome_message', ['name' => htmlspecialchars($_SESSION['bhw_full_name'] ?? 'Admin')]); ?></h1>
                <p class="text-secondary mb-0">
                    <span id="live-clock-date"></span> • <span id="live-clock-time"></span>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo BASE_URL; ?>admin-patients" class="btn btn-primary"><?php echo __('dashboard.quick_actions'); ?></a>
            </div>
        </div>
    </div>

    <!-- Stat Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value"><?php echo number_format($total_patients); ?></div>
                    <div class="stat-card-label"><?php echo __('dashboard.total_patients'); ?></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-card-icon secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/>
                    </svg>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value"><?php echo number_format($total_bhws); ?></div>
                    <div class="stat-card-label"><?php echo __('dashboard.total_bhws'); ?></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-card-icon warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value"><?php echo number_format($total_inventory); ?></div>
                    <div class="stat-card-label"><?php echo __('dashboard.total_inventory'); ?></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-card-icon <?php echo $low_stock > 0 ? 'danger' : ''; ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value"><?php echo number_format($low_stock); ?></div>
                    <div class="stat-card-label"><?php echo __('dashboard.low_stock_items'); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Health Records Quick Stats -->
    <div class="glass-card mb-4">
        <div class="glass-card-header d-flex justify-content-between align-items-center">
            <h5 class="glass-card-title mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2" style="vertical-align: -3px;">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
                Health Records Overview
            </h5>
            <a href="<?php echo BASE_URL; ?>admin-health-records" class="btn btn-sm btn-glass">View All →</a>
        </div>
        <div class="glass-card-body">
            <div class="row g-3">
                <div class="col-6 col-md-4 col-xl">
                    <a href="<?php echo BASE_URL; ?>admin-health-records-pregnancy" class="text-decoration-none">
                        <div class="p-3 rounded text-center" style="background: rgba(236, 72, 153, 0.1); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="fw-bold fs-4" style="color: #ec4899;"><?php echo number_format($healthStats['pregnancy']); ?></div>
                            <div class="small text-muted">Pregnancy</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-xl">
                    <a href="<?php echo BASE_URL; ?>admin-health-records-childcare" class="text-decoration-none">
                        <div class="p-3 rounded text-center" style="background: rgba(245, 158, 11, 0.1); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="fw-bold fs-4" style="color: #f59e0b;"><?php echo number_format($healthStats['childcare']); ?></div>
                            <div class="small text-muted">Child Care</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-xl">
                    <a href="<?php echo BASE_URL; ?>admin-health-records-natality" class="text-decoration-none">
                        <div class="p-3 rounded text-center" style="background: rgba(16, 185, 129, 0.1); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="fw-bold fs-4" style="color: #10b981;"><?php echo number_format($healthStats['natality']); ?></div>
                            <div class="small text-muted">Births</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-xl">
                    <a href="<?php echo BASE_URL; ?>admin-health-records-mortality" class="text-decoration-none">
                        <div class="p-3 rounded text-center" style="background: rgba(107, 114, 128, 0.1); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="fw-bold fs-4" style="color: #6b7280;"><?php echo number_format($healthStats['mortality']); ?></div>
                            <div class="small text-muted">Deaths</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-xl">
                    <a href="<?php echo BASE_URL; ?>admin-health-records-chronic" class="text-decoration-none">
                        <div class="p-3 rounded text-center" style="background: rgba(239, 68, 68, 0.1); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="fw-bold fs-4" style="color: #ef4444;"><?php echo number_format($healthStats['chronic']); ?></div>
                            <div class="small text-muted">Chronic</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-xl">
                    <a href="<?php echo BASE_URL; ?>admin-health-records-ntp" class="text-decoration-none">
                        <div class="p-3 rounded text-center" style="background: rgba(139, 92, 246, 0.1); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="fw-bold fs-4" style="color: #8b5cf6;"><?php echo number_format($healthStats['ntp']); ?></div>
                            <div class="small text-muted">NTP (TB)</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-xl">
                    <a href="<?php echo BASE_URL; ?>admin-health-records-wra" class="text-decoration-none">
                        <div class="p-3 rounded text-center" style="background: rgba(59, 130, 246, 0.1); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="fw-bold fs-4" style="color: #3b82f6;"><?php echo number_format($healthStats['wra']); ?></div>
                            <div class="small text-muted">WRA</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Medicine Inventory Bar Chart -->
        <div class="col-12 col-lg-7">
            <div class="chart-card glass-card">
                <div class="chart-card-header">
                    <h5 class="chart-card-title"><?php echo __('dashboard.medicine_stock_levels'); ?></h5>
                    <a href="<?php echo BASE_URL; ?>admin-inventory" class="btn btn-sm btn-glass"><?php echo __('view'); ?> →</a>
                </div>
                <div class="card-body">
                    <canvas id="inventoryBarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- SMS Delivery Status -->
        <div class="col-12 col-lg-5">
            <div class="chart-card glass-card">
                <div class="chart-card-header">
                    <h5 class="chart-card-title"><?php echo __('dashboard.sms_delivery_status'); ?></h5>
                </div>
                <div class="card-body">
                    <canvas id="smsPieChart"></canvas>
                    <div class="mt-3 d-flex justify-content-center gap-4 small">
                        <span><span class="status-dot active"></span> <?php echo __('dashboard.sms_sent'); ?> (<?php echo $sms_sent; ?>)</span>
                        <span><span class="status-dot inactive"></span> <?php echo __('dashboard.sms_failed'); ?> (<?php echo $sms_failed; ?>)</span>
                        <span><span class="status-dot pending"></span> <?php echo __('dashboard.sms_pending'); ?> (<?php echo $sms_pending; ?>)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Patient Registration Trends -->
        <div class="col-12 col-lg-6">
            <div class="chart-card glass-card">
                <div class="chart-card-header">
                    <h5 class="chart-card-title"><?php echo __('dashboard.patient_registration_trends'); ?></h5>
                </div>
                <div class="card-body">
                    <canvas id="regLineChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Visits Trend -->
        <div class="col-12 col-lg-6">
            <div class="chart-card glass-card">
                <div class="chart-card-header">
                    <h5 class="chart-card-title"><?php echo __('dashboard.recent_visits'); ?> <?php echo __('dashboard.statistics'); ?></h5>
                </div>
                <div class="card-body">
                    <canvas id="visitsLineChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Row -->
    <div class="row g-3 mb-4">
        <!-- Recent Visits Table -->
        <div class="col-12 col-lg-6">
            <div class="glass-card">
                <div class="glass-card-header d-flex justify-content-between align-items-center">
                    <h5 class="glass-card-title mb-0"><?php echo __('dashboard.recent_visits'); ?></h5>
                    <a href="<?php echo BASE_URL; ?>admin-patients" class="btn btn-sm btn-glass"><?php echo __('view'); ?> →</a>
                </div>
                <div class="glass-card-body p-0">
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th><?php echo __('patients.full_name'); ?></th>
                                    <th><?php echo __('patients.visits'); ?></th>
                                    <th><?php echo __('date'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentVisits)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4"><?php echo __('no_data'); ?></td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($recentVisits as $visit): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-sm" style="width:32px;height:32px;border-radius:8px;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:600;">
                                                    <?php echo strtoupper(substr($visit['patient_name'] ?? 'P', 0, 2)); ?>
                                                </div>
                                                <span><?php echo htmlspecialchars($visit['patient_name'] ?? 'Unknown'); ?></span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-primary"><?php echo htmlspecialchars($visit['visit_type'] ?? 'General'); ?></span></td>
                                        <td><?php echo date('M j, Y', strtotime($visit['visit_date'] ?? $visit['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit Log Timeline -->
        <div class="col-12 col-lg-6">
            <div class="glass-card">
                <div class="glass-card-header d-flex justify-content-between align-items-center">
                    <h5 class="glass-card-title mb-0"><?php echo __('dashboard.audit_logs'); ?></h5>
                    <?php if (is_admin()): ?>
                    <a href="<?php echo BASE_URL; ?>admin-audit-logs" class="btn btn-sm btn-glass"><?php echo __('view'); ?> →</a>
                    <?php endif; ?>
                </div>
                <div class="glass-card-body">
                    <div class="audit-timeline">
                        <?php if (empty($auditLogs)): ?>
                        <p class="text-muted text-center py-4"><?php echo __('no_data'); ?></p>
                        <?php else: ?>
                            <?php foreach ($auditLogs as $log): ?>
                            <div class="audit-item d-flex gap-3 mb-3 pb-3" style="border-bottom: 1px solid var(--border-color);">
                                <div class="audit-icon" style="width:36px;height:36px;border-radius:8px;background:rgba(var(--primary-rgb),0.15);color:var(--primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <?php
                                    $actionIcon = match(strtolower($log['action'] ?? '')) {
                                        'login' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>',
                                        'logout' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
                                        'create', 'insert' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
                                        'update', 'edit' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
                                        'delete' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>',
                                        default => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
                                    };
                                    echo $actionIcon;
                                    ?>
                                </div>
                                <div class="audit-content flex-grow-1">
                                    <div class="audit-text">
                                        <strong><?php echo htmlspecialchars($log['user_name'] ?? 'System'); ?></strong>
                                        <span class="text-secondary"><?php echo htmlspecialchars(ucfirst($log['action'] ?? 'action')); ?></span>
                                        <?php if (!empty($log['entity_type'])): ?>
                                        <span class="badge badge-secondary"><?php echo htmlspecialchars($log['entity_type']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="audit-time text-muted small">
                                        <?php echo date('M j, Y g:i A', strtotime($log['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3">
        <div class="col-12">
            <div class="glass-card">
                <div class="glass-card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h5 class="mb-1"><?php echo __('dashboard.quick_actions'); ?></h5>
                            <p class="text-secondary mb-0 small">Frequently used admin shortcuts</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?php echo BASE_URL; ?>admin-patient-form" class="btn btn-primary"><?php echo __('patients.add_patient'); ?></a>
                            <a href="<?php echo BASE_URL; ?>admin-announcement-edit" class="btn btn-secondary"><?php echo __('announcements.create_announcement'); ?></a>
                            <a href="<?php echo BASE_URL; ?>admin-inventory" class="btn btn-glass"><?php echo __('nav.inventory'); ?></a>
                            <a href="<?php echo BASE_URL; ?>admin-reports" class="btn btn-glass"><?php echo __('nav.reports'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Data from PHP
const smsData = {
    sent: <?php echo json_encode($sms_sent); ?>,
    failed: <?php echo json_encode($sms_failed); ?>,
    pending: <?php echo json_encode($sms_pending); ?>
};
const months = <?php echo json_encode($months); ?>;
const registrations = <?php echo json_encode($registrations); ?>;
const inventoryData = <?php echo json_encode($inventoryChart); ?>;
const visitsData = <?php echo json_encode($visitsChart); ?>;

// Get theme colors
function getChartColors() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    return {
        primary: '#20c997',
        secondary: '#6366f1',
        text: isDark ? '#f8fafc' : '#1e293b',
        textMuted: isDark ? '#94a3b8' : '#64748b',
        grid: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
        bg: isDark ? '#1e293b' : '#ffffff'
    };
}

// Chart instances
let smsChart, regChart, inventoryChart, visitsChart;

function initCharts() {
    const colors = getChartColors();
    
    // SMS Pie Chart
    const pieEl = document.getElementById('smsPieChart');
    if (pieEl) {
        if (smsChart) smsChart.destroy();
        smsChart = new Chart(pieEl, {
            type: 'doughnut',
            data: {
                labels: ['<?php echo __('dashboard.sms_sent'); ?>', '<?php echo __('dashboard.sms_failed'); ?>', '<?php echo __('dashboard.sms_pending'); ?>'],
                datasets: [{
                    data: [smsData.sent, smsData.failed, smsData.pending],
                    backgroundColor: ['#10b981', '#ef4444', '#64748b'],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // Registration Line Chart
    const regEl = document.getElementById('regLineChart');
    if (regEl) {
        if (regChart) regChart.destroy();
        regChart = new Chart(regEl, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: '<?php echo __('dashboard.recent_registrations'); ?>',
                    data: registrations,
                    borderColor: colors.primary,
                    backgroundColor: 'rgba(32, 201, 151, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: colors.primary,
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { color: colors.grid }, ticks: { color: colors.textMuted } },
                    y: { beginAtZero: true, grid: { color: colors.grid }, ticks: { color: colors.textMuted, precision: 0 } }
                },
                plugins: { legend: { display: false } }
            }
        });
    }

    // Inventory Bar Chart
    const invEl = document.getElementById('inventoryBarChart');
    if (invEl && inventoryData.labels && inventoryData.labels.length > 0) {
        if (inventoryChart) inventoryChart.destroy();
        inventoryChart = new Chart(invEl, {
            type: 'bar',
            data: {
                labels: inventoryData.labels,
                datasets: [{
                    label: '<?php echo __('inventory.quantity'); ?>',
                    data: inventoryData.quantities,
                    backgroundColor: inventoryData.colors,
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: { grid: { color: colors.grid }, ticks: { color: colors.textMuted } },
                    y: { grid: { display: false }, ticks: { color: colors.textMuted } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            afterLabel: function(ctx) {
                                const alertLevel = inventoryData.alert_levels[ctx.dataIndex];
                                return 'Alert at: ' + alertLevel;
                            }
                        }
                    }
                }
            }
        });
    }

    // Visits Line Chart
    const visitEl = document.getElementById('visitsLineChart');
    if (visitEl && visitsData.labels && visitsData.labels.length > 0) {
        if (visitsChart) visitsChart.destroy();
        visitsChart = new Chart(visitEl, {
            type: 'line',
            data: {
                labels: visitsData.labels,
                datasets: [{
                    label: '<?php echo __('dashboard.recent_visits'); ?>',
                    data: visitsData.counts,
                    borderColor: colors.secondary,
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: colors.secondary
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { color: colors.grid }, ticks: { color: colors.textMuted } },
                    y: { beginAtZero: true, grid: { color: colors.grid }, ticks: { color: colors.textMuted, precision: 0 } }
                },
                plugins: { legend: { display: false } }
            }
        });
    }
}

// Initialize charts after DOM ready
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initCharts, 100);
});

// Re-init on theme change
document.addEventListener('themeChanged', function() {
    setTimeout(initCharts, 50);
});

// Resize handler
let resizeTimeout;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(initCharts, 200);
});
</script>

<?php include __DIR__ . '/../../includes/footer_admin.php'; ?>
