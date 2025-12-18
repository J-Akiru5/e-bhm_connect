<!DOCTYPE html>
<?php
// Immersive Landing Page - E-BHM Connect
$announcementsStmt = $pdo->query("SELECT a.*, b.full_name 
                    FROM announcements a 
                    LEFT JOIN bhw_users b ON a.bhw_id = b.bhw_id 
                    ORDER BY a.created_at DESC LIMIT 5");
$recentAnnouncements = $announcementsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Dynamic Stats
$stats = [
    'residents' => 0,
    'health_workers' => 0,
    'programs' => 0
];

try {
    // Count Residents (Patients)
    $stmt = $pdo->query("SELECT COUNT(*) FROM patients");
    $stats['residents'] = $stmt->fetchColumn();

    // Count Health Workers (BHWs)
    $stmt = $pdo->query("SELECT COUNT(*) FROM bhw_users WHERE role IN ('admin', 'bhw', 'superadmin')");
    $stats['health_workers'] = $stmt->fetchColumn();

    // Count Active Programs
    $stmt = $pdo->query("SELECT COUNT(*) FROM health_programs WHERE status = 'Active'");
    $stats['programs'] = $stmt->fetchColumn();

} catch (Exception $e) {
    error_log("Stats fetch error: " . $e->getMessage());
    // Fallback values are 0
}

include_once __DIR__ . '/../../includes/header_public.php';
?>

<style>
/* ================================================================
   IMMERSIVE LANDING PAGE STYLES
   Dark, Cinematic, Premium Design
   ================================================================ */

/* Hero Section - Full Screen Immersive */
.immersive-hero {
    min-height: 100vh;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.85) 0%, rgba(15, 23, 42, 0.95) 100%),
                url('<?php echo BASE_URL; ?>assets/images/hero_bg.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
    padding: 120px 0 80px;
}

/* Animated Gradient Orbs */
.hero-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    animation: orbFloat 20s ease-in-out infinite;
    pointer-events: none;
    z-index: 1;
}

.hero-orb-1 {
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(32, 201, 151, 0.25) 0%, transparent 70%);
    top: -200px;
    left: -200px;
    animation-delay: 0s;
}

.hero-orb-2 {
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(32, 201, 151, 0.2) 0%, transparent 70%);
    bottom: -150px;
    right: -150px;
    animation-delay: -7s;
}

.hero-orb-3 {
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
    top: 30%;
    right: 10%;
    animation-delay: -14s;
}

.hero-orb-4 {
    width: 350px;
    height: 350px;
    background: radial-gradient(circle, rgba(32, 201, 151, 0.12) 0%, transparent 70%);
    bottom: 20%;
    left: 20%;
    animation-delay: -10s;
}

@keyframes orbFloat {
    0%, 100% { 
        transform: translate(0, 0) scale(1);
        opacity: 0.6;
    }
    25% { 
        transform: translate(40px, -40px) scale(1.05);
        opacity: 0.8;
    }
    50% { 
        transform: translate(-30px, 30px) scale(0.95);
        opacity: 0.5;
    }
    75% { 
        transform: translate(25px, 40px) scale(1.02);
        opacity: 0.7;
    }
}

/* Hero Content Grid */
.hero-grid {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 var(--space-6);
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: var(--space-8);
    align-items: center;
    position: relative;
    z-index: 10;
    align-items: start;
    padding-top: var(--space-10);
}

/* Text Scrim - Better Contrast */
.text-scrim {
    position: relative;
    padding: var(--space-8);
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.7) 0%, rgba(15, 23, 42, 0.4) 100%);
    border-radius: var(--radius-2xl);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.05);
}

/* Hero Badge */
.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    background: rgba(32, 201, 151, 0.15);
    padding: var(--space-2) var(--space-4);
    border-radius: var(--radius-full);
    font-size: var(--font-size-sm);
    color: var(--primary);
    margin-bottom: var(--space-4);
    border: 1px solid rgba(32, 201, 151, 0.3);
}

