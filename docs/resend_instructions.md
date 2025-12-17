# Resend Email Integration Guide

## Installation
```bash
composer require resend/resend-php
```

## Configuration

Add your Resend API key to `config/secrets.php`:
```php
if (!defined('RESEND_API_KEY')) {
    define('RESEND_API_KEY', 're_your_api_key_here');
}
```

## Basic Usage

### Send Email
```php
// The Resend class is in the global namespace (loaded via Composer autoload)
// Use \Resend::client() to access it

$resend = \Resend::client('re_your_api_key');

$resend->emails->send([
    'from' => 'E-BHM Connect <noreply@yourdomain.com>',
    'to' => ['recipient@example.com'],
    'subject' => 'Email Verification',
    'html' => '<p>Your verification link: <a href="...">Click here</a></p>'
]);
```

## Email Helper Function

Create `includes/email_helper.php`:
```php
<?php
require_once __DIR__ . '/../config/config.php'; // This loads the Composer autoloader

// Note: The Resend class is in the global namespace (not Resend\Resend)
// Use \Resend::client() to access it

function sendVerificationEmail(string $email, string $name, string $token): bool {
    try {
        $resend = \Resend::client(RESEND_API_KEY);
        $verifyUrl = BASE_URL . "verify-email?token=" . urlencode($token);
        
        $resend->emails->send([
            'from' => 'E-BHM Connect <noreply@yourdomain.com>',
            'to' => [$email],
            'subject' => 'Verify Your E-BHM Connect Account',
            'html' => "
                <h2>Welcome to E-BHM Connect, {$name}!</h2>
                <p>Please verify your email by clicking the link below:</p>
                <p><a href='{$verifyUrl}'>Verify Email Address</a></p>
                <p>This link expires in 24 hours.</p>
                <p>If you didn't register, please ignore this email.</p>
            "
        ]);
        return true;
    } catch (Exception $e) {
        error_log('Resend email error: ' . $e->getMessage());
        return false;
    }
}
```

## Important Notes

1. **Domain Verification**: You must verify your domain in Resend dashboard before sending from it
2. **API Key Security**: Never commit your API key to version control
3. **Rate Limits**: Resend has rate limits; implement queueing for bulk emails
4. **Testing**: Use `onboarding@resend.dev` as sender for testing without domain verification
