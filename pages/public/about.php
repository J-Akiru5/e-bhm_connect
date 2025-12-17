<?php
// About E-BHM Connect
include_once __DIR__ . '/../../includes/header_public.php';
?>
<style>
.glass-card { background: rgba(255, 255, 255, 0.08); -webkit-backdrop-filter: blur(20px); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 16px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); padding: 32px; margin-bottom: 24px; }
.page-header { text-align: center; margin-bottom: 48px; }
.page-title { font-size: 2.5rem; font-weight: 700; color: #ffffff; margin-bottom: 12px; }
.page-subtitle { color: rgba(255, 255, 255, 0.6); font-size: 1.125rem; }
.content-section { margin-bottom: 32px; }
.content-section h2 { font-size: 1.75rem; font-weight: 600; color: #20c997; margin-bottom: 16px; }
.content-section p { color: rgba(255, 255, 255, 0.8); line-height: 1.8; font-size: 1rem; }
.feature-grid { display: grid; grid-template-columns: repeat(1, 1fr); gap: 20px; margin-top: 24px; }
@media (min-width: 768px) { .feature-grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1024px) { .feature-grid { grid-template-columns: repeat(3, 1fr); } }
.feature-card { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 24px; text-align: center; transition: all 0.25s ease; }
.feature-card:hover { transform: translateY(-4px); background: rgba(255, 255, 255, 0.08); }
.feature-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 16px; }
.feature-icon.primary { background: rgba(32, 201, 151, 0.2); color: #20c997; }
.feature-icon.info { background: rgba(99, 102, 241, 0.2); color: #6366f1; }
.feature-icon.warning { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.feature-title { font-size: 1.125rem; font-weight: 600; color: #ffffff; margin-bottom: 8px; }
.feature-description { color: rgba(255, 255, 255, 0.6); font-size: 0.875rem; line-height: 1.5; }
</style>

<div class="container" style="padding: 48px 24px; max-width: 1200px; margin: 0 auto;">
    <div class="page-header">
        <h1 class="page-title">About E-BHM Connect</h1>
        <p class="page-subtitle">Empowering Community Health Through Technology</p>
    </div>

    <div class="glass-card">
        <div class="content-section">
            <h2><i class="fas fa-info-circle"></i> What is E-BHM Connect?</h2>
            <p>
                E-BHM Connect is a comprehensive Electronic Barangay Health Management system designed to digitize and streamline 
                health services in barangay communities. Our platform bridges the gap between Barangay Health Workers (BHWs), 
                healthcare providers, and community members through an innovative web-based solution.
            </p>
            <p>
                Built with modern technology and following mobile-first design principles, E-BHM Connect ensures that healthcare 
                management is accessible, efficient, and user-friendly for all stakeholders in the community health ecosystem.
            </p>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-bullseye"></i> Our Mission</h2>
            <p>
                To transform barangay health services by providing accessible, efficient, and comprehensive digital tools that empower 
                Barangay Health Workers to deliver quality healthcare to their communities while ensuring patient data security and 
                privacy.
            </p>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-eye"></i> Our Vision</h2>
            <p>
                A future where every barangay in the Philippines has access to modern digital health management systems, enabling 
                data-driven decision making, improved patient outcomes, and seamless healthcare delivery at the grassroots level.
            </p>
        </div>
    </div>

    <div class="glass-card">
        <h2 style="text-align: center; color: #20c997; margin-bottom: 32px;">
            <i class="fas fa-star"></i> Key Features
        </h2>
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="feature-title">Patient Management</h3>
                <p class="feature-description">Comprehensive patient records, visit tracking, and health monitoring</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon info">
                    <i class="fas fa-box"></i>
                </div>
                <h3 class="feature-title">Inventory System</h3>
                <p class="feature-description">Track medical supplies, medicines, and equipment in real-time</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon warning">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <h3 class="feature-title">Vital Signs Monitoring</h3>
                <p class="feature-description">Record and track patient vitals over time</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon primary">
                    <i class="fas fa-sms"></i>
                </div>
                <h3 class="feature-title">SMS Notifications</h3>
                <p class="feature-description">Automated health reminders and appointment alerts</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon info">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 class="feature-title">Reports & Analytics</h3>
                <p class="feature-description">Generate comprehensive health reports and insights</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon warning">
                    <i class="fas fa-robot"></i>
                </div>
                <h3 class="feature-title">AI Chatbot</h3>
                <p class="feature-description">24/7 health information and guidance</p>
            </div>
        </div>
    </div>

    <div class="glass-card">
        <div class="content-section">
            <h2><i class="fas fa-handshake"></i> Our Commitment</h2>
            <p>
                E-BHM Connect is committed to maintaining the highest standards of data security, patient privacy, and system reliability. 
                We continuously improve our platform based on feedback from BHWs and healthcare professionals to ensure it meets the 
                evolving needs of community health services.
            </p>
            <p>
                We believe that technology should empower, not complicate. That's why we've designed E-BHM Connect with simplicity and 
                efficiency in mind, ensuring that even users with minimal technical experience can navigate and utilize the system effectively.
            </p>
        </div>
    </div>

    <div style="text-align: center; margin-top: 48px;">
        <a href="<?php echo BASE_URL; ?>" class="btn btn-primary" style="padding: 14px 32px; font-size: 1.125rem;">
            <i class="fas fa-home"></i> Back to Home
        </a>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer_public.php'; ?>