.hero-badge .pulse {
    width: 8px;
    height: 8px;
    background: var(--primary);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(32, 201, 151, 0.7); }
    50% { box-shadow: 0 0 0 12px rgba(32, 201, 151, 0); }
}

/* Hero Title */
.hero-title {
    font-size: 4rem;
    font-weight: 800;
    color: white;
    line-height: 1.05;
    margin-bottom: var(--space-4);
    letter-spacing: -0.02em;
}

.hero-title .highlight {
    background: linear-gradient(135deg, var(--primary) 0%, #6ee7b7 50%, var(--primary) 100%);
    background-size: 200% auto;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
    0%, 100% { background-position: 0% center; }
    50% { background-position: 100% center; }
}

.hero-tagline {
    font-size: var(--font-size-sm);
    color: rgba(255, 255, 255, 0.5);
    text-transform: uppercase;
    letter-spacing: 0.2em;
    margin-bottom: var(--space-2);
}

.hero-subtitle {
    font-size: var(--font-size-lg);
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: var(--space-6);
    line-height: 1.6;
}

/* CTA Buttons */
.hero-actions {
    display: flex;
    gap: var(--space-4);
    flex-wrap: wrap;
}

.btn-glow {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    padding: var(--space-4) var(--space-8);
    border-radius: var(--radius-lg);
    font-weight: 600;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    text-decoration: none;
    transition: all var(--transition-base);
    box-shadow: 0 0 30px rgba(32, 201, 151, 0.4);
}

.btn-glow:hover {
    transform: translateY(-3px);
    box-shadow: 0 0 50px rgba(32, 201, 151, 0.6);
    color: white;
}

.btn-outline-light {
    background: transparent;
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: var(--space-4) var(--space-8);
    border-radius: var(--radius-lg);
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    text-decoration: none;
    transition: all var(--transition-base);
}

.btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: white;
    color: white;
}

