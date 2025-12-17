<?php
// Help & Support
include_once __DIR__ . '/../../includes/header_public.php';
?>
<style>
.glass-card { background: rgba(255, 255, 255, 0.08); -webkit-backdrop-filter: blur(20px); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 16px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); padding: 32px; margin-bottom: 24px; transition: transform 0.25s ease; }
.glass-card:hover { transform: translateY(-4px); box-shadow: 0 16px 48px rgba(0, 0, 0, 0.16); }
.page-header { text-align: center; margin-bottom: 48px; }
.page-title { font-size: 2.5rem; font-weight: 700; color: #ffffff; margin-bottom: 12px; }
.page-subtitle { color: rgba(255, 255, 255, 0.6); font-size: 1.125rem; }
.help-section { margin-bottom: 32px; }
.help-section h2 { font-size: 1.75rem; font-weight: 600; color: #20c997; margin-bottom: 20px; }
.faq-item { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 20px; margin-bottom: 16px; cursor: pointer; transition: all 0.25s ease; }
.faq-item:hover { background: rgba(255, 255, 255, 0.08); }
.faq-question { font-size: 1.125rem; font-weight: 600; color: #ffffff; display: flex; justify-content: space-between; align-items: center; }
.faq-answer { color: rgba(255, 255, 255, 0.7); line-height: 1.8; margin-top: 12px; display: none; }
.faq-item.active .faq-answer { display: block; }
.faq-item .toggle-icon { transition: transform 0.3s ease; }
.faq-item.active .toggle-icon { transform: rotate(180deg); }
.support-grid { display: grid; grid-template-columns: repeat(1, 1fr); gap: 20px; margin-top: 24px; }
@media (min-width: 768px) { .support-grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1024px) { .support-grid { grid-template-columns: repeat(3, 1fr); } }
.support-card { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 24px; text-align: center; }
.support-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 16px; }
.support-icon.primary { background: rgba(32, 201, 151, 0.2); color: #20c997; }
.support-icon.info { background: rgba(99, 102, 241, 0.2); color: #6366f1; }
.support-icon.warning { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.support-title { font-size: 1.125rem; font-weight: 600; color: #ffffff; margin-bottom: 8px; }
.support-description { color: rgba(255, 255, 255, 0.6); font-size: 0.875rem; line-height: 1.5; margin-bottom: 16px; }
.btn-support { padding: 10px 20px; background: rgba(32, 201, 151, 0.2); border: 1px solid #20c997; border-radius: 10px; color: #20c997; font-weight: 500; text-decoration: none; display: inline-block; transition: all 0.25s ease; }
.btn-support:hover { background: #20c997; color: #ffffff; }
</style>

<div class="container" style="padding: 48px 24px; max-width: 1200px; margin: 0 auto;">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-question-circle"></i> Help & Support</h1>
        <p class="page-subtitle">Find answers to your questions and get assistance</p>
    </div>

    <!-- Quick Support Options -->
    <div class="glass-card">
        <h2 style="text-align: center; color: #20c997; margin-bottom: 32px;">
            <i class="fas fa-headset"></i> How Can We Help You?
        </h2>
        <div class="support-grid">
            <div class="support-card">
                <div class="support-icon primary">
                    <i class="fas fa-book"></i>
                </div>
                <h3 class="support-title">User Guide</h3>
                <p class="support-description">Comprehensive documentation on using E-BHM Connect</p>
                <a href="#user-guide" class="btn-support">View Guide</a>
            </div>

            <div class="support-card">
                <div class="support-icon info">
                    <i class="fas fa-video"></i>
                </div>
                <h3 class="support-title">Video Tutorials</h3>
                <p class="support-description">Step-by-step video guides for common tasks</p>
                <a href="#tutorials" class="btn-support">Watch Videos</a>
            </div>

            <div class="support-card">
                <div class="support-icon warning">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3 class="support-title">Contact Support</h3>
                <p class="support-description">Get in touch with our support team</p>
                <a href="#contact" class="btn-support">Contact Us</a>
            </div>
        </div>
    </div>

    <!-- Frequently Asked Questions -->
    <div class="glass-card" id="faq">
        <div class="help-section">
            <h2><i class="fas fa-comments"></i> Frequently Asked Questions</h2>
            
            <div class="faq-item" onclick="toggleFAQ(this)">
                <div class="faq-question">
                    <span>How do I register as a Barangay Health Worker?</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <div class="faq-answer">
                    To register as a BHW, click on "BHW Login" from the homepage, then select "Register New Account". 
                    Fill in your personal information and verify your email address. Your account will be pending approval 
                    by a system administrator before you can access the full system.
                </div>
            </div>

            <div class="faq-item" onclick="toggleFAQ(this)">
                <div class="faq-question">
                    <span>How do I reset my password?</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <div class="faq-answer">
                    On the login page, click "Forgot Password". Enter your registered email address and you'll receive 
                    a password reset link. Follow the instructions in the email to create a new password. If you don't 
                    receive the email, check your spam folder or contact support.
                </div>
            </div>

            <div class="faq-item" onclick="toggleFAQ(this)">
                <div class="faq-question">
                    <span>How do I add a new patient record?</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <div class="faq-answer">
                    Navigate to the "Patients" page from the admin dashboard and click the "Add New Patient" button. 
                    Fill in all required patient information including name, date of birth, contact details, and address. 
                    Click "Save" to create the patient record. You can then add visit records and vital signs from the 
                    patient's profile page.
                </div>
            </div>

            <div class="faq-item" onclick="toggleFAQ(this)">
                <div class="faq-question">
                    <span>How do I track inventory and medicine stock?</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <div class="faq-answer">
                    Go to the "Inventory" page to view all medical supplies and medicines. You can add new items, update 
                    quantities, set low-stock alerts, and track expiration dates. The system automatically updates stock 
                    levels when you dispense medicines to patients.
                </div>
            </div>

            <div class="faq-item" onclick="toggleFAQ(this)">
                <div class="faq-question">
                    <span>Can I generate reports for DOH submissions?</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <div class="faq-answer">
                    Yes! Navigate to the "Reports" page where you can generate various reports including patient lists, 
                    chronic disease reports, inventory stock reports, and visit records. You can customize date ranges 
                    and export reports in PDF format for official submissions.
                </div>
            </div>

            <div class="faq-item" onclick="toggleFAQ(this)">
                <div class="faq-question">
                    <span>How does the SMS notification system work?</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <div class="faq-answer">
                    The SMS system sends automated health reminders and announcements to registered patients. You can 
                    broadcast messages to all patients or specific groups. The system requires SMS credits which can be 
                    configured in the App Settings by a superadmin.
                </div>
            </div>

            <div class="faq-item" onclick="toggleFAQ(this)">
                <div class="faq-question">
                    <span>Is my patient data secure and confidential?</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <div class="faq-answer">
                    Absolutely! E-BHM Connect uses industry-standard encryption, secure authentication, and role-based 
                    access control. We comply with the Data Privacy Act of 2012 and maintain strict confidentiality of 
                    all health information. All system activities are logged for audit purposes.
                </div>
            </div>

            <div class="faq-item" onclick="toggleFAQ(this)">
                <div class="faq-question">
                    <span>Can patients access their own health records?</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <div class="faq-answer">
                    Yes, patients can register for a patient portal account where they can view their visit history, 
                    vital signs, prescribed medications, and upcoming appointments. They can also use the AI health 
                    chatbot for basic health inquiries.
                </div>
            </div>
        </div>
    </div>

    <!-- User Guide -->
    <div class="glass-card" id="user-guide">
        <div class="help-section">
            <h2><i class="fas fa-book-open"></i> Quick Start Guide</h2>
            <h3 style="color: #ffffff; margin-top: 24px;">For Barangay Health Workers</h3>
            <ol style="color: rgba(255, 255, 255, 0.8); line-height: 2; font-size: 1rem;">
                <li><strong>Dashboard:</strong> Overview of patients, inventory, and announcements</li>
                <li><strong>Patients:</strong> Manage patient records, add visits, record vital signs</li>
                <li><strong>Inventory:</strong> Track medicines and medical supplies</li>
                <li><strong>Programs:</strong> Create and manage health programs and campaigns</li>
                <li><strong>Announcements:</strong> Broadcast important health information</li>
                <li><strong>Messages:</strong> Send SMS notifications to patients</li>
                <li><strong>Reports:</strong> Generate comprehensive health reports</li>
                <li><strong>Settings:</strong> Update your profile and preferences</li>
            </ol>
        </div>
    </div>

    <!-- Contact Support -->
    <div class="glass-card" id="contact">
        <div class="help-section">
            <h2><i class="fas fa-phone-alt"></i> Contact Support</h2>
            <p style="color: rgba(255, 255, 255, 0.8); line-height: 1.8; margin-bottom: 24px;">
                If you can't find the answer to your question in our FAQ or need further assistance, please don't hesitate to contact our support team.
            </p>
            
            <div class="support-grid">
                <div class="support-card" style="text-align: left;">
                    <div class="support-icon primary">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3 class="support-title">Email Support</h3>
                    <p style="color: rgba(255, 255, 255, 0.6); margin-bottom: 8px;">
                        <a href="mailto:support@ebhm-connect.ph" style="color: #20c997;">support@ebhm-connect.ph</a>
                    </p>
                    <p style="color: rgba(255, 255, 255, 0.5); font-size: 0.875rem;">Response time: 24-48 hours</p>
                </div>

                <div class="support-card" style="text-align: left;">
                    <div class="support-icon info">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3 class="support-title">Phone Support</h3>
                    <p style="color: rgba(255, 255, 255, 0.6); margin-bottom: 8px;">
                        <a href="tel:+639123456789" style="color: #6366f1;">+63 912 345 6789</a>
                    </p>
                    <p style="color: rgba(255, 255, 255, 0.5); font-size: 0.875rem;">Mon-Fri: 8:00 AM - 5:00 PM</p>
                </div>

                <div class="support-card" style="text-align: left;">
                    <div class="support-icon warning">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3 class="support-title">Office Address</h3>
                    <p style="color: rgba(255, 255, 255, 0.6); margin-bottom: 8px;">
                        Barangay Health Office<br>
                        [Your Address Here]
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div style="text-align: center; margin-top: 48px;">
        <a href="<?php echo BASE_URL; ?>" class="btn btn-primary" style="padding: 14px 32px; font-size: 1.125rem;">
            <i class="fas fa-home"></i> Back to Home
        </a>
    </div>
</div>

<script>
function toggleFAQ(element) {
    // Close all other FAQ items
    document.querySelectorAll('.faq-item').forEach(item => {
        if (item !== element) {
            item.classList.remove('active');
        }
    });
    
    // Toggle current item
    element.classList.toggle('active');
}
</script>

<?php include_once __DIR__ . '/../../includes/footer_public.php'; ?>
