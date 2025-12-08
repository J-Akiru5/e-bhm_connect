<?php
// Admin Dashboard - Medical Premium look
// Includes header (sets auth and $pdo) and analytics helper
include __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../includes/admin_analytics_helper.php';

$data = [];
try {
    $data = fetch_dashboard_data($pdo);
} catch (Throwable $e) {
    error_log('Dashboard fetch error: ' . $e->getMessage());
    $data = [];
}

$total_patients = isset($data['total_patients']) ? (int)$data['total_patients'] : 0;
$total_bhws = isset($data['total_bhws']) ? (int)$data['total_bhws'] : 0;
$sms_stats = isset($data['sms_stats']) && is_array($data['sms_stats']) ? $data['sms_stats'] : ['sent' => 0, 'failed' => 0, 'pending' => 0];
$sms_sent = isset($sms_stats['sent']) ? (int)$sms_stats['sent'] : 0;
$sms_failed = isset($sms_stats['failed']) ? (int)$sms_stats['failed'] : 0;
$sms_pending = isset($sms_stats['pending']) ? (int)$sms_stats['pending'] : 0;
$recent = isset($data['recent_registrations']) && is_array($data['recent_registrations']) ? $data['recent_registrations'] : [];

// Prepare chart arrays: months and values
$months = [];
$registrations = [];
foreach ($recent as $row) {
    // expect each row like ['ym' => '2025-07', 'count' => 5] or ['month' => 'Jul 2025', 'count' => 5]
    if (isset($row['label'])) {
        $months[] = $row['label'];
    } elseif (isset($row['ym'])) {
        $months[] = $row['ym'];
    } elseif (isset($row['month'])) {
        $months[] = $row['month'];
    } else {
        $months[] = '';
    }
    $registrations[] = isset($row['count']) ? (int)$row['count'] : 0;
}

?>

