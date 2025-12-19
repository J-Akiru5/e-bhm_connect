<?php
// pages/portal/portal_dashboard.php
// Patient portal dashboard (modernized)
include_once __DIR__ . '/../../includes/header_portal.php';

// Get all data for the logged-in patient
$patient_id = isset($_SESSION['patient_id']) ? $_SESSION['patient_id'] : null;

// Fetch patient info and health records
if ($patient_id) {
    $stmt1 = $pdo->prepare("SELECT * FROM patients WHERE patient_id = ?");
    $stmt1->execute([$patient_id]);
    $patient = $stmt1->fetch(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare("SELECT * FROM patient_health_records WHERE patient_id = ?");
    $stmt2->execute([$patient_id]);
    $health_records = $stmt2->fetch(PDO::FETCH_ASSOC);
    
    // Query 3: Vitals History
    $stmt3 = $pdo->prepare("SELECT * FROM patient_vitals WHERE patient_id = ? ORDER BY recorded_at DESC LIMIT 10");
    $stmt3->execute([$patient_id]);
    $vitals_history = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    
    // Get latest vitals
    $latest_vitals = !empty($vitals_history) ? $vitals_history[0] : null;

    // Query 4: Visit History
    $stmt4 = $pdo->prepare("SELECT * FROM health_visits WHERE patient_id = ? ORDER BY visit_date DESC LIMIT 10");
    $stmt4->execute([$patient_id]);
    $visit_history = $stmt4->fetchAll(PDO::FETCH_ASSOC);
    
    // Get counts
    $stmt5 = $pdo->prepare("SELECT COUNT(*) FROM health_visits WHERE patient_id = ?");
    $stmt5->execute([$patient_id]);
    $total_visits = $stmt5->fetchColumn();
    
    $stmt6 = $pdo->prepare("SELECT COUNT(*) FROM patient_vitals WHERE patient_id = ?");
    $stmt6->execute([$patient_id]);
    $total_vitals = $stmt6->fetchColumn();
    
    // Get recent announcements
    $stmt7 = $pdo->prepare("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3");
    $stmt7->execute();
    $announcements = $stmt7->fetchAll(PDO::FETCH_ASSOC);
    
    // Get medicine dispensation history
    $stmtDispense = $pdo->prepare("
        SELECT mdl.*, mi.item_name as medicine_name
        FROM medicine_dispensing_log mdl
        LEFT JOIN medication_inventory mi ON mdl.item_id = mi.item_id
        WHERE mdl.resident_id = ?
        ORDER BY mdl.dispensed_at DESC
        LIMIT 10
    ");
    $stmtDispense->execute([$patient_id]);
    $dispensation_history = $stmtDispense->fetchAll(PDO::FETCH_ASSOC);
} else {
    $patient = null;
    $health_records = null;
    $vitals_history = [];
    $visit_history = [];
    $announcements = [];
    $dispensation_history = [];
    $total_visits = 0;
    $total_vitals = 0;
    $latest_vitals = null;
}

// Calculate age
$age = 'N/A';
if (!empty($patient['birthdate'])) {
    $birthdate = new DateTime($patient['birthdate']);
    $now = new DateTime();
    $age = $birthdate->diff($now)->y;
}

// Profile photo
$profilePhoto = $patient['profile_photo'] ?? null;
$photoPath = $profilePhoto ? BASE_URL . 'uploads/profiles/' . $profilePhoto : BASE_URL . 'assets/images/default-avatar.png';

// Last visit
$lastVisit = !empty($visit_history) ? date('M j, Y', strtotime($visit_history[0]['visit_date'])) : 'No visits yet';
?>

<style>
/* Modern Dashboard Styles */
.dashboard-hero {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: var(--radius-2xl);
    padding: var(--space-8);
    margin-bottom: var(--space-6);
    color: white;
    position: relative;
    overflow: hidden;
}

.dashboard-hero::before {
    content: '';
    position: absolute;
    top: -30%;
    right: -15%;
    width: 50%;
    height: 160%;
    background: rgba(255,255,255,0.08);
    transform: rotate(15deg);
    border-radius: 50%;
    pointer-events: none;
}

.hero-content {
    display: flex;
    align-items: center;
    gap: var(--space-6);
    flex-wrap: wrap;
}

.hero-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 4px solid rgba(255,255,255,0.3);
    object-fit: cover;
    background: rgba(255,255,255,0.1);
}

.hero-info h1 {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: var(--space-2);
}

.hero-meta {
    opacity: 0.9;
    font-size: var(--font-size-sm);
    display: flex;
    gap: var(--space-4);
    flex-wrap: wrap;
}

.hero-actions {
    margin-left: auto;
    display: flex;
    gap: var(--space-3);
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--space-4);
    margin-bottom: var(--space-6);
}