/* Gabby Section */
.gabby-section {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.gabby-container {
    position: relative;
    width: 420px;
    height: 420px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.gabby-glow {
    position: absolute;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(32, 201, 151, 0.3) 0%, transparent 70%);
    border-radius: 50%;
    animation: gabbyGlow 3s ease-in-out infinite;
}

@keyframes gabbyGlow {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

.gabby-animation {
    width: 450px;
    height: 450px;
    object-fit: contain;
    position: relative;
    z-index: 2;
    filter: drop-shadow(0 0 40px rgba(32, 201, 151, 0.5));
}

.gabby-position-container {
    position: absolute;
    bottom: -90px;
    right: 1%;
    z-index: 20;
    pointer-events: none;
}

@media (max-width: 1200px) {
    .gabby-position-container {
        position: relative;
        bottom: auto;
        right: auto;
        margin-top: var(--space-8);
        text-align: center;
        width: 100%;
        display: flex;
        justify-content: center;
    }
}

/* Live Activity Widget */
.activity-widget {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--radius-xl);
    padding: var(--space-5);
    margin-top: var(--space-6);
    width: 100%;
    max-width: 100%;
}

.activity-header {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    margin-bottom: var(--space-4);
    padding-bottom: var(--space-3);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.activity-header svg {
    color: var(--primary);
}

.activity-header span {
    color: white;
    font-weight: 600;
    font-size: var(--font-size-sm);
}

.activity-dot {
    width: 8px;
    height: 8px;
    background: var(--primary);
    border-radius: 50%;
    margin-left: auto;
    animation: pulse 2s infinite;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    padding: var(--space-3);
    border-radius: var(--radius-lg);
    transition: all var(--transition-fast);
    text-decoration: none;
    color: inherit;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-item:hover {
    background: rgba(255, 255, 255, 0.05);
}

.activity-icon {
    width: 36px;
    height: 36px;
    background: rgba(32, 201, 151, 0.15);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.activity-icon svg {
    width: 16px;
    height: 16px;
    color: var(--primary);
}

.activity-text {
    flex: 1;
}

.activity-title {
    color: white;
    font-size: var(--font-size-sm);
    font-weight: 500;
}

.activity-time {
    color: rgba(255, 255, 255, 0.5);
    font-size: var(--font-size-xs);
}

/* Stats Section */
.stats-section {
    background: linear-gradient(180deg, rgba(15, 23, 42, 1) 0%, rgba(15, 23, 42, 0.95) 100%);
    padding: var(--space-16) var(--space-6);
    position: relative;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--space-6);
    max-width: 1000px;
    margin: 0 auto;
}

.stat-card {
    text-align: center;
    padding: var(--space-6);
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: var(--radius-xl);
    transition: all var(--transition-fast);
}

.stat-card:hover {
    background: rgba(32, 201, 151, 0.05);
    border-color: rgba(32, 201, 151, 0.2);
    transform: translateY(-5px);
}

.stat-value {
    font-size: 3rem;
    font-weight: 800;
    color: var(--primary);
}

.stat-label {
    color: rgba(255, 255, 255, 0.6);
    font-size: var(--font-size-sm);
    margin-top: var(--space-2);
}

/* Services Section - Dark Theme */
.services-section {
    background: var(--dark);
    padding: var(--space-20) var(--space-6);
    position: relative;
}

.section-header {
    text-align: center;
    margin-bottom: var(--space-12);
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: var(--space-3);
}

.section-subtitle {
    color: rgba(255, 255, 255, 0.5);
    font-size: var(--font-size-lg);
    max-width: 600px;
    margin: 0 auto;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--space-6);
    max-width: 1200px;
    margin: 0 auto;
}

.service-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: var(--radius-2xl);
    overflow: hidden;
    transition: all var(--transition-base);
}

.service-card:hover {
    transform: translateY(-8px);
    border-color: rgba(32, 201, 151, 0.3);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
}

.service-card-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
    filter: brightness(0.8);
    transition: all var(--transition-base);
}

.service-card:hover .service-card-image {
    filter: brightness(1);
}

.service-card-content {
    padding: var(--space-5);
}

.service-card h5 {
    color: white;
    font-weight: 600;
    margin-bottom: var(--space-2);
}

.service-card p {
    color: rgba(255, 255, 255, 0.5);
    font-size: var(--font-size-sm);
    line-height: 1.5;
    margin: 0;
}

/* CTA Section */
.cta-section {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    padding: var(--space-16) var(--space-6);
    text-align: center;
    position: relative;
    overflow: hidden;
}

.cta-section::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 60%;
    height: 200%;
    background: rgba(255, 255, 255, 0.05);
    transform: rotate(15deg);
    pointer-events: none;
}

.cta-content {
    position: relative;
    z-index: 10;
    max-width: 700px;
    margin: 0 auto;
}

.cta-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: var(--space-4);
}

.cta-text {
    color: rgba(255, 255, 255, 0.9);
    font-size: var(--font-size-lg);
    margin-bottom: var(--space-6);
}

.btn-white {
    background: white;
    color: var(--primary-dark);
    padding: var(--space-4) var(--space-8);
    border-radius: var(--radius-lg);
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    text-decoration: none;
    transition: all var(--transition-base);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.btn-white:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
    color: var(--primary-dark);
}

/* About Section */
.about-section {
    background: var(--dark);
    padding: var(--space-20) var(--space-6);
}

.about-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-6);
    max-width: 1000px;
    margin: 0 auto var(--space-10);
}

.about-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: var(--radius-2xl);
    padding: var(--space-8);
    transition: all var(--transition-fast);
}

.about-card:hover {
    border-color: rgba(32, 201, 151, 0.3);
    background: rgba(255, 255, 255, 0.05);
}

