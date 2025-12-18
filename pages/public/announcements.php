<?php
// Modern Announcements Page - Dark Immersive Theme

// Ensure database connection is available (fallback if accessed directly)
if (!isset($pdo)) {
    require_once __DIR__ . '/../../config/config.php';
    require_once __DIR__ . '/../../config/database.php';
}

// Fetch announcements BEFORE including header
$announcements = [];
$announcements_error = null;
try {
    $stmt = $pdo->query("SELECT a.*, b.full_name 
                    FROM announcements a 
                    LEFT JOIN bhw_users b ON a.bhw_id = b.bhw_id 
                    ORDER BY a.created_at DESC");
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Public announcements fetch error: ' . $e->getMessage());
    $announcements = [];
    $announcements_error = $e->getMessage();
}

// Now include header
include_once __DIR__ . '/../../includes/header_public.php';
?>

<style>
/* CSS Variable Fallbacks */
:root {
    --primary: #20c997;
    --primary-dark: #0f5132;
    --dark: #0f172a;
}

/* Announcements Page - Dark Immersive Theme */
.announcements-hero {
    min-height: 50vh;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.95) 100%),
                url('<?php echo BASE_URL; ?>assets/images/announcements_bg.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    padding: 140px 24px 80px;
}

/* Animated Orbs */
.hero-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    animation: orbFloat 20s ease-in-out infinite;
    pointer-events: none;
}

.hero-orb-1 {
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(32, 201, 151, 0.2) 0%, transparent 70%);
    top: -150px;
    left: -150px;
}

.hero-orb-2 {
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.12) 0%, transparent 70%);
    bottom: -100px;
    right: -100px;
    animation-delay: -10s;
}

@keyframes orbFloat {
    0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.6; }
    50% { transform: translate(-30px, 30px) scale(0.95); opacity: 0.5; }
}

.hero-content {
    position: relative;
    z-index: 10;
    text-align: center;
    max-width: 800px;
}

/* Hero Grid Layout */
.hero-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 40px;
    align-items: center;
    max-width: 1100px;
    width: 100%;
    position: relative;
    z-index: 10;
}

.hero-gabby {
    display: flex;
    justify-content: center;
    align-items: center;
}

.hero-gabby img {
    width: 450px;
    height: 450px;
    object-fit: contain;
    filter: drop-shadow(0 0 40px rgba(32, 201, 151, 0.5));
}

@media (max-width: 900px) {
    .hero-grid {
        grid-template-columns: 1fr;
        text-align: center;
    }
    .hero-gabby {
        order: -1;
    }
    .hero-gabby img {
        width: 200px;
        height: 200px;
    }
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(32, 201, 151, 0.15);
    padding: 8px 20px;
    border-radius: 100px;
    font-size: 0.875rem;
    color: var(--primary);
    margin-bottom: 20px;
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
    50% { box-shadow: 0 0 0 10px rgba(32, 201, 151, 0); }
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    color: white;
    margin-bottom: 16px;
    letter-spacing: -0.02em;
}

