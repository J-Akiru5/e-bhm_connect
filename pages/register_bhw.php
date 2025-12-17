<?php
// Public BHW Registration Page - Glassmorphism Design with Legal Modal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BHW Registration - E-BHM Connect</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Poppins Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    
    <style>
        /* Full-Screen Glassmorphism Auth Layout */
        .auth-fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
            padding: 24px;
        }
        
        /* Animated Floating Orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
            animation: float 20s ease-in-out infinite;
            pointer-events: none;
        }
        
        .orb-1 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(32, 201, 151, 0.8) 0%, transparent 70%);
            top: -10%;
            left: -10%;
        }
        
        .orb-2 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.7) 0%, transparent 70%);
            bottom: -15%;
            right: -15%;
            animation-delay: -5s;
        }
        
        .orb-3 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.5) 0%, transparent 70%);
            top: 50%;
            right: 20%;
            animation-delay: -10s;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }
        
        /* Glass Card Container */
        .auth-glass-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 580px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.20);
            animation: fadeInUp 0.6s ease-out;
            margin: 24px 0;
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Logo & Header */
        .auth-glass-card .logo-container {
            text-align: center;
            margin-bottom: 24px;
        }
        
        .auth-glass-card .logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
            margin-bottom: 12px;
            filter: drop-shadow(0 4px 16px rgba(32, 201, 151, 0.35));
        }
        
        .auth-glass-card h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
            text-align: center;
        }
        
        .auth-glass-card .subtitle {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            margin-bottom: 24px;
        }
        
        .badge-bhw {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, rgba(32, 201, 151, 0.2), rgba(32, 201, 151, 0.1));
            border: 1px solid rgba(32, 201, 151, 0.3);
            color: #20c997;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        
        /* Form Styling */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-weight: 500;
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 8px;
        }
        
        .glass-input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: #ffffff;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            transition: all 250ms ease;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        
        .glass-input:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.25);
        }
        
        .glass-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(32, 201, 151, 0.8);
            box-shadow: 0 0 0 4px rgba(32, 201, 151, 0.15);
            padding: 11px 15px;
        }
        
        .glass-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        @media (max-width: 576px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
        
        /* Primary Button */
        .btn-glass-primary {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, #20c997, #0f5132);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 250ms ease;
            box-shadow: 0 4px 16px rgba(32, 201, 151, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-glass-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(32, 201, 151, 0.45);
        }
        
        .btn-glass-primary:active {
            transform: translateY(0);
        }
        
        .btn-glass-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Footer Links */
        .auth-footer {
            margin-top: 24px;
            text-align: center;
        }
        
        .auth-footer p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
            margin-bottom: 16px;
        }
        
        .auth-footer a {
            color: #20c997;
            text-decoration: none;
            font-weight: 600;
            transition: all 250ms ease;
        }
        
        .auth-footer a:hover {
            color: #ffffff;
            text-decoration: underline;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 250ms ease;
        }
        
        .back-link:hover {
            color: #ffffff;
        }
        
        /* Workflow Steps */
        .workflow-steps {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
        }
        
        .workflow-steps h4 {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 12px;
        }
        
        .step {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
        }
        
        .step:not(:last-child) {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .step-number {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.6);
        }
        
        .step.active .step-number {
            background: linear-gradient(135deg, #20c997, #0f5132);
            color: #ffffff;
        }
        
        .step-text {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .step.active .step-text {
            color: #20c997;
            font-weight: 500;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .auth-glass-card {
                padding: 32px 24px;
                border-radius: 16px;
            }
            
            .auth-glass-card h2 {
                font-size: 1.5rem;
            }
            
            .orb-1, .orb-2, .orb-3 {
                filter: blur(60px);
            }
        }
        
        @media (max-width: 480px) {
            .auth-fullscreen {
                padding: 16px;
            }
            
            .auth-glass-card {
                padding: 24px 20px;
            }
            
            .auth-glass-card h2 {
                font-size: 1.35rem;
            }
        }
        
        /* Legal Modal Styles */
        .legal-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 24px;
        }
        
        .legal-modal {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 32px 80px rgba(0, 0, 0, 0.4);
            animation: modalSlideIn 0.4s ease-out;
        }
        
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .legal-modal-header {
            padding: 32px 32px 0;
            text-align: center;
        }
        
        .legal-modal-header .warning-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(239, 68, 68, 0.1));
            border: 2px solid rgba(239, 68, 68, 0.3);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        
        .legal-modal-header h3 {
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .legal-modal-header p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }
        
        .legal-modal-body {
            padding: 24px 32px;
        }
        
        .legal-section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }
        
        .legal-section h4 {
            color: #ef4444;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .legal-section p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0;
        }
        
        .legal-modal-footer {
            padding: 0 32px 32px;
        }
        
        .checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
            cursor: pointer;
        }
        
        .checkbox-container input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: #20c997;
            flex-shrink: 0;
            margin-top: 2px;
        }
        
        .checkbox-container label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            line-height: 1.5;
            cursor: pointer;
        }
        
        .modal-buttons {
            display: flex;
            gap: 12px;
        }
        
        .btn-decline {
            flex: 1;
            padding: 14px 24px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            font-weight: 500;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 250ms ease;
        }
        
        .btn-decline:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }
        
        .btn-accept {
            flex: 1;
            padding: 14px 24px;
            background: linear-gradient(135deg, #20c997, #0f5132);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 250ms ease;
            box-shadow: 0 4px 16px rgba(32, 201, 151, 0.35);
        }
        
        .btn-accept:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(32, 201, 151, 0.45);
        }
        
        .btn-accept:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Hide form initially */
        .registration-form {
            display: none;
        }
        
        .registration-form.active {
            display: block;
        }
    </style>
