# Resend Email Setup Guide

This guide explains how to configure email verification for E-BHM Connect.

## Prerequisites

1. A [Resend](https://resend.com) account
2. A verified domain (or use sandbox for testing)

---

## Step 1: Get Your API Key

1. Log in to [Resend Dashboard](https://resend.com/api-keys)
2. Click **Create API Key**
3. Name it (e.g., "E-BHM Connect Production")
4. Copy the key (you won't see it again)

---

## Step 2: Verify Your Domain

For production emails (not going to spam):

1. Go to [Resend Domains](https://resend.com/domains)
2. Click **Add Domain**
3. Enter your domain (e.g., `mail.yourdomain.com`)
4. Add the DNS records Resend provides to your domain registrar
5. Wait for verification (usually 5-30 minutes)

---

## Step 3: Configure secrets.php

Edit `config/secrets.php`:

```php
<?php
// Resend API Configuration
define('RESEND_API_KEY', 're_xxxxxxxxxxxxxxxxxxxxx'); // Your API key
define('RESEND_FROM_EMAIL', 'noreply@yourdomain.com'); // Must be verified domain

// Other secrets...
define('GEMINI_API_KEY', 'your-gemini-key');
define('GATEWAY_TOKEN', 'your-sms-gateway-token');
```

> **Important**: `RESEND_FROM_EMAIL` must use your verified domain, not `onboarding@resend.dev`.

---

## Step 4: Test Email Sending

1. Register a new BHW account at `/register-bhw`
2. Check the email inbox
3. If email doesn't arrive:
   - Check [Resend Email Logs](https://resend.com/emails)
   - Verify PHP error log for exceptions
   - Ensure domain is verified

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| "Invalid API key" | Regenerate key in Resend dashboard |
| Emails going to spam | Verify domain and add SPF/DKIM records |
| "Domain not verified" | Complete domain verification in Resend |
| No error but no email | Check Resend email logs for status |

---

## Sandbox Mode (Testing Only)

For testing without domain verification:

```php
define('RESEND_FROM_EMAIL', 'onboarding@resend.dev');
```

> **Note**: Sandbox emails only work with the email address on your Resend account.

---

**Last Updated**: 2026-01-05
