<?php
include_once __DIR__ . '/../../includes/header_public.php';
?>

<style>
/* Reusing Immersive Hero Styles from detailed pages */
.privacy-hero {
    background: linear-gradient(135deg, var(--dark) 0%, rgba(15, 23, 42, 0.95) 100%);
    position: relative;
    padding: 120px 0 60px;
    overflow: hidden;
    margin-bottom: -40px;
}

.privacy-content {
    background: var(--dark);
    min-height: 50vh;
    padding-bottom: 80px;
    position: relative;
    z-index: 2;
}

.policy-card {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: var(--radius-xl);
    padding: 40px;
    margin-top: 40px;
}

.policy-section {
    margin-bottom: 30px;
}

.policy-section h3 {
    color: var(--primary);
    margin-bottom: 15px;
    font-size: 1.25rem;
}

.policy-section p {
    color: var(--gray-400);
    line-height: 1.7;
    margin-bottom: 10px;
}

.last-updated {
    color: var(--gray-500);
    font-size: 0.9rem;
    margin-bottom: 20px;
    display: block;
}
</style>

<!-- Hero Section -->
<section class="privacy-hero">
    <!-- Animated Orbs -->
    <div class="hero-orb hero-orb-1" style="top: 10%; left: 10%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(32,201,151,0.15) 0%, rgba(0,0,0,0) 70%);"></div>
    <div class="hero-orb hero-orb-2" style="bottom: 20%; right: 10%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(13,110,253,0.1) 0%, rgba(0,0,0,0) 70%);"></div>
    
    <div class="container position-relative z-1 text-center">
        <h1 class="display-4 fw-bold text-white mb-3" data-aos="fade-up">Privacy Policy</h1>
        <p class="lead text-white-50 mx-auto" style="max-width: 600px;" data-aos="fade-up" data-aos-delay="100">
            How we collect, use, and protect your personal information.
        </p>
    </div>
</section>

<!-- Content Section -->
<div class="privacy-content">
    <div class="container">
        <div class="policy-card" data-aos="fade-up" data-aos-delay="200">
            <span class="last-updated">Last Updated: December 2025</span>
            
            <div class="policy-section">
                <h3>1. Information Collection</h3>
                <p>We collect information necessary to provide health services, including personal identification (name, age, address) and health records. This data is collected securely through our Resident Portal or directly by Barangay Health Workers.</p>
            </div>

            <div class="policy-section">
                <h3>2. Use of Information</h3>
                <p>Your data is used solely for:</p>
                <ul style="color: var(--gray-400); line-height: 1.7;">
                    <li>Managing your health records and visits.</li>
                    <li>Sending important health announcements and reminders.</li>
                    <li>Generating anonymized health statistics for Barangay Bacong.</li>
                </ul>
            </div>

            <div class="policy-section">
                <h3>3. Data Protection</h3>
                <p>We implement security measures to protect your personal information. Access is restricted to authorized health personnel only. We do not sell or share your data with third parties without your consent, except as required by law.</p>
            </div>

            <div class="policy-section">
                <h3>4. Contact Us</h3>
                <p>If you have any questions about this Privacy Policy, please contact the Barangay Health Center or visit the <a href="<?php echo BASE_URL; ?>?page=home#contact" style="color: var(--primary);">Contact page</a>.</p>
            </div>
        </div>
    </div>
</div>

<script>
if (typeof AOS !== 'undefined') {
    AOS.init({ duration: 800, once: true });
}
</script>

<?php include_once __DIR__ . '/../../includes/footer_public.php'; ?>