</head>
<body>

<?php
// Retrieve form data if available (on validation error)
$form_data = isset($_SESSION['register_form_data']) ? $_SESSION['register_form_data'] : [];
$full_name = isset($form_data['full_name']) ? htmlspecialchars($form_data['full_name']) : '';
$username = isset($form_data['username']) ? htmlspecialchars($form_data['username']) : '';
$email = isset($form_data['email']) ? htmlspecialchars($form_data['email']) : '';
$bhw_unique_id = isset($form_data['bhw_unique_id']) ? htmlspecialchars($form_data['bhw_unique_id']) : '';

// Show register_error via SweetAlert2 if present
if (isset($_SESSION['register_error'])) {
    $msg = json_encode($_SESSION['register_error']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'error', title: 'Registration Failed', text: $msg, background: '#1e293b', color: '#fff'}); } });</script>";
    unset($_SESSION['register_error']);
}

// Clear form data after displaying (one-time use)
if (isset($_SESSION['register_form_data'])) {
    unset($_SESSION['register_form_data']);
}
?>

<!-- Legal Acknowledgment Modal -->
<div class="legal-modal-overlay" id="legalModal">
    <div class="legal-modal">
        <div class="legal-modal-header">
            <div class="warning-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <h3>Important Notice</h3>
            <p>Please read and acknowledge before proceeding</p>
        </div>
        
        <div class="legal-modal-body">
            <div class="legal-section">
                <h4>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    Authorized Personnel Only
                </h4>
                <p>By proceeding, you attest under penalty of law that you are a duly appointed and active Barangay Health Worker (BHW). Unauthorized access to this system is strictly prohibited.</p>
            </div>
            
            <div class="legal-section">
                <h4>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    Data Privacy Act of 2012 (R.A. 10173)
                </h4>
                <p>You explicitly agree to uphold the confidentiality and integrity of all patient data. You understand that sensitive personal health information is protected by law. Any unauthorized disclosure, sharing, or mishandling of data is a criminal offense punishable by imprisonment and fines.</p>
            </div>
            
            <div class="legal-section">
                <h4>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    Fraud and Liability Warning
                </h4>
                <p>This system logs all access attempts and IP addresses. If you are found to be impersonating a health worker or falsifying credentials, legal actions will be filed against you immediately. Your account requires manual approval from the Head of the Health Center.</p>
            </div>
            
            <div class="legal-section">
                <h4>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 11 12 14 22 4"></polyline>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                    Approval Workflow
                </h4>
                <p><strong>Step 1:</strong> Register and provide credentials.<br>
                <strong>Step 2:</strong> Verify your email address via the link sent to you.<br>
                <strong>Step 3:</strong> Wait for the Super Admin (Health Head) to validate your employment status.<br>
                <strong>Step 4:</strong> Access granted only upon 'Approved' status.</p>
            </div>
        </div>
        
        <div class="legal-modal-footer">
            <div class="checkbox-container">
                <input type="checkbox" id="agreeCheckbox" onchange="toggleAcceptButton()">
                <label for="agreeCheckbox">
                    <strong>By registering, I attest under penalty of law (Data Privacy Act of 2012, R.A. 10173) that I am an authorized Barangay Health Worker.</strong> I understand that providing false information is a criminal offense.
                </label>
            </div>
            
            <div class="modal-buttons">
                <button class="btn-decline" onclick="declineTerms()">Decline</button>
                <button class="btn-accept" id="acceptBtn" disabled onclick="acceptTerms()">I Understand & Accept</button>
            </div>
        </div>
    </div>
