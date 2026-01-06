<?php
// includes/email_helper.php
// Email helper functions using Resend API

require_once __DIR__ . '/../config/config.php';

// Note: The Resend class is in the global namespace (loaded via Composer autoload)
// Use \Resend::client() to access it

/**
 * Send approval notification email to BHW user
 * 
 * @param string $email Recipient email address
 * @param string $name Recipient full name
 * @return bool Success status
 */
function sendBhwApprovalEmail(string $email, string $name): bool {
    try {
        $resend = \Resend::client(RESEND_API_KEY);
        $loginUrl = BASE_URL . "login-bhw";
        
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; margin: 0; padding: 40px 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 16px; overflow: hidden; box-shadow: 0 24px 64px rgba(0, 0, 0, 0.2);">
        
        <!-- Header -->
        <div style="padding: 40px 40px 20px; text-align: center;">
            <h1 style="color: #20c997; margin: 0; font-size: 28px;">E-BHM Connect</h1>
            <p style="color: rgba(255,255,255,0.7); margin: 8px 0 0; font-size: 14px;">Barangay Health Management System</p>
        </div>
        
        <!-- Content -->
        <div style="padding: 20px 40px 40px;">
            <div style="background: rgba(32, 201, 151, 0.15); border: 1px solid rgba(32, 201, 151, 0.3); border-radius: 12px; padding: 32px; text-align: center;">
                <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #20c997, #0f5132); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                    <span style="color: white; font-size: 32px;">✓</span>
                </div>
                <h2 style="color: #20c997; margin: 0 0 16px; font-size: 24px;">Account Approved!</h2>
                <p style="color: rgba(255,255,255,0.8); margin: 0 0 24px; font-size: 16px; line-height: 1.6;">
                    Congratulations, {$name}! Your BHW account has been approved by the Healthcare Center Head. You can now access the E-BHM Connect system.
                </p>
                
                <a href="{$loginUrl}" style="display: inline-block; background: linear-gradient(135deg, #20c997, #0f5132); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 12px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 16px rgba(32, 201, 151, 0.35);">
                    Login to Dashboard
                </a>
            </div>
        </div>
        
        <!-- Footer -->
        <div style="padding: 20px 40px; background: rgba(0,0,0,0.2); text-align: center;">
            <p style="color: rgba(255,255,255,0.4); margin: 0; font-size: 12px;">
                Welcome to the E-BHM Connect team!
            </p>
        </div>
    </div>
</body>
</html>
HTML;

        $resend->emails->send([
            'from' => 'E-BHM Connect <' . RESEND_FROM_EMAIL . '>',
            'to' => [$email],
            'subject' => 'Your E-BHM Connect Account Has Been Approved!',
            'html' => $html
        ]);
        
        return true;
    } catch (Exception $e) {
        error_log('Resend email error: ' . $e->getMessage());
        return false;
    }
}
