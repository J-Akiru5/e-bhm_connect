<?php
/**
 * Health Records Dashboard
 * E-BHM Connect - Glassmorphism Design
 * Central hub for all health record categories
 */
include __DIR__ . '/../../../includes/header_admin.php';

// Fetch counts for each record type
$counts = [
    'pregnancy' => 0,
    'child_care' => 0,
    'natality' => 0,
    'mortality' => 0,
    'chronic' => 0,
    'ntp' => 0,
    'wra' => 0
];

try {
    // Pregnancy tracking count
    $stmt = $pdo->query("SELECT COUNT(*) FROM pregnancy_tracking");
    $counts['pregnancy'] = (int)$stmt->fetchColumn();
    
    // Child care records count
    $stmt = $pdo->query("SELECT COUNT(*) FROM child_care_records");
    $counts['child_care'] = (int)$stmt->fetchColumn();
    
    // Natality records count
    $stmt = $pdo->query("SELECT COUNT(*) FROM natality_records");
    $counts['natality'] = (int)$stmt->fetchColumn();
    
    // Mortality records count
    $stmt = $pdo->query("SELECT COUNT(*) FROM mortality_records");
    $counts['mortality'] = (int)$stmt->fetchColumn();
    
    // Chronic disease masterlist count
    $stmt = $pdo->query("SELECT COUNT(*) FROM chronic_disease_masterlist");
    $counts['chronic'] = (int)$stmt->fetchColumn();
    
    // NTP client monitoring count
    $stmt = $pdo->query("SELECT COUNT(*) FROM ntp_client_monitoring");
    $counts['ntp'] = (int)$stmt->fetchColumn();
    
    // WRA tracking count
    $stmt = $pdo->query("SELECT COUNT(*) FROM wra_tracking");
    $counts['wra'] = (int)$stmt->fetchColumn();
} catch (PDOException $e) {
    // Tables may not exist yet, keep defaults
    error_log("Health records count error: " . $e->getMessage());
}

// Total records
$totalRecords = array_sum($counts);

// Record categories configuration
$categories = [
    [
        'id' => 'pregnancy',
        'title' => 'Pregnancy Tracking',
        'description' => 'Monitor pregnant women from identification to delivery outcome',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/><path d="M12 13v8"/></svg>',
        'color' => '#ec4899',
        'link' => BASE_URL . 'admin-health-records-pregnancy',
        'count' => $counts['pregnancy']
    ],
    [
        'id' => 'child_care',
        'title' => 'Child Care (12-59 Months)',
        'description' => 'Track child immunization and supplementation records',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12h.01"/><path d="M15 12h.01"/><path d="M10 16c.5.3 1.2.5 2 .5s1.5-.2 2-.5"/><path d="M19 6.3a9 9 0 0 1 1.8 3.9 2 2 0 0 1 0 3.6 9 9 0 0 1-17.6 0 2 2 0 0 1 0-3.6A9 9 0 0 1 12 3c2 0 3.5 1.1 3.5 2.5s-.9 2.5-2 2.5c-.8 0-1.5-.4-1.5-1"/></svg>',
        'color' => '#f59e0b',
        'link' => BASE_URL . 'admin-health-records-childcare',
        'count' => $counts['child_care']
    ],
    [
        'id' => 'natality',
        'title' => 'Natality Records',
        'description' => 'Register and track all birth records in the barangay',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11a9 9 0 0 1 9 9"/><path d="M4 4a16 16 0 0 1 16 16"/><circle cx="5" cy="19" r="1"/></svg>',
        'color' => '#10b981',
        'link' => BASE_URL . 'admin-health-records-natality',
        'count' => $counts['natality']
    ],
    [
        'id' => 'mortality',
        'title' => 'Mortality Records',
        'description' => 'Document and track death records with cause analysis',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 14V2"/><path d="M9 18.12 10 14H4.17a2 2 0 0 1-1.92-2.56l2.33-8A2 2 0 0 1 6.5 2H20a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-2.76a2 2 0 0 0-1.79 1.11L12 22h0a3.13 3.13 0 0 1-3-3.88Z"/></svg>',
        'color' => '#6b7280',
        'link' => BASE_URL . 'admin-health-records-mortality',
        'count' => $counts['mortality']
    ],
    [
        'id' => 'chronic',
        'title' => 'Hypertensive & Diabetic',
        'description' => 'Masterlist of patients with chronic conditions and medications',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>',
        'color' => '#ef4444',
        'link' => BASE_URL . 'admin-health-records-chronic',
        'count' => $counts['chronic']
    ],
    [
        'id' => 'ntp',
        'title' => 'NTP Client Monitoring',
        'description' => 'Tuberculosis program tracking with treatment progress',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>',
        'color' => '#8b5cf6',
        'link' => BASE_URL . 'admin-health-records-ntp',
        'count' => $counts['ntp']
    ],
    [
        'id' => 'wra',
        'title' => 'WRA Tracking',
        'description' => 'Women of Reproductive Age tracking with family planning',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>',
        'color' => '#06b6d4',
        'link' => BASE_URL . 'admin-health-records-wra',
        'count' => $counts['wra']
    ]
];
?>

