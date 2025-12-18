<?php
// Immersive Help & Support Page
include_once __DIR__ . '/../../includes/header_public.php';
?>

<!-- Custom Styles -->
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

.help-hero {
    min-height: 40vh;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.95) 100%),
                url('<?php echo BASE_URL; ?>assets/images/hero_bg.jpg');
    background-size: cover;
    background-position: center;
    position: relative;
    overflow: hidden;
    padding-top: 120px;
    padding-bottom: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: -40px; /* Overlap effect */
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
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, #3b82f6 0%, transparent 70%);
    bottom: -50px;
    right: -50px;
}

/* Content Container */
.help-content {
    position: relative;
    z-index: 10;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px 80px;
}

.page-title {
    font-size: 3rem;
    font-weight: 800;
    color: white;
    margin-bottom: 1rem;
    text-align: center;
}

.page-subtitle {
    color: var(--text-muted);
    font-size: 1.25rem;
    text-align: center;
    margin-bottom: 2rem;
}

/* Glass Card */
.glass-card {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 40px;
    margin-bottom: 32px;
    transition: all 0.3s ease;
}

.glass-card:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(32, 201, 151, 0.2);
}

/* Support Grid */
.support-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-top: 32px;
}

.support-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid var(--glass-border);
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    transition: all 0.3s ease;
}

.support-card:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.06);
    border-color: var(--primary);
}

.support-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto 16px;
}