.stat-card {
    background: var(--white);
    border-radius: var(--radius-xl);
    padding: var(--space-5);
    border: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: var(--space-4);
    transition: all var(--transition-fast);
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-icon.primary { background: rgba(32, 201, 151, 0.15); color: var(--primary); }
.stat-icon.blue { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
.stat-icon.purple { background: rgba(139, 92, 246, 0.15); color: #8b5cf6; }
.stat-icon.amber { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }

.stat-content .stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
}

.stat-content .stat-label {
    font-size: var(--font-size-sm);
    color: var(--gray-500);
}

/* Modern Cards */
.dashboard-card {
    background: var(--white);
    border-radius: var(--radius-xl);
    border: 1px solid var(--gray-200);
    overflow: hidden;
    margin-bottom: var(--space-4);
}

.dashboard-card-header {
    padding: var(--space-4) var(--space-5);
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--space-3);
}

.dashboard-card-header h3 {
    font-size: var(--font-size-md);
    font-weight: 600;
    color: var(--gray-800);
    display: flex;
    align-items: center;
    gap: var(--space-2);
    margin: 0;
}

.dashboard-card-body {
    padding: var(--space-5);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-4);
}

.info-item .info-label {
    font-size: var(--font-size-xs);
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: var(--space-1);
}

.info-item .info-value {
    font-weight: 500;
    color: var(--gray-800);
}

/* Vital Badge */
.vital-badge {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    padding: var(--space-2) var(--space-3);
    background: rgba(32, 201, 151, 0.1);
    border-radius: var(--radius-lg);
    font-weight: 600;
    color: var(--primary-dark);
}

/* Announcement Card */
.announcement-item {
    padding: var(--space-4);
    border-left: 3px solid var(--primary);
    background: var(--gray-50);
    border-radius: 0 var(--radius-lg) var(--radius-lg) 0;
    margin-bottom: var(--space-3);
}

.announcement-item:last-child {
    margin-bottom: 0;
}

.announcement-title {
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: var(--space-1);
}

.announcement-date {
    font-size: var(--font-size-xs);
    color: var(--gray-500);
}

