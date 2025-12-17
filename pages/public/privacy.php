<?php
// Privacy Policy
include_once __DIR__ . '/../../includes/header_public.php';
?>
<style>
.glass-card { background: rgba(255, 255, 255, 0.08); -webkit-backdrop-filter: blur(20px); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 16px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); padding: 32px; margin-bottom: 24px; }
.page-header { text-align: center; margin-bottom: 48px; }
.page-title { font-size: 2.5rem; font-weight: 700; color: #ffffff; margin-bottom: 12px; }
.page-subtitle { color: rgba(255, 255, 255, 0.6); font-size: 1.125rem; }
.content-section { margin-bottom: 32px; }
.content-section h2 { font-size: 1.5rem; font-weight: 600; color: #20c997; margin-bottom: 16px; }
.content-section h3 { font-size: 1.25rem; font-weight: 600; color: #ffffff; margin: 20px 0 12px; }
.content-section p, .content-section li { color: rgba(255, 255, 255, 0.8); line-height: 1.8; font-size: 1rem; }
.content-section ul { margin-left: 20px; }
.content-section li { margin-bottom: 8px; }
.highlight-box { background: rgba(32, 201, 151, 0.1); border-left: 4px solid #20c997; padding: 16px 20px; border-radius: 8px; margin: 20px 0; }
</style>

<div class="container" style="padding: 48px 24px; max-width: 900px; margin: 0 auto;">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-shield-alt"></i> Privacy Policy</h1>
        <p class="page-subtitle">Last Updated: <?php echo date('F j, Y'); ?></p>
    </div>

    <div class="glass-card">
        <div class="content-section">
            <p>
                E-BHM Connect ("we," "our," or "us") is committed to protecting the privacy and security of your personal and health 
                information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use 
                our Electronic Barangay Health Management system.
            </p>
        </div>

        <div class="highlight-box">
            <p style="margin: 0; font-weight: 600; color: #20c997;">
                <i class="fas fa-info-circle"></i> Your privacy is important to us. We comply with the Data Privacy Act of 2012 (Republic Act No. 10173) 
                and other applicable Philippine privacy laws.
            </p>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-database"></i> Information We Collect</h2>
            
            <h3>Personal Information</h3>
            <ul>
                <li>Full name, date of birth, gender, and contact information</li>
                <li>Address and barangay location</li>
                <li>Email address and phone number</li>
                <li>Government-issued ID numbers (if provided)</li>
            </ul>

            <h3>Health Information</h3>
            <ul>
                <li>Medical history and chronic conditions</li>
                <li>Vital signs (blood pressure, temperature, weight, etc.)</li>
                <li>Visit records and consultation notes</li>
                <li>Prescribed medications and treatments</li>
                <li>Laboratory and diagnostic test results</li>
            </ul>

            <h3>Usage Information</h3>
            <ul>
                <li>Log-in credentials and session data</li>
                <li>System access logs and activity timestamps</li>
                <li>IP addresses and browser information</li>
            </ul>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-cogs"></i> How We Use Your Information</h2>
            <p>We use the collected information for the following purposes:</p>
            <ul>
                <li><strong>Healthcare Delivery:</strong> To provide, maintain, and improve barangay health services</li>
                <li><strong>Record Keeping:</strong> To maintain accurate and comprehensive health records</li>
                <li><strong>Communication:</strong> To send health reminders, appointment notifications, and important announcements</li>
                <li><strong>Reporting:</strong> To generate aggregated health statistics and reports for public health monitoring</li>
                <li><strong>System Security:</strong> To protect against unauthorized access and maintain data integrity</li>
                <li><strong>Compliance:</strong> To comply with legal obligations and regulatory requirements</li>
            </ul>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-lock"></i> Data Security</h2>
            <p>We implement industry-standard security measures to protect your information:</p>
            <ul>
                <li>Encrypted data transmission using HTTPS/SSL</li>
                <li>Secure password hashing and authentication</li>
                <li>Role-based access control for healthcare workers</li>
                <li>Regular security audits and system monitoring</li>
                <li>Secure database storage with regular backups</li>
            </ul>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-share-alt"></i> Information Sharing</h2>
            <p>We do not sell, trade, or rent your personal information. We may share your information only in the following circumstances:</p>
            <ul>
                <li><strong>Authorized Healthcare Workers:</strong> BHWs and healthcare providers involved in your care</li>
                <li><strong>Government Agencies:</strong> Department of Health (DOH) and local health units for public health reporting</li>
                <li><strong>Legal Requirements:</strong> When required by law, court order, or government regulation</li>
                <li><strong>Emergency Situations:</strong> When necessary to protect your health or safety</li>
            </ul>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-user-shield"></i> Your Rights</h2>
            <p>Under the Data Privacy Act of 2012, you have the following rights:</p>
            <ul>
                <li><strong>Right to Access:</strong> Request a copy of your personal and health information</li>
                <li><strong>Right to Correction:</strong> Request correction of inaccurate or incomplete data</li>
                <li><strong>Right to Erasure:</strong> Request deletion of your data (subject to legal retention requirements)</li>
                <li><strong>Right to Object:</strong> Object to certain data processing activities</li>
                <li><strong>Right to Data Portability:</strong> Request your data in a structured, machine-readable format</li>
                <li><strong>Right to File a Complaint:</strong> Lodge a complaint with the National Privacy Commission</li>
            </ul>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-clock"></i> Data Retention</h2>
            <p>
                We retain your personal and health information for as long as necessary to fulfill the purposes outlined in this policy 
                and as required by applicable laws and regulations. Medical records are typically retained for a minimum period as 
                mandated by Philippine law.
            </p>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-child"></i> Children's Privacy</h2>
            <p>
                Our system may collect health information for minors (under 18 years old) when guardians or parents register them for 
                health services. We require parental or guardian consent for collecting and processing information of minors.
            </p>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-edit"></i> Changes to This Policy</h2>
            <p>
                We may update this Privacy Policy from time to time. We will notify registered users of any material changes via email 
                or system notifications. The "Last Updated" date at the top of this page indicates when the policy was last revised.
            </p>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-envelope"></i> Contact Us</h2>
            <p>
                If you have questions, concerns, or requests regarding this Privacy Policy or how we handle your information, please contact:
            </p>
            <div class="highlight-box">
                <p style="margin: 0;">
                    <strong>Data Protection Officer</strong><br>
                    E-BHM Connect System<br>
                    Email: <a href="mailto:privacy@ebhm-connect.ph" style="color: #20c997;">privacy@ebhm-connect.ph</a><br>
                    Phone: [Your Contact Number]
                </p>
            </div>
        </div>

        <div class="highlight-box" style="margin-top: 40px;">
            <p style="margin: 0; font-weight: 600;">
                <i class="fas fa-check-circle"></i> By using E-BHM Connect, you acknowledge that you have read, understood, and agree 
                to be bound by this Privacy Policy.
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