<div class="container-fluid py-4 fade-in">
    <!-- Page Header -->
    <div class="glass-card mb-4" style="background: linear-gradient(135deg, rgba(236, 72, 153, 0.1), rgba(99, 102, 241, 0.1));">
        <div class="glass-card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2" style="vertical-align: -6px;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                    Health Records Dashboard
                </h1>
                <p class="text-secondary mb-0">Comprehensive health tracking for barangay residents</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    <div class="h4 mb-0 fw-bold" style="color: var(--primary);"><?php echo number_format($totalRecords); ?></div>
                    <small class="text-secondary">Total Records</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card h-100">
                <div class="stat-card-content text-center">
                    <div class="stat-card-value" style="color: #ec4899;"><?php echo number_format($counts['pregnancy']); ?></div>
                    <div class="stat-card-label small">Pregnancies</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card h-100">
                <div class="stat-card-content text-center">
                    <div class="stat-card-value" style="color: #10b981;"><?php echo number_format($counts['natality']); ?></div>
                    <div class="stat-card-label small">Births</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card h-100">
                <div class="stat-card-content text-center">
                    <div class="stat-card-value" style="color: #6b7280;"><?php echo number_format($counts['mortality']); ?></div>
                    <div class="stat-card-label small">Deaths</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card h-100">
                <div class="stat-card-content text-center">
                    <div class="stat-card-value" style="color: #ef4444;"><?php echo number_format($counts['chronic']); ?></div>
                    <div class="stat-card-label small">Chronic</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card h-100">
                <div class="stat-card-content text-center">
                    <div class="stat-card-value" style="color: #8b5cf6;"><?php echo number_format($counts['ntp']); ?></div>
                    <div class="stat-card-label small">TB Cases</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card h-100">
                <div class="stat-card-content text-center">
                    <div class="stat-card-value" style="color: #06b6d4;"><?php echo number_format($counts['wra']); ?></div>
                    <div class="stat-card-label small">WRA</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Record Categories Grid -->
    <div class="row g-4">
        <?php foreach ($categories as $category): ?>
        <div class="col-12 col-md-6 col-xl-4">
            <a href="<?php echo htmlspecialchars($category['link']); ?>" class="text-decoration-none">
                <div class="glass-card h-100 health-record-card" style="--card-accent: <?php echo $category['color']; ?>;">
                    <div class="glass-card-body">
                        <div class="d-flex align-items-start gap-3">
                            <div class="health-record-icon" style="background: <?php echo $category['color']; ?>15; color: <?php echo $category['color']; ?>;">
                                <?php echo $category['icon']; ?>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1" style="color: var(--text-primary);"><?php echo htmlspecialchars($category['title']); ?></h5>
                                <p class="text-secondary small mb-3"><?php echo htmlspecialchars($category['description']); ?></p>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge" style="background: <?php echo $category['color']; ?>20; color: <?php echo $category['color']; ?>; font-weight: 600;">
                                        <?php echo number_format($category['count']); ?> Records
                                    </span>
                                    <span class="health-record-arrow" style="color: <?php echo $category['color']; ?>;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Recent Activity Section -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="glass-card">
                <div class="glass-card-header d-flex justify-content-between align-items-center">
                    <h5 class="glass-card-title mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2" style="vertical-align: -4px;">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        Quick Actions
                    </h5>
                </div>
                <div class="glass-card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?php echo BASE_URL; ?>admin-health-records-pregnancy?action=add" class="btn btn-glass">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            New Pregnancy Record
                        </a>
                        <a href="<?php echo BASE_URL; ?>admin-health-records-natality?action=add" class="btn btn-glass">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            New Birth Record
                        </a>
                        <a href="<?php echo BASE_URL; ?>admin-health-records-chronic?action=add" class="btn btn-glass">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            New Chronic Disease Entry
                        </a>
                        <a href="<?php echo BASE_URL; ?>admin-health-records-ntp?action=add" class="btn btn-glass">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            New TB Client
                        </a>
                        <a href="<?php echo BASE_URL; ?>admin-health-records-wra?action=add" class="btn btn-glass">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            New WRA Entry
                        </a>
                        <a href="<?php echo BASE_URL; ?>admin-reports" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
                            </svg>
                            Generate Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Health Record Card Styles */
.health-record-card {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.health-record-card:hover {
    transform: translateY(-4px);
    border-color: var(--card-accent);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.health-record-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: transform 0.3s ease;
}

.health-record-card:hover .health-record-icon {
    transform: scale(1.1);
}

.health-record-arrow {
    opacity: 0;
    transform: translateX(-8px);
    transition: all 0.3s ease;
}

.health-record-card:hover .health-record-arrow {
    opacity: 1;
    transform: translateX(0);
}

/* Stat cards uniform height */
.stat-card {
    min-height: 100px;
}

/* Mobile optimizations */
@media (max-width: 767.98px) {
    .health-record-icon {
        width: 48px;
        height: 48px;
    }
    
    .health-record-icon svg {
        width: 24px;
        height: 24px;
    }
}
</style>

<?php include __DIR__ . '/../../../includes/footer_admin.php'; ?>