.hero-title .highlight {
    background: linear-gradient(135deg, var(--primary) 0%, #6ee7b7 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: 1.125rem;
    color: rgba(255, 255, 255, 0.7);
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

/* Announcements Section */
.announcements-section {
    background: var(--dark);
    padding: 80px 24px;
    min-height: 50vh;
}

.announcements-container {
    max-width: 900px;
    margin: 0 auto;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 24px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 24px;
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    background: rgba(32, 201, 151, 0.1);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    color: var(--primary);
}

.empty-state h3 {
    color: white;
    font-size: 1.5rem;
    margin-bottom: 12px;
}

.empty-state p {
    color: rgba(255, 255, 255, 0.5);
    margin-bottom: 24px;
}

/* Announcement Card */
.announcement-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 20px;
    margin-bottom: 24px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.announcement-card:hover {
    border-color: rgba(32, 201, 151, 0.3);
    transform: translateY(-4px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.announcement-header {
    padding: 24px 28px 0;
    display: flex;
    align-items: flex-start;
    gap: 16px;
}

.announcement-icon {
    width: 48px;
    height: 48px;
    background: rgba(32, 201, 151, 0.15);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: var(--primary);
}

.announcement-meta {
    flex: 1;
}

.announcement-title {
    color: white;
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 8px;
    line-height: 1.3;
}

.announcement-info {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.announcement-date, .announcement-author {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.875rem;
}

.announcement-date svg, .announcement-author svg {
    width: 14px;
    height: 14px;
    opacity: 0.7;
}

.announcement-body {
    padding: 20px 28px 28px;
}

.announcement-content {
    color: rgba(255, 255, 255, 0.7);
    line-height: 1.8;
    font-size: 1rem;
}

/* Type Badge */
.announcement-type {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 500;
    background: rgba(32, 201, 151, 0.15);
    color: var(--primary);
    margin-left: auto;
}

/* Back Button */
.back-section {
    text-align: center;
    padding-top: 40px;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 32px;
    background: transparent;
    border: 2px solid rgba(255, 255, 255, 0.2);
    color: white;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-back:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: rgba(32, 201, 151, 0.1);
}

.btn-glow {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    padding: 14px 32px;
    border-radius: 12px;
    font-weight: 600;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 0 30px rgba(32, 201, 151, 0.3);
}

.btn-glow:hover {
    transform: translateY(-2px);
    box-shadow: 0 0 50px rgba(32, 201, 151, 0.5);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title { font-size: 2.5rem; }
    .announcement-header { flex-direction: column; }
    .announcement-type { margin-left: 0; margin-top: 12px; }
}
</style>

<!-- Hero Section -->
<section class="announcements-hero">
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>
    
    <div class="hero-grid">
        <div class="hero-content" style="text-align: left;">
            <div class="hero-badge" data-aos="fade-up">
                <span class="pulse"></span>
                Stay Informed
            </div>
            <h1 class="hero-title" data-aos="fade-up">
                Community <span class="highlight">Announcements</span>
            </h1>
            <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="100" style="margin: 0;">
                Stay updated with the latest news, health advisories, and important announcements from Barangay Bacong Health Center.
            </p>
        </div>
        
        <div class="hero-gabby" data-aos="fade-left" data-aos-delay="200">
            <img src="<?php echo BASE_URL; ?>assets/images/gabby_announcements.gif" alt="Gabby">
        </div>
    </div>
</section>

<!-- Announcements List -->
<section class="announcements-section">
    <div class="announcements-container">
        <?php if (empty($announcements)): ?>
            <div class="empty-state" data-aos="fade-up">
                <div class="empty-state-icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                </div>
                <?php if (!empty($announcements_error)): ?>
                    <h3>Unable to load announcements</h3>
                    <p>There was a problem loading announcements. Please try again later.</p>
                <?php else: ?>
                    <h3>No Announcements Yet</h3>
                    <p>Check back later for important updates from the health center.</p>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>" class="btn-glow">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Back to Home
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($announcements as $index => $announcement): ?>
                <div class="announcement-card" id="announcement-<?php echo $announcement['announcement_id']; ?>" data-aos="fade-up" data-aos-delay="<?php echo min($index * 50, 300); ?>">
                    <div class="announcement-header">
                        <div class="announcement-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                        </div>
                        <div class="announcement-meta">
                            <h3 class="announcement-title"><?php echo htmlspecialchars($announcement['title'] ?? ''); ?></h3>
                            <div class="announcement-info">
                                <span class="announcement-date">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <?php echo date('F j, Y', strtotime($announcement['created_at'])); ?>
                                </span>
                                <span class="announcement-author">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    <?php echo htmlspecialchars($announcement['full_name'] ?? 'System'); ?>
                                </span>
                            </div>
                        </div>
                        <span class="announcement-type">Update</span>
                    </div>
                    <div class="announcement-body">
                        <div class="announcement-content">
                            <?php echo nl2br(htmlspecialchars($announcement['content'] ?? '')); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div class="back-section" data-aos="fade-up">
                <a href="<?php echo BASE_URL; ?>" class="btn-back">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Back to Home
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>



<script>
if (typeof AOS !== 'undefined') {
    AOS.init({ duration: 600, once: true, offset: 50 });
}

// Highlight announcement if hash is present
document.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash;
    if (hash && hash.startsWith('#announcement-')) {
        const card = document.querySelector(hash);
        if (card) {
            setTimeout(() => {
                card.style.borderColor = 'var(--primary)';
                card.style.boxShadow = '0 0 30px rgba(32, 201, 151, 0.3)';
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 500);
        }
    }
});
</script>

<?php include_once __DIR__ . '/../../includes/footer_public.php'; ?>