.about-icon {
    width: 60px;
    height: 60px;
    background: rgba(32, 201, 151, 0.15);
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: var(--space-4);
    color: var(--primary);
}

.about-card h3 {
    color: white;
    font-size: 1.5rem;
    margin-bottom: var(--space-3);
}

.about-card p {
    color: rgba(255, 255, 255, 0.6);
    line-height: 1.7;
}

.barangay-info {
    display: flex;
    justify-content: center;
    gap: var(--space-8);
    flex-wrap: wrap;
    padding: var(--space-6);
    background: rgba(32, 201, 151, 0.05);
    border: 1px solid rgba(32, 201, 151, 0.15);
    border-radius: var(--radius-xl);
    max-width: 900px;
    margin: 0 auto;
}

.info-item {
    color: rgba(255, 255, 255, 0.8);
    font-size: var(--font-size-sm);
}

.info-item strong {
    color: var(--primary);
}

/* Contact Section */
.contact-section {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    padding: var(--space-20) var(--space-6);
    color: white;
}

.contact-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-6);
    max-width: 900px;
    margin: 0 auto;
}

.contact-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: var(--radius-2xl);
    padding: var(--space-6);
    transition: all var(--transition-fast);
}

.contact-card:hover {
    border-color: rgba(255, 255, 255, 0.3);
}

.contact-icon {
    width: 56px;
    height: 56px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: var(--space-4);
    color: white;
}

.contact-card h4 {
    color: white;
    font-size: 1.25rem;
    margin-bottom: var(--space-4);
}

.contact-card p {
    color: rgba(255, 255, 255, 0.6);
    margin-bottom: var(--space-2);
    font-size: var(--font-size-sm);
}

.contact-card p strong {
    color: rgba(255, 255, 255, 0.8);
}

.contact-buttons {
    display: flex;
    gap: var(--space-3);
    margin-top: var(--space-4);
}

.btn-contact {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: var(--space-2) var(--space-4);
    border-radius: var(--radius-lg);
    font-size: var(--font-size-sm);
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    text-decoration: none;
    transition: all var(--transition-fast);
    border: 1px solid transparent;
}

.btn-contact:hover {
    background: white;
    color: var(--primary-dark);
}

.btn-contact-outline {
    background: transparent;
    color: rgba(255, 255, 255, 0.7);
    padding: var(--space-2) var(--space-4);
    border-radius: var(--radius-lg);
    font-size: var(--font-size-sm);
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    text-decoration: none;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all var(--transition-fast);
}

.btn-contact-outline:hover {
    border-color: white;
    color: white;
}

/* Responsive */
@media (max-width: 1200px) {
    .hero-grid { grid-template-columns: 1fr; text-align: center; }
    .gabby-section { margin-top: var(--space-10); }
    .text-scrim { padding: var(--space-6); }
    .hero-actions { justify-content: center; }
    .services-grid { grid-template-columns: repeat(2, 1fr); }
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .hero-title { font-size: 2.5rem; }
    .services-grid { grid-template-columns: 1fr; }
    .gabby-container { width: 220px; height: 220px; }
    .gabby-animation { width: 200px; height: 200px; }
}
</style>