@media (max-width: 768px) {
    .hero-content {
        flex-direction: column;
        text-align: center;
    }
    
    .hero-actions {
        margin-left: 0;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Dashboard Hero -->
<div class="dashboard-hero">
    <div class="hero-content">
        <img src="<?php echo htmlspecialchars($photoPath); ?>" alt="Profile" class="hero-avatar">
        <div class="hero-info">
            <h1>Welcome, <?php echo htmlspecialchars($patient['full_name'] ?? 'Patient'); ?>!</h1>
            <div class="hero-meta">
                <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;vertical-align:middle;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg><?php echo $age; ?> years old</span>
                <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;vertical-align:middle;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg><?php echo htmlspecialchars($patient['address'] ?? 'N/A'); ?></span>
                <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;vertical-align:middle;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path></svg><?php echo htmlspecialchars($patient['contact'] ?? 'N/A'); ?></span>
            </div>
        </div>
        <div class="hero-actions">
            <a href="<?php echo BASE_URL; ?>portal-profile" class="btn btn-glass" style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.2);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                Edit Profile
            </a>
            <a href="<?php echo BASE_URL; ?>?action=report-my-record" class="btn btn-primary" target="_blank" style="background: white; color: var(--primary-dark);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                Download Records
            </a>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo $total_visits; ?></div>
            <div class="stat-label">Total Visits</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo $total_vitals; ?></div>
            <div class="stat-label">Vitals Recorded</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo $lastVisit; ?></div>
            <div class="stat-label">Last Visit</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo $latest_vitals ? htmlspecialchars($latest_vitals['blood_pressure']) : 'N/A'; ?></div>
            <div class="stat-label">Last BP Reading</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column -->
    <div class="col-lg-6">
        <!-- Personal Information -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Personal Information
                </h3>
                <a href="<?php echo BASE_URL; ?>portal-profile" class="btn btn-sm btn-glass">Edit</a>
            </div>
            <div class="dashboard-card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($patient['full_name'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Birthdate</div>
                        <div class="info-value"><?php echo $patient['birthdate'] ? date('M j, Y', strtotime($patient['birthdate'])) : 'N/A'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Sex</div>
                        <div class="info-value"><?php echo htmlspecialchars($patient['sex'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Contact</div>
                        <div class="info-value"><?php echo htmlspecialchars($patient['contact'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="info-item" style="grid-column: span 2;">
                        <div class="info-label">Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($patient['address'] ?? 'N/A'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Vitals -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                    </svg>
                    Latest Vitals
                </h3>
            </div>
            <div class="dashboard-card-body">
                <?php if ($latest_vitals): ?>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Blood Pressure</div>
                            <div class="vital-badge"><?php echo htmlspecialchars($latest_vitals['blood_pressure']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Heart Rate</div>
                            <div class="info-value"><?php echo htmlspecialchars($latest_vitals['heart_rate']); ?> bpm</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Temperature</div>
                            <div class="info-value"><?php echo htmlspecialchars($latest_vitals['temperature']); ?>°C</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Recorded</div>
                            <div class="info-value"><?php echo date('M j, Y g:i A', strtotime($latest_vitals['recorded_at'])); ?></div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--gray-400)" stroke-width="1.5" class="mb-2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                        <p class="mb-0">No vitals recorded yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Announcements -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    Recent Announcements
                </h3>
            </div>
            <div class="dashboard-card-body">
                <?php if (!empty($announcements)): ?>
                    <?php foreach ($announcements as $ann): ?>
                        <div class="announcement-item">
                            <div class="announcement-title"><?php echo htmlspecialchars($ann['title']); ?></div>
                            <div class="announcement-date"><?php echo date('M j, Y', strtotime($ann['created_at'])); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <p class="mb-0">No announcements</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-6">
        <!-- Health Records -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    My Health Records
                </h3>
            </div>
            <div class="dashboard-card-body">
                <div class="mb-4">
                    <div class="info-label">Medical History</div>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($health_records['medical_history'] ?? 'No medical history recorded.')); ?></p>
                </div>
                <div class="mb-4">
                    <div class="info-label">Immunization Records</div>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($health_records['immunization_records'] ?? 'No immunization records.')); ?></p>
                </div>
                <div class="mb-4">
                    <div class="info-label">Medication Records</div>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($health_records['medication_records'] ?? 'No medication records.')); ?></p>
                </div>
                <?php if (!empty($health_records['chronic_disease_mgmt'])): ?>
                <div>
                    <div class="info-label">Chronic Disease Management</div>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($health_records['chronic_disease_mgmt'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Visit History -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    Recent Visits
                </h3>
            </div>
            <div class="dashboard-card-body" style="padding: 0;">
                <?php if (!empty($visit_history)): ?>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th style="padding-left: var(--space-5);">Date</th>
                                    <th>Type</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($visit_history, 0, 5) as $visit): ?>
                                    <tr>
                                        <td style="padding-left: var(--space-5);"><?php echo date('M j, Y', strtotime($visit['visit_date'])); ?></td>
                                        <td><span class="badge badge-primary"><?php echo htmlspecialchars($visit['visit_type']); ?></span></td>
                                        <td><?php echo htmlspecialchars($visit['remarks'] ?: '-'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--gray-400)" stroke-width="1.5" class="mb-2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect></svg>
                        <p class="mb-0">No visits recorded yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Vitals History -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                    </svg>
                    Vitals History
                </h3>
            </div>
            <div class="dashboard-card-body" style="padding: 0;">
                <?php if (!empty($vitals_history)): ?>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th style="padding-left: var(--space-5);">Date</th>
                                    <th>BP</th>
                                    <th>HR</th>
                                    <th>Temp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($vitals_history, 0, 5) as $vital): ?>
                                    <tr>
                                        <td style="padding-left: var(--space-5);"><?php echo date('M j, Y', strtotime($vital['recorded_at'])); ?></td>
                                        <td><span class="vital-badge" style="font-size: 0.8rem; padding: 4px 8px;"><?php echo htmlspecialchars($vital['blood_pressure']); ?></span></td>
                                        <td><?php echo htmlspecialchars($vital['heart_rate']); ?> bpm</td>
                                        <td><?php echo htmlspecialchars($vital['temperature']); ?>°C</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--gray-400)" stroke-width="1.5" class="mb-2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                        <p class="mb-0">No vitals recorded yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Medicine Dispensation History -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="2">
                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
                        <path d="M12 5 9.04 7.96a2.17 2.17 0 0 0 0 3.08v0c.82.82 2.13.85 3 .07l2.07-1.9a2.82 2.82 0 0 1 3.79 0l2.96 2.66"></path>
                    </svg>
                    Medicine Dispensation History
                </h3>
            </div>
            <div class="dashboard-card-body" style="padding: 0;">
                <?php if (!empty($dispensation_history)): ?>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th style="padding-left: var(--space-5);">Date</th>
                                    <th>Medicine</th>
                                    <th>Qty</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($dispensation_history, 0, 5) as $dispense): ?>
                                    <tr>
                                        <td style="padding-left: var(--space-5);"><?php echo date('M j, Y', strtotime($dispense['dispensed_at'])); ?></td>
                                        <td><span class="badge" style="background: rgba(236, 72, 153, 0.15); color: #ec4899;"><?php echo htmlspecialchars($dispense['medicine_name'] ?? 'Unknown'); ?></span></td>
                                        <td><?php echo (int)$dispense['quantity']; ?></td>
                                        <td><?php echo htmlspecialchars($dispense['notes'] ?: '-'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--gray-400)" stroke-width="1.5" class="mb-2">
                            <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
                        </svg>
                        <p class="mb-0">No medicines dispensed yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer_portal.php'; ?>