</div>

<!-- Full-Screen Glassmorphism Layout -->
<div class="auth-fullscreen">
    <!-- Animated Floating Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    
    <!-- Glass Card Container -->
    <div class="auth-glass-card">
        <!-- Logo & Header -->
        <div class="logo-container">
            <a href="<?php echo BASE_URL; ?>?page=home">
                <img src="<?php echo BASE_URL; ?>assets/images/e-logo.png" alt="E-BHM Connect" class="logo">
            </a>
            <span class="badge-bhw">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <path d="M20 8v6"></path>
                    <path d="M23 11h-6"></path>
                </svg>
                Health Worker Registration
            </span>
            <h2>BHW Registration</h2>
            <p class="subtitle">Create your account to access the health management system</p>
        </div>

        <!-- Workflow Steps -->
        <div class="workflow-steps">
            <h4>Registration Process</h4>
            <div class="step active">
                <span class="step-number">1</span>
                <span class="step-text">Complete Registration Form</span>
            </div>
            <div class="step">
                <span class="step-number">2</span>
                <span class="step-text">Verify Email Address</span>
            </div>
            <div class="step">
                <span class="step-number">3</span>
                <span class="step-text">Await Admin Approval</span>
            </div>
        </div>

        <!-- Registration Form (Hidden until legal accepted) -->
        <div class="registration-form" id="registrationForm">
            <form action="<?php echo BASE_URL; ?>?action=register-bhw" method="POST">
                <div class="form-group">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input 
                        type="text" 
                        class="glass-input" 
                        id="full_name" 
                        name="full_name" 
                        value="<?php echo $full_name; ?>"
                        placeholder="Enter your full name"
                        required
                    >
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input 
                            type="text" 
                            class="glass-input" 
                            id="username" 
                            name="username" 
                            value="<?php echo $username; ?>"
                            placeholder="Choose a username"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input 
                            type="email" 
                            class="glass-input" 
                            id="email" 
                            name="email" 
                            value="<?php echo $email; ?>"
                            placeholder="your.email@example.com"
                            required
                        >
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input 
                            type="password" 
                            class="glass-input" 
                            id="password" 
                            name="password" 
                            placeholder="Create a password"
                            required
                            minlength="8"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm" class="form-label">Confirm Password</label>
                        <input 
                            type="password" 
                            class="glass-input" 
                            id="password_confirm" 
                            name="password_confirm" 
                            placeholder="Confirm your password"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="bhw_unique_id" class="form-label">BHW ID Number</label>
                    <input 
                        type="text" 
                        class="glass-input" 
                        id="bhw_unique_id" 
                        name="bhw_unique_id" 
                        value="<?php echo $bhw_unique_id; ?>"
                        placeholder="Enter your official BHW ID"
                        required
                    >
                </div>

                <button type="submit" class="btn-glass-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                    Register Account
                </button>
            </form>
        </div>

        <!-- Footer Links -->
        <div class="auth-footer">
            <p>Already have an account? <a href="<?php echo BASE_URL; ?>login-bhw">Login here</a></p>
            <a href="<?php echo BASE_URL; ?>?page=home" class="back-link">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to Home
            </a>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
    function toggleAcceptButton() {
        const checkbox = document.getElementById('agreeCheckbox');
        const acceptBtn = document.getElementById('acceptBtn');
        acceptBtn.disabled = !checkbox.checked;
    }
    
    function acceptTerms() {
        const modal = document.getElementById('legalModal');
        const form = document.getElementById('registrationForm');
        
        modal.style.animation = 'modalSlideOut 0.3s ease-in forwards';
        
        setTimeout(() => {
            modal.style.display = 'none';
            form.classList.add('active');
        }, 300);
    }
    
    function declineTerms() {
        window.location.href = '<?php echo BASE_URL; ?>?page=home';
    }
    
    // Auto-accept terms if form data is present (returning from validation error)
    <?php if (!empty($full_name) || !empty($username) || !empty($email) || !empty($bhw_unique_id)): ?>
    window.addEventListener('load', function() {
        const modal = document.getElementById('legalModal');
        const form = document.getElementById('registrationForm');
        modal.style.display = 'none';
        form.classList.add('active');
    });
    <?php endif; ?>
    
    // Add slide out animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes modalSlideOut {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(-50px) scale(0.95); }
        }
    `;
    document.head.appendChild(style);
</script>

</body>
</html>
