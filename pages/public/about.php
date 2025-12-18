<?php
// About E-BHM Connect - Modernized
include_once __DIR__ . '/../../includes/header_public.php';
?>

<?php
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
    error_log("Error fetching stats: " . $e->getMessage());
}
?>

<style>
/* Immersive Hero & Dark Theme Adaptation */
.about-hero {
    position: relative;
    padding: 140px 0 80px;
    background: linear-gradient(135deg, rgba(32, 201, 151, 0.95), rgba(13, 110, 81, 0.95)), 
                url('<?php echo BASE_URL; ?>assets/images/hero_bg.jpg');
    background-size: cover;
    background-position: center;
    overflow: hidden;
    margin-bottom: -40px;
    z-index: 1;
}

.about-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 50%;
    height: 200%;
    background: rgba(255,255,255,0.05);
    transform: rotate(15deg);
    pointer-events: none;
}

.about-content {
    background: var(--dark);
    position: relative;
    z-index: 2;
    padding-bottom: var(--space-20);
}

.about-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50px;
    color: white;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 1.5rem;
    backdrop-filter: blur(4px);
    border: none;
}

.about-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
    color: white;
    letter-spacing: -0.02em;
}

.about-subtitle {
    font-size: 1.25rem;
    color: rgba(255, 255, 255, 0.9);
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.6;
}

/* Glass Cards */
.glass-card {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: var(--radius-xl);
    padding: var(--space-8);
    height: 100%;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.glass-card:hover {
    background: rgba(255, 255, 255, 0.05);
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    border-color: rgba(32, 201, 151, 0.2);
}

.card-title {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 1.5rem;
    color: var(--primary);
    margin-bottom: 1.5rem;
}

.card-text {
    color: var(--gray-400);
    line-height: 1.7;
    margin-bottom: 1rem;
}

/* Feature Grid */
.feature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.feature-item {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: var(--radius-lg);
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
}

.feature-item:hover {
    background: rgba(32, 201, 151, 0.05);
    border-color: rgba(32, 201, 151, 0.2);
    transform: translateY(-5px);
}

.feature-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 1.5rem;
    background: rgba(255, 255, 255, 0.05);
    color: var(--primary);
}

.feature-item h4 {
    color: white;
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.feature-item p {
    color: var(--gray-500);
    font-size: 0.9rem;
    margin: 0;
}

/* Stats Strip */
.stats-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
    margin: 4rem 0;
    padding: 2rem;
    background: rgba(32, 201, 151, 0.05);
    border: 1px solid rgba(32, 201, 151, 0.1);
    border-radius: var(--radius-xl);
}

.stat-box {
    text-align: center;
}

.stat-num {
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, #fff 0%, #cbd5e1 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.stat-desc {
    color: var(--primary);
    font-size: 0.9rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

@media (max-width: 992px) {
    .stats-strip { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .about-title { font-size: 2.5rem; }
    .stats-strip { grid-template-columns: 1fr; gap: 1.5rem; }
}
</style>

<!-- Immersive Hero -->
<section class="about-hero">
    <!-- Animated Orbs -->
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>
    
    <div class="container position-relative z-2 text-center">
        <div class="about-badge" data-aos="fade-down">
            <i class="fas fa-info-circle"></i> About Us
        </div>
        <h1 class="about-title" data-aos="fade-up" data-aos-delay="100">
            E-BHM Connect
        </h1>
        <p class="about-subtitle" data-aos="fade-up" data-aos-delay="200">
            Revolutionizing community healthcare with a modern, secure, and accessible digital management system for Barangay Bacong.
        </p>
    </div>
</section>

<!-- Content Area -->
<section class="about-content">
    <div class="container">
        
        <!-- Stats Strip -->
        <div class="stats-strip" data-aos="fade-up">
            <div class="stat-box">
                <div class="stat-num"><?php echo number_format($stats['residents']); ?>+</div>
                <div class="stat-desc">Residents Served</div>
            </div>
            <div class="stat-box">
                <div class="stat-num"><?php echo number_format($stats['health_workers']); ?>+</div>
                <div class="stat-desc">Health Workers</div>
            </div>
            <div class="stat-box">
                <div class="stat-num">99%</div>
                <div class="stat-desc">System Uptime</div>
            </div>
            <div class="stat-box">
                <div class="stat-num"><?php echo number_format($stats['programs']); ?>+</div>
                <div class="stat-desc">Core Programs</div>
            </div>
        </div>

        <!-- Main Info Cards -->
        <div class="row g-4 mb-5">
            <div class="col-lg-12">
                <div class="glass-card" data-aos="fade-up">
                    <h2 class="card-title">
                        <i class="fas fa-laptop-medical"></i> What is E-BHM Connect?
                    </h2>
                    <p class="card-text">
                        E-BHM Connect is a comprehensive Electronic Barangay Health Management system designed to digitize and streamline 
                        health services in barangay communities. Our platform bridges the gap between Barangay Health Workers (BHWs), 
                        healthcare providers, and community members through an innovative web-based solution.
                    </p>
                    <p class="card-text mb-0">
                        Built with modern technology and following mobile-first design principles, E-BHM Connect ensures that healthcare 
                        management is accessible, efficient, and user-friendly for all stakeholders in the community health ecosystem.
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="glass-card" data-aos="fade-up" data-aos-delay="100">
                    <h2 class="card-title">
                        <i class="fas fa-bullseye"></i> Our Mission
                    </h2>
                    <p class="card-text">
                        To transform barangay health services by providing accessible, efficient, and comprehensive digital tools that empower 
                        Barangay Health Workers to deliver quality healthcare to their communities while ensuring patient data security and privacy.
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="glass-card" data-aos="fade-up" data-aos-delay="200">
                    <h2 class="card-title">
                        <i class="fas fa-eye"></i> Our Vision
                    </h2>
                    <p class="card-text">
                        A future where every barangay in the Philippines has access to modern digital health management systems, enabling 
                        data-driven decision making, improved patient outcomes, and seamless healthcare delivery at the grassroots level.
                    </p>
                </div>
            </div>
        </div>

        <!-- Key Features Grid -->
        <div class="glass-card" data-aos="fade-up">
            <div class="text-center mb-4">
                <h2 class="card-title justify-content-center">
                    <i class="fas fa-star"></i> Key Features
                </h2>
            </div>
            
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h4>Patient Management</h4>
                    <p>Secure records & visit tracking</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h4>Inventory System</h4>
                    <p>Real-time machine & supply tracking</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h4>Vital Monitoring</h4>
                    <p>Track health metrics over time</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h4>SMS Alerts</h4>
                    <p>Automated appointment reminders</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Analytics</h4>
                    <p>Data-driven health insights</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h4>AI Assistant</h4>
                    <p>24/7 support with Gabby</p>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="<?php echo BASE_URL; ?>" class="btn btn-outline-light rounded-pill px-4 py-2">
                <i class="fas fa-arrow-left me-2"></i> Back to Home
            </a>
        </div>

    </div>
</section>

<script>
if (typeof AOS !== 'undefined') {
    AOS.init({ duration: 800, once: true, offset: 50 });
}
</script>

<?php include_once __DIR__ . '/../../includes/footer_public.php'; ?>
