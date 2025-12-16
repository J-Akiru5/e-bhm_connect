<!DOCTYPE html>
<?php
// Consolidated Single Page Parallax Home
// Fetch announcements for the widget
$announcementsStmt = $pdo->query("SELECT a.*, b.full_name 
                    FROM announcements a 
                    LEFT JOIN bhw_users b ON a.bhw_id = b.bhw_id 
                    ORDER BY a.created_at DESC LIMIT 5");
$recentAnnouncements = $announcementsStmt->fetchAll(PDO::FETCH_ASSOC);

// Include the public header
include_once __DIR__ . '/../../includes/header_public.php';
?>

    <!-- Hero Section with Recent Updates Widget -->
    <section class="hero-section" id="hero">
        <!-- Animated Background Orbs -->
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
        <div class="hero-orb hero-orb-3"></div>
        
        <div class="hero-content">
            <!-- Left Side: Hero Text -->
            <div class="hero-text" data-aos="fade-up">
                <div class="hero-badge">
                    <span class="pulse"></span>
                    Barangay Health Services
                </div>
                
                <h1 class="hero-title">
                    Welcome to<br>
                    <span class="highlight">E-BHM Connect</span>
                </h1>
                
                <p class="hero-subtitle">
                    Digitizing health services for efficiency, accuracy, and accessibility in Barangay Bacong. Access your health records, schedule appointments, and stay connected with your community.
                </p>
                
                <div class="hero-actions">
                    <a href="<?php echo BASE_URL; ?>?page=login-patient" class="btn btn-primary btn-lg">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Resident Portal
                    </a>
                    <a href="<?php echo BASE_URL; ?>?page=home#contact" class="btn btn-outline btn-lg">Contact Us</a>
                </div>
                
                <div style="margin-top: var(--space-6);">
                    <button class="btn btn-glass" onclick="(typeof window.openGabbyPanel === 'function') ? window.openGabbyPanel() : window.location.href='<?php echo BASE_URL; ?>?page=portal_chatbot';">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                        Chat with Gabby
                    </button>
                </div>
            </div>
            
            <!-- Right Side: Recent Community Updates Widget -->
            <div class="updates-widget" data-aos="fade-up" data-aos-delay="200">
                <div class="updates-widget-header">
                    <h3>
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        Recent Community Updates
                    </h3>
                </div>
                <div class="updates-widget-content">
                    <?php if (empty($recentAnnouncements)): ?>
                        <div class="update-item">
                            <div class="update-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                            </div>
                            <div class="update-content">
                                <div class="update-title">No announcements yet</div>
                                <div class="update-time">Check back later for updates</div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recentAnnouncements as $announcement): ?>
                            <a href="<?php echo BASE_URL; ?>?page=announcements#announcement-<?php echo $announcement['announcement_id']; ?>" class="update-item">
                                <div class="update-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                    </svg>
                                </div>
                                <div class="update-content">
                                    <div class="update-title"><?php echo htmlspecialchars($announcement['title']); ?></div>
                                    <div class="update-time"><?php echo date('M j, Y', strtotime($announcement['created_at'])); ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="updates-widget-footer">
                    <a href="<?php echo BASE_URL; ?>?page=announcements">View All Announcements →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section: Are you a registered resident? -->
    <section class="cta-section" id="register-cta">
        <div class="cta-card" data-aos="zoom-in">
            <h2 class="cta-title">Are you a registered resident?</h2>
            <p class="cta-text">
                Join our digital health management system to access your health records, receive important health updates, and stay connected with Barangay Bacong Health Center.
            </p>
            <a href="<?php echo BASE_URL; ?>register-patient" class="btn btn-primary btn-lg">
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
    <section id="about" class="section section-light anchor-offset">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">Our Mission &amp; Vision</h2>
                <p class="section-subtitle">Committed to delivering accessible and quality healthcare services to every resident of Barangay Bacong.</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="info-card h-100">
                        <h3>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="margin-right: 8px; vertical-align: middle;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            Our Mission
                        </h3>
                        <p>To digitize and securely manage patient health records, streamline healthcare services, and empower Barangay Health Workers with digital tools for better community health management.</p>
                        <h3 class="mt-4">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="margin-right: 8px; vertical-align: middle;">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            Our Vision
                        </h3>
                        <p>To create a healthier community in Bacong, Dumangas by leveraging technology for efficient, reliable, and accessible health services for all residents.</p>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="info-card h-100">
                        <h3>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="margin-right: 8px; vertical-align: middle;">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            Barangay Information
                        </h3>
                        <p><strong>Barangay Name:</strong> Bacong, Dumangas</p>
                        <p><strong>Location:</strong> Dumangas, Iloilo, Philippines</p>
                        <p><strong>Population:</strong> 1,385+ residents</p>
                        <p><strong>Health Center:</strong> Bacong Barangay Health Center</p>
                        <p><strong>Contact:</strong> (033) 123-4567</p>
                        <p><strong>Email:</strong> healthcenter@bacong.gov</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="section section-alt anchor-offset">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">Our Services</h2>
                <p class="section-subtitle">Accessible, community-centered healthcare services for every resident.</p>
            </div>

            <div class="services-grid">
                <div class="service-card" data-aos="zoom-in" data-aos-delay="100">
                    <div class="icon-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                        </svg>
                    </div>
                    <h5>Vaccination Programs</h5>
                    <p>Comprehensive immunization schedules and records for infants, children, and adults.</p>
                </div>
                
                <div class="service-card" data-aos="zoom-in" data-aos-delay="200">
                    <div class="icon-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
                        </svg>
                    </div>
                    <h5>Health Checkups</h5>
                    <p>Routine health monitoring, blood pressure checks, and general consultations.</p>
                </div>
                
                <div class="service-card" data-aos="zoom-in" data-aos-delay="300">
                    <div class="icon-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                    <h5>Maternity Care</h5>
                    <p>Maternal health services including prenatal and postnatal care support.</p>
                </div>
                
                <div class="service-card" data-aos="zoom-in" data-aos-delay="400">
                    <div class="icon-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                        </svg>
                    </div>
                    <h5>Chronic Disease Management</h5>
                    <p>Support programs for diabetes, hypertension, and other chronic conditions.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section section-light anchor-offset">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">Contact Us</h2>
                <p class="section-subtitle">For inquiries or assistance, reach out to us through any of these channels.</p>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-md-5" data-aos="fade-right">
                    <div class="info-card h-100">
                        <h3>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="margin-right: 8px; vertical-align: middle;">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                            </svg>
                            Bacong Health Center
                        </h3>
                        <p><strong>Address:</strong> Bacong, Dumangas, Iloilo</p>
                        <p><strong>Contact:</strong> (033) 123-4567</p>
                        <p><strong>Email:</strong> healthcenter@bacong.gov</p>
                        <p><strong>Hours:</strong> Monday - Friday, 8AM - 5PM</p>
                    </div>
                </div>

                <div class="col-md-5" data-aos="fade-left">
                    <div class="info-card h-100">
                        <h3>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="margin-right: 8px; vertical-align: middle;">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            Barangay Hall
                        </h3>
                        <p><strong>Contact:</strong> (033) 987-6543</p>
                        <p><strong>Email:</strong> barangaybacong@gmail.com</p>
                        <div class="mt-4 d-flex gap-2 flex-wrap">
                            <a href="https://www.facebook.com/barangay.bacong.2025" target="_blank" class="btn btn-primary btn-sm">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                                Facebook
                            </a>
                            <a href="https://www.google.com/maps/search/?api=1&query=Barangay+Bacong+Dumangas+Iloilo" target="_blank" class="btn btn-outline-brand btn-sm">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                Google Maps
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
// Include the public footer
include_once __DIR__ . '/../../includes/footer_public.php';
?>

<script>
// Initialize AOS with desired options for the home page
if (typeof AOS !== 'undefined') {
  AOS.init({ duration: 800, once: true, offset: 100 });
}

// Smooth scroll for in-page anchors when already on the home page
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('a[href*="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      var href = anchor.getAttribute('href');
      if (!href) return;
      var parts = href.split('#');
      var hash = parts[1] || null;
      if (!hash) return;

      var shouldIntercept = href.indexOf('?page=home#') !== -1 || href.charAt(0) === '#' || (window.location.search.indexOf('page=home') !== -1);

      if (shouldIntercept) {
        var target = document.getElementById(hash);
        if (target) {
          e.preventDefault();
          target.scrollIntoView({behavior:'smooth', block:'start'});
        }
      }
    });
  });
});
</script>