<!-- Immersive Hero Section -->
<section class="immersive-hero">
    <!-- Animated Orbs -->
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>
    <div class="hero-orb hero-orb-3"></div>
    <div class="hero-orb hero-orb-4"></div>
    
    <div class="hero-grid">
        <!-- Left: Text Content with Scrim -->
        <div class="text-scrim" data-aos="fade-right">
            <div class="hero-badge">
                <span class="pulse"></span>
                Barangay Health Services
            </div>
            
            <p class="hero-tagline">Digitizing Healthcare for Communities</p>
            
            <h1 class="hero-title">
                Welcome to<br>
                <span class="highlight">E-BHM Connect</span>
            </h1>
            
            <p class="hero-subtitle">
                The official digital health management platform for Barangay Bacong. Access your health records, connect with health workers, and stay informed about community health programs.
            </p>
            
            <div class="hero-actions">
                <a href="<?php echo BASE_URL; ?>login-patient" class="btn-glow">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Resident Portal
                </a>
                <a href="<?php echo BASE_URL; ?>login-bhw" class="btn-outline-light">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    BHW Login
                </a>
            </div>
        </div>
        
        <!-- Right: Gabby Animation + Activity Widget -->
        <div class="gabby-section" data-aos="fade-left">
            <!-- Gabby animation removed from here - moved to absolute position -->
            
    <!-- Right Column: Recent Activity Widget -->
    <div class="hero-sidebar" data-aos="fade-left" data-aos-delay="200">
        <div class="activity-widget">
            <div class="activity-header">
                <span class="pulse"></span>
                <span>Recent community updates</span>
                <div class="activity-dot"></div>
            </div>
            
            <div class="activity-list">
                <?php if (empty($recentAnnouncements)): ?>
                    <div class="text-center p-3 text-white-50">No recent updates</div>
                <?php else: ?>
                    <?php foreach ($recentAnnouncements as $announcement): ?>
                    <a href="<?php echo BASE_URL; ?>announcements#announcement-<?php echo $announcement['announcement_id']; ?>" class="activity-item">
                        <div class="activity-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                        </div>
                        <div class="activity-text">
                            <div class="activity-title"><?php echo htmlspecialchars($announcement['title'] ?? ''); ?></div>
                            <div class="activity-time"><?php echo date('M j, Y', strtotime($announcement['created_at'])); ?></div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Gabby Animation - Fixed Bottom Right -->
<div class="gabby-position-container">
    <div class="gabby-container">
        <div class="gabby-glow"></div>
        <img src="<?php echo BASE_URL; ?>assets/images/gabby_welcome.gif" alt="Gabby AI" class="gabby-animation">
    </div>
</div></section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="stats-grid" data-aos="fade-up">
        <div class="stat-card">
            <div class="stat-value"><?php echo number_format($stats['residents']); ?>+</div>
            <div class="stat-label">Residents Served</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo number_format($stats['health_workers']); ?>+</div>
            <div class="stat-label">Health Workers</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">24/7</div>
            <div class="stat-label">Digital Access</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo number_format($stats['programs']); ?>+</div>
            <div class="stat-label">Health Programs</div>
        </div>

    </div>
</section>

<!-- Services Section -->
<section class="services-section" id="services">
    <div class="section-header" data-aos="fade-up">
        <h2 class="section-title">Our Services</h2>
        <p class="section-subtitle">Accessible, community-centered healthcare services for every resident.</p>
    </div>

    <div class="services-grid">
        <div class="service-card" data-aos="zoom-in" data-aos-delay="100">
            <img src="<?php echo BASE_URL; ?>assets/images/service_vaccination.jpg" alt="Vaccination" class="service-card-image">
            <div class="service-card-content">
                <h5>Vaccination Programs</h5>
                <p>Comprehensive immunization schedules and records for infants, children, and adults.</p>
            </div>
        </div>
        
        <div class="service-card" data-aos="zoom-in" data-aos-delay="200">
            <img src="<?php echo BASE_URL; ?>assets/images/service_checkup.jpg" alt="Health Checkups" class="service-card-image">
            <div class="service-card-content">
                <h5>Health Checkups</h5>
                <p>Routine health monitoring, blood pressure checks, and general consultations.</p>
            </div>
        </div>
        
        <div class="service-card" data-aos="zoom-in" data-aos-delay="300">
            <img src="<?php echo BASE_URL; ?>assets/images/service_maternity.jpg" alt="Maternity Care" class="service-card-image">
            <div class="service-card-content">
                <h5>Maternity Care</h5>
                <p>Maternal health services including prenatal and postnatal care support.</p>
            </div>
        </div>
        
        <div class="service-card" data-aos="zoom-in" data-aos-delay="400">
            <img src="<?php echo BASE_URL; ?>assets/images/service_chronic.jpg" alt="Chronic Disease" class="service-card-image">
            <div class="service-card-content">
                <h5>Chronic Disease Management</h5>
                <p>Support programs for diabetes, hypertension, and other conditions.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="cta-content" data-aos="zoom-in">
        <h2 class="cta-title">Are you a resident of Brgy. Bacong?</h2>
        <p class="cta-text">
            Join our digital health management system to access your health records, receive important updates, and stay connected with Barangay Bacong Health Center.
        </p>
        <a href="<?php echo BASE_URL; ?>register-patient" class="btn-white">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <line x1="20" y1="8" x2="20" y2="14"></line>
                <line x1="23" y1="11" x2="17" y2="11"></line>
            </svg>
            Register Now!
        </a>
    </div>
