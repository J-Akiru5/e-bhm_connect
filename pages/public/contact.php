<?php
// Immersive Contact Page
include_once __DIR__ . '/../../includes/header_public.php';
?>

<style>
/* Immersive Theme Styles */
:root {
    --primary: #20c997;
    --primary-dark: #0f5132;
    --dark: #0f172a;
    --glass-bg: rgba(255, 255, 255, 0.03);
    --glass-border: rgba(255, 255, 255, 0.08);
    --text-muted: rgba(255, 255, 255, 0.6);
}

.immersive-hero {
    min-height: 100vh;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.95) 100%),
                url('<?php echo BASE_URL; ?>assets/images/hero_bg.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    position: relative;
    overflow: hidden;
    padding-top: 120px;
    padding-bottom: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Animated Orbs */
.hero-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.4;
    animation: orbFloat 20s ease-in-out infinite;
    pointer-events: none;
    z-index: 1;
}

.hero-orb-1 {
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, var(--primary) 0%, transparent 70%);
    top: -200px;
    left: -200px;
}

.hero-orb-2 {
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, #3b82f6 0%, transparent 70%);
    bottom: -100px;
    right: -100px;
    animation-delay: -5s;
}

@keyframes orbFloat {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(30px, -30px) scale(1.05); }
}

/* Content */
.contact-container {
    position: relative;
    z-index: 2;
    max-width: 1200px;
    width: 100%;
    padding: 0 20px;
}

.page-title {
    font-size: 3.5rem;
    font-weight: 800;
    color: white;
    margin-bottom: 1rem;
    text-align: center;
}

.page-subtitle {
    color: var(--text-muted);
    font-size: 1.25rem;
    text-align: center;
    margin-bottom: 4rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.glass-card {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 40px;
    height: 100%;
    transition: all 0.3s ease;
}

.glass-card:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(32, 201, 151, 0.3);
    transform: translateY(-5px);
}

.card-icon {
    width: 60px;
    height: 60px;
    background: rgba(32, 201, 151, 0.1);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 1.5rem;
    margin-bottom: 24px;
}

.card-title {
    color: white;
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 24px;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 20px;
    color: rgba(255, 255, 255, 0.8);
}

.contact-item i {
    color: var(--primary);
    width: 24px;
    margin-right: 12px;
    margin-top: 4px;
}

.contact-label {
    display: block;
    font-size: 0.875rem;
    color: var(--text-muted);
    margin-bottom: 4px;
}

.contact-value {
    font-size: 1.1rem;
    color: white;
    font-weight: 500;
}

.social-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    width: 100%;
    justify-content: center;
    margin-bottom: 12px;
}

.btn-facebook {
    background: rgba(24, 119, 242, 0.15);
    color: #1877F2;
    border: 1px solid rgba(24, 119, 242, 0.3);
}

.btn-facebook:hover {
    background: #1877F2;
    color: white;
}

.btn-maps {
    background: rgba(52, 168, 83, 0.15);
    color: #34A853;
    border: 1px solid rgba(52, 168, 83, 0.3);
}

.btn-maps:hover {
    background: #34A853;
    color: white;
}
</style>

<div class="immersive-hero">
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>

    <div class="contact-container">
        <div data-aos="fade-down">
            <h1 class="page-title">Contact Us</h1>
            <p class="page-subtitle">We're here to help. Reach out to the Barangay Health Center or Barangay Hall for any inquiries.</p>
        </div>

        <div class="row g-4">
            <!-- Health Center Card -->
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="glass-card">
                    <div class="card-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h3 class="card-title">Bacong Health Center</h3>
                    
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <span class="contact-label">Address</span>
                            <span class="contact-value">Bacong, Dumangas</span>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-phone-alt"></i>
                        <div>
                            <span class="contact-label">Contact Number</span>
                            <span class="contact-value">(033) 123-4567</span>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-mobile-alt"></i>
                        <div>
                            <span class="contact-label">Mobile</span>
                            <span class="contact-value">(033) 765-4321</span>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <span class="contact-label">Health Center Email</span>
                            <span class="contact-value">healthcenter@bacong.gov</span>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-notes-medical"></i>
                        <div>
                            <span class="contact-label">General Inquiries</span>
                            <span class="contact-value">ebhw.bacong@gmail.com</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barangay Hall Card -->
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="glass-card">
                    <div class="card-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 class="card-title">Barangay Hall</h3>
                    
                    <div class="contact-item">
                        <i class="fas fa-phone-alt"></i>
                        <div>
                            <span class="contact-label">Contact Number</span>
                            <span class="contact-value">(033) 987-6543</span>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <span class="contact-label">Official Email</span>
                            <span class="contact-value">barangaybacong@gmail.com</span>
                        </div>
                    </div>

                    <div class="mt-5">
                        <a href="https://www.facebook.com/barangay.bacong.2025" target="_blank" class="social-btn btn-facebook">
                            <i class="fab fa-facebook"></i> Follow on Facebook
                        </a>
                        <a href="https://www.google.com/maps/search/?api=1&query=Barangay+Bacong+Dumangas+Iloilo" target="_blank" class="social-btn btn-maps">
                            <i class="fas fa-map-marked-alt"></i> Find on Google Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include the public footer
include_once __DIR__ . '/../../includes/footer_public.php';
?>