.support-icon.primary { background: rgba(32, 201, 151, 0.15); color: var(--primary); }
.support-icon.info { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
.support-icon.warning { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }

.btn-support {
    display: inline-block;
    padding: 10px 24px;
    background: rgba(32, 201, 151, 0.1);
    border: 1px solid var(--primary);
    border-radius: 12px;
    color: var(--primary);
    font-weight: 500;
    text-decoration: none;
    margin-top: 16px;
    transition: all 0.2s;
}

.btn-support:hover {
    background: var(--primary);
    color: white;
}

/* FAQ */
.faq-item {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid var(--glass-border);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 16px;
    cursor: pointer;
    transition: all 0.2s;
}

.faq-item:hover, .faq-item.active {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(32, 201, 151, 0.3);
}

.faq-question {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
}

.faq-answer {
    color: var(--text-muted);
    line-height: 1.7;
    margin-top: 16px;
    display: none;
    padding-top: 16px;
    border-top: 1px solid var(--glass-border);
}

.faq-item.active .faq-answer { display: block; }
.faq-item .toggle-icon { transition: transform 0.3s ease; }
.faq-item.active .toggle-icon { transform: rotate(180deg); color: var(--primary); }

/* User Guide Tabs */
.guide-tabs {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    border-bottom: 1px solid var(--glass-border);
    padding-bottom: 1rem;
    overflow-x: auto;
}

.guide-tab {
    background: transparent;
    border: none;
    color: var(--text-muted);
    padding: 0.5rem 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border-radius: 8px;
    white-space: nowrap;
}

.guide-tab:hover {
    color: white;
    background: rgba(255, 255, 255, 0.05);
}

.guide-tab.active {
    color: var(--primary);
    background: rgba(32, 201, 151, 0.1);
}

.guide-content {
    display: none;
    animation: fadeIn 0.3s ease;
}

.guide-content.active {
    display: block;
}

.step-item {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.step-number {
    width: 32px;
    height: 32px;
    background: var(--primary);
    color: var(--dark);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    flex-shrink: 0;
}

.step-text h4 {
    color: white;
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.step-text p {
    color: var(--text-muted);
    font-size: 0.95rem;
    line-height: 1.6;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<div class="help-hero">
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>
    
    <div class="text-center" style="position: relative; z-index: 2;" data-aos="fade-down">
        <h1 class="page-title">Help & Support</h1>
        <p class="page-subtitle">Find answers to your questions and get assistance</p>
    </div>
</div>

<div class="help-content">
    <!-- Quick Support Options -->
    <div class="glass-card" data-aos="fade-up">
        <h2 class="text-white text-center mb-4"><i class="fas fa-headset text-primary me-2"></i> How Can We Help You?</h2>
        <div class="support-grid">
            <div class="support-card">
                <div class="support-icon primary"><i class="fas fa-book"></i></div>
                <h3 class="text-white h5 mb-2">User Guide</h3>
                <p class="text-white-50 small">Documentation on using E-BHM Connect</p>
                <a href="#user-guide" class="btn-support">View Guide</a>
            </div>

            <div class="support-card">
                <div class="support-icon info"><i class="fas fa-magic"></i></div>
                <h3 class="text-white h5 mb-2">Interactive Tour</h3>
                <p class="text-white-50 small">Take a guided walkthrough</p>
                <button onclick="startGlobalTour()" class="btn-support bg-transparent border-0 cursor-pointer">Start Tour</button>
            </div>

            <div class="support-card">
                <div class="support-icon warning"><i class="fas fa-envelope"></i></div>
                <h3 class="text-white h5 mb-2">Contact Support</h3>
                <p class="text-white-50 small">Get in touch with our team</p>
                <a href="#contact" class="btn-support">Contact Us</a>
            </div>
        </div>
    </div>

    <!-- User Guide Section -->
    <div class="glass-card" id="user-guide" data-aos="fade-up">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-white mb-0"><i class="fas fa-book-open text-primary me-2"></i> User Guide</h2>
            <button class="btn btn-outline-light btn-sm rounded-pill" onclick="startGlobalTour()">
                <i class="fas fa-play me-2"></i> Start Site Tour
            </button>
        </div>
        
        <div class="guide-tabs">
            <button class="guide-tab active" onclick="switchTab('registration')">Registration</button>
            <button class="guide-tab" onclick="switchTab('appointments')">Appointments</button>
            <button class="guide-tab" onclick="switchTab('records')">Health Records</button>
            <button class="guide-tab" onclick="switchTab('portal')">Resident Portal</button>
        </div>

        <!-- Registration Guide -->
        <div id="registration" class="guide-content active">
            <div class="step-item">
                <div class="step-number">1</div>
                <div class="step-text">
                    <h4>Create an Account</h4>
                    <p>Navigate to the Resident Portal login page and click "Register". Fill in your personal details including Full Name, Email, and Mobile Number.</p>
                </div>
            </div>
            <div class="step-item">
                <div class="step-number">2</div>
                <div class="step-text">
                    <h4>Verify Your Identity</h4>
                    <p>Upload a valid ID or Barangay Clearance to verify your residency in Barangay Bacong. This ensures that our health services are prioritized for residents.</p>
                </div>
            </div>
            <div class="step-item">
                <div class="step-number">3</div>
                <div class="step-text">
                    <h4>Wait for Approval</h4>
                    <p>Your account will be reviewed by a Barangay Health Worker. You will receive an email notification once your account is activated.</p>
                </div>
            </div>
        </div>

        <!-- Appointments Guide -->
        <div id="appointments" class="guide-content">
            <div class="step-item">
                <div class="step-number">1</div>
                <div class="step-text">
                    <h4>Log In to Portal</h4>
                    <p>Access your Resident Portal using your email and password.</p>
                </div>
            </div>
            <div class="step-item">
                <div class="step-number">2</div>
                <div class="step-text">
                    <h4>Book Appointment</h4>
                    <p>Go to the "Appointments" tab and click "Book New". Select your preferred date, time, and reason for visit (e.g., Checkup, Vaccination).</p>
                </div>
            </div>
            <div class="step-item">
                <div class="step-number">3</div>
                <div class="step-text">
                    <h4>Receive Confirmation</h4>
                    <p>You will see your appointment status change to "Confirmed" once approved by the health center staff. You'll also get an SMS reminder.</p>
                </div>
            </div>
        </div>

        <!-- Health Records Guide -->
        <div id="records" class="guide-content">
            <div class="step-item">
                <div class="step-number">1</div>
                <div class="step-text">
                    <h4>View Your Dashboard</h4>
                    <p>Upon logging in, your dashboard shows a summary of your recent visits and upcoming schedules.</p>
                </div>
            </div>
            <div class="step-item">
                <div class="step-number">2</div>
                <div class="step-text">
                    <h4>Access Medical History</h4>
                    <p>Click on "My Records" to view your vaccination history, blood pressure readings, and past consultation notes securely.</p>
                </div>
            </div>
        </div>

        <!-- Portal Guide -->
        <div id="portal" class="guide-content">
            <div class="step-item">
                <div class="step-number">1</div>
                <div class="step-text">
                    <h4>Update Profile</h4>
                    <p>Keep your contact information and emergency contacts up to date in the "Profile" section.</p>
                </div>
            </div>
            <div class="step-item">
                <div class="step-number">2</div>
                <div class="step-text">
                    <h4>Chat with Gabby</h4>
                    <p>Use the "Ask Gabby" feature in the bottom corner for instant answers to common health questions.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="glass-card" id="faq" data-aos="fade-up" data-aos-delay="100">
        <h2 class="text-white mb-4"><i class="fas fa-comments text-primary me-2"></i> Frequently Asked Questions</h2>
        
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
                On the login page, click "Forgot Password". Enter your registered email address and you'll receive a password reset link.
            </div>
        </div>

        <div class="faq-item" onclick="toggleFAQ(this)">
            <div class="faq-question">
                <span>Is my patient data secure?</span>
                <i class="fas fa-chevron-down toggle-icon"></i>
            </div>
            <div class="faq-answer">
                Absolutely! E-BHM Connect uses industry-standard encryption and strict confidentiality protocols compliant with the Data Privacy Act.
            </div>
        </div>
    </div>

    <!-- Contact Support -->
    <div class="glass-card" id="contact" data-aos="fade-up" data-aos-delay="200">
        <h2 class="text-white mb-4"><i class="fas fa-phone-alt text-primary me-2"></i> Still need help?</h2>
        <div class="support-grid">
            <div class="support-card bg-transparent border-0 text-start ps-0">
                <h3 class="text-white h5">Email Support</h3>
                <a href="mailto:support@ebhm-connect.ph" class="text-primary text-decoration-none">support@ebhm-connect.ph</a>
                <p class="text-white-50 small mt-1">Response time: 24-48 hours</p>
            </div>
            <div class="support-card bg-transparent border-0 text-start ps-0">
                <h3 class="text-white h5">Phone Support</h3>
                <a href="tel:+639123456789" class="text-primary text-decoration-none">+63 912 345 6789</a>
                <p class="text-white-50 small mt-1">Mon-Fri: 8:00 AM - 5:00 PM</p>
            </div>
        </div>
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

function switchTab(tabId) {
    // Update tabs
    document.querySelectorAll('.guide-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    event.currentTarget.classList.add('active');

    // Update content
    document.querySelectorAll('.guide-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById(tabId).classList.add('active');
}
</script>

<?php include_once __DIR__ . '/../../includes/footer_public.php'; ?>