</section>

<!-- About Section -->
<section class="about-section" id="about">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">Our Mission & Vision</h2>
            <p class="section-subtitle">Committed to delivering accessible and quality healthcare services to every resident of Barangay Bacong.</p>
        </div>
        
        <div class="about-grid">
            <div class="about-card" data-aos="fade-right">
                <div class="about-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                </div>
                <h3>Our Mission</h3>
                <p>To digitize and securely manage patient health records, streamline healthcare services, and empower Barangay Health Workers with digital tools for better community health management.</p>
            </div>
            <div class="about-card" data-aos="fade-left">
                <div class="about-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </div>
                <h3>Our Vision</h3>
                <p>To create a healthier community in Bacong, Dumangas by leveraging technology for efficient, reliable, and accessible health services for all residents.</p>
            </div>
        </div>
        
        <div class="barangay-info" data-aos="fade-up">
            <div class="info-item">
                <strong>Barangay:</strong> Bacong, Dumangas, Iloilo
            </div>
            <div class="info-item">
                <strong>Population:</strong> 1,385+ residents
            </div>
            <div class="info-item">
                <strong>Health Center:</strong> Bacong Barangay Health Center
            </div>
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="<?php echo BASE_URL; ?>?page=about" class="btn btn-primary btn-lg rounded-pill px-5">
                Learn More 
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section" id="contact">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">Contact Us</h2>
            <p class="section-subtitle">For inquiries or assistance, reach out to us through any of these channels.</p>
        </div>
        
        <div class="contact-grid">
            <div class="contact-card" data-aos="fade-right">
                <div class="contact-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                    </svg>
                </div>
                <h4>Bacong Health Center</h4>
                <p><strong>Address:</strong> Bacong, Dumangas, Iloilo</p>
                <p><strong>Contact:</strong> (033) 123-4567</p>
                <p><strong>Email:</strong> healthcenter@bacong.gov</p>
                <p><strong>Hours:</strong> Mon - Fri, 8AM - 5PM</p>
            </div>
            
            <div class="contact-card" data-aos="fade-left">
                <div class="contact-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </div>
                <h4>Barangay Hall</h4>
                <p><strong>Contact:</strong> (033) 987-6543</p>
                <p><strong>Email:</strong> barangaybacong@gmail.com</p>
                <div class="contact-buttons">
                    <a href="https://www.facebook.com/barangay.bacong.2025" target="_blank" class="btn-contact">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                        Facebook
                    </a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Barangay+Bacong+Dumangas+Iloilo" target="_blank" class="btn-contact-outline">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        Maps
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../../includes/footer_public.php'; ?>

<script>
// Initialize AOS with smooth settings
if (typeof AOS !== 'undefined') {
    AOS.init({ 
        duration: 800, 
        once: true, 
        offset: 100,
        easing: 'ease-out-cubic'
    });
}

// Smooth scroll for anchors
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const hash = this.getAttribute('href').substring(1);
        const target = document.getElementById(hash);
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>