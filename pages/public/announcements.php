<?php
$stmt = $pdo->query("SELECT a.*, b.full_name 
                    FROM announcements a 
                    LEFT JOIN bhw_users b ON a.bhw_id = b.bhw_id 
                    ORDER BY a.created_at DESC");
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once __DIR__ . '/../../includes/header_public.php';
?>

<!-- Announcements Page Header -->
<section class="hero-section" style="min-height: 40vh; padding: var(--space-20) var(--space-6);">
    <div class="hero-orb hero-orb-1" style="opacity: 0.2;"></div>
    <div class="hero-orb hero-orb-2" style="opacity: 0.2;"></div>
    
    <div style="position: relative; z-index: 10; text-align: center; max-width: 800px;">
        <div class="hero-badge" data-aos="fade-up">
            <span class="pulse"></span>
            Stay Informed
        </div>
        <h1 class="hero-title" data-aos="fade-up" style="font-size: var(--font-size-4xl);">
            Community <span class="highlight">Announcements</span>
        </h1>
        <p class="hero-subtitle" data-aos="fade-up" style="margin: 0 auto;">
            Stay updated with the latest news, health advisories, and important announcements from Barangay Bacong Health Center.
        </p>
    </div>
</section>

<!-- Announcements List -->
<section class="section section-light" style="padding-top: var(--space-12);">
    <div class="container">
        <?php if (empty($announcements)): ?>
            <div class="glass-card-light text-center" style="padding: var(--space-12);" data-aos="fade-up">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--gray-400)" stroke-width="1.5" style="margin-bottom: var(--space-4);">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                <h3 style="color: var(--gray-600);">No Announcements Yet</h3>
                <p style="color: var(--gray-500);">Check back later for important updates from the health center.</p>
                <a href="<?php echo BASE_URL; ?>?page=home" class="btn btn-primary" style="margin-top: var(--space-4);">Back to Home</a>
            </div>
        <?php else: ?>
            <div class="announcements-grid">
                <?php foreach ($announcements as $index => $announcement): ?>
                    <div class="announcement-card" id="announcement-<?php echo $announcement['announcement_id']; ?>" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                        <div class="announcement-card-header">
                            <h4>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px; vertical-align: middle;">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                <?php echo htmlspecialchars($announcement['title'] ?? ''); ?>
                            </h4>
                        </div>
                        <div class="announcement-card-body">
                            <p style="color: var(--gray-700); line-height: 1.7; margin: 0;">
                                <?php echo nl2br(htmlspecialchars($announcement['content'] ?? '')); ?>
                            </p>
                        </div>
                        <div class="announcement-card-footer">
                            <div style="display: flex; align-items: center; gap: var(--space-2);">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <span><?php echo date('F j, Y, g:i a', strtotime($announcement['created_at'])); ?></span>
                                <span style="margin: 0 var(--space-2);">•</span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span><?php echo htmlspecialchars($announcement['full_name'] ?? 'System'); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Back to Home Link -->
        <div class="text-center" style="margin-top: var(--space-12);" data-aos="fade-up">
            <a href="<?php echo BASE_URL; ?>?page=home" class="btn btn-outline-brand btn-lg">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to Home
            </a>
        </div>
    </div>
</section>

<script>
if (typeof AOS !== 'undefined') {
    AOS.init({ duration: 600, once: true, offset: 50 });
}
</script>

<?php
include_once __DIR__ . '/../../includes/footer_public.php';
?>