<style>
    :root{ --brand-teal: #B2A08F; --brand-dark: #0b7b72; }
    .stat-card { background: #fff; border-radius: 12px; padding:18px; transition: transform .18s ease, box-shadow .18s ease; }
    .stat-card:hover { transform: translateY(-6px); box-shadow: 0 14px 40px rgba(16,24,32,0.06); }
    .stat-icon { font-size: 1.6rem; color: #fff; }
    .stat-bubble { width:56px; height:56px; border-radius:12px; display:flex; align-items:center; justify-content:center; background:var(--brand-teal); margin-right:12px; box-shadow:0 8px 22px rgba(0,0,0,0.06); }
    .stat-value { font-weight:700; font-size:1.45rem; }
    .stat-label { color: #6c757d; font-size:0.95rem; }
    .card-quick-actions .btn { min-width: 180px; }
    .chart-card { min-height: 320px; border-radius:12px; overflow:hidden; box-shadow:0 8px 30px rgba(4,15,35,0.04); }
    .chart-card .card-body { padding:16px; }
    .chart-card .card-title { margin-bottom:12px; font-weight:600; }
    .chart-header { border-bottom:1px solid #eef2f6; padding-bottom:10px; margin-bottom:12px; }
    /* Ensure canvases have an explicit height so Chart.js sizing is stable */
    #smsPieChart, #regLineChart { display:block; width:100% !important; height:320px !important; }
    @media (max-width: 575.98px) { .chart-card { min-height: 260px; } #smsPieChart, #regLineChart { height:260px !important; } }
</style>

<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Dashboard</h1>
    </div>

    <!-- Top Row - Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-3">
                <div class="stat-card h-100 d-flex align-items-center">
                    <div class="stat-bubble">
                        <svg class="stat-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                          <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3z"/>
                          <path fill-rule="evenodd" d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="stat-value"><?php echo number_format($total_patients); ?></div>
                        <div class="stat-label">Total Patients</div>
                    </div>
                </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="stat-card h-100 d-flex align-items-center">
                <div class="stat-bubble">
                    <svg class="stat-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M8 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                      <path d="M14 14s-1 0-1-1 1-4-5-4-5 3-5 4  -1 1-1 1h12z"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-value"><?php echo number_format($total_bhws); ?></div>
                    <div class="stat-label">Active BHWs</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="stat-card h-100 d-flex align-items-center">
                <div class="stat-bubble" style="background:#ffc107;">
                    <svg class="stat-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M2 1.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 .5.5V3a2 2 0 0 1-1 1.732V6a2 2 0 0 1 1 1.732V10a2 2 0 0 1-1 1.732v1.268a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5V13A2 2 0 0 1 2 11.268V9.999A2 2 0 0 1 1 8.268V6.536A2 2 0 0 1 2 4.804V3.232A2 2 0 0 1 1 3V1.5z"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-value"><?php echo number_format($sms_pending); ?></div>
                    <div class="stat-label">Pending SMS</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="stat-card h-100 d-flex align-items-center">
                <div class="stat-bubble" style="background: #198754;">
                    <svg class="stat-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.97 10.97l-2.47-2.47 1.06-1.06 1.41 1.41 3.47-3.47 1.06 1.06-4.53 4.53z"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-value"><?php echo number_format($sms_sent); ?></div>
                    <div class="stat-label">Sent SMS</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Middle Row - Charts -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm chart-card">
                <div class="card-body">
                    <h5 class="card-title">SMS Delivery Status</h5>
                    <canvas id="smsPieChart" aria-label="SMS Delivery Status Chart"></canvas>
                    <div class="mt-3 small text-muted">
                        <span class="me-3"><span class="badge bg-success">&nbsp;</span> Sent</span>
                        <span class="me-3"><span class="badge bg-danger">&nbsp;</span> Failed</span>
                        <span class="me-3"><span class="badge bg-secondary">&nbsp;</span> Pending</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="card shadow-sm chart-card">
                <div class="card-body">
                    <h5 class="card-title">Patient Registration Trends</h5>
                    <canvas id="regLineChart" aria-label="Patient Registration Trends"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row - Quick Actions -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm card-quick-actions p-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div>
                        <h5 class="mb-1">Quick Actions</h5>
                        <div class="text-muted">Frequently used admin shortcuts</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="../admin/announcement_edit.php" class="btn btn-outline-success">Send Announcement</a>
                        <a href="../admin/patient_form.php" class="btn btn-success">Register Patient</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // PHP data to JS
    const smsData = {
        sent: <?php echo json_encode($sms_sent); ?>,
        failed: <?php echo json_encode($sms_failed); ?>,
        pending: <?php echo json_encode($sms_pending); ?>
    };

    const months = <?php echo json_encode($months); ?>;
    const registrations = <?php echo json_encode($registrations); ?>;

        // Create charts after layout settles to avoid incorrect initial sizing
        (function(){
            let smsChart = null;
            let regChart = null;

            function createCharts() {
                const pieEl = document.getElementById('smsPieChart');
                if (pieEl) {
                    try {
                        const pieCtx = pieEl.getContext('2d');
                        if (smsChart) smsChart.destroy();
                        smsChart = new Chart(pieCtx, {
                            type: 'pie',
                            data: {
                                labels: ['Sent','Failed','Pending'],
                                datasets: [{
                                    data: [smsData.sent, smsData.failed, smsData.pending],
                                    backgroundColor: ['#B2A08F','#dc3545','#6c757d'],
                                    hoverOffset: 6
                                }]
                            },
                            options: { plugins: { legend: { position: 'bottom' } }, maintainAspectRatio: false }
                        });
                    } catch (e) { console.error('Pie chart init error', e); }
                }

                const regEl = document.getElementById('regLineChart');
                if (regEl) {
                    try {
                        const regCtx = regEl.getContext('2d');
                        if (regChart) regChart.destroy();
                        regChart = new Chart(regCtx, {
                            type: 'line',
                            data: {
                                labels: months,
                                datasets: [{
                                    label: 'Registrations',
                                    data: registrations,
                                    borderColor: '#B2A08F',
                                    backgroundColor: 'rgba(178,160,143,0.12)',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 4,
                                    pointBackgroundColor: '#B2A08F'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    x: { grid: { display: false } },
                                    y: { beginAtZero: true, ticks: { precision:0 } }
                                },
                                plugins: { legend: { display: false } }
                            }
                        });
                    } catch (e) { console.error('Line chart init error', e); }
                }

                // trigger resize to let Chart.js compute correct sizes
                setTimeout(() => { try { if (smsChart) smsChart.resize(); if (regChart) regChart.resize(); } catch(e){} }, 60);
            }

            // Debounce helper
            function debounce(fn, wait){ let t; return function(){ clearTimeout(t); t = setTimeout(()=>fn.apply(this, arguments), wait); }; }

            // Create after a short delay to allow any CSS/layout/AOS animations finish
            setTimeout(createCharts, 80);

            // Recreate/rescale charts on window resize (debounced)
            window.addEventListener('resize', debounce(function(){ createCharts(); }, 200));
        })();
</script>

<?php include __DIR__ . '/../../includes/footer_admin.php'; ?>
