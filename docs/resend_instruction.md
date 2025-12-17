# Resend API Instructions for Vanilla PHP

We are using the `resend/resend-php` library via Composer in a Vanilla PHP environment.

## Integration Rules
1. Always include the autoloader: `require __DIR__ . '/../vendor/autoload.php';` (Adjust path relative to the file location).
2. Use a dedicated helper file: `lib/mailer.php`.
3. Function Signature: `function sendVerificationEmail($userEmail, $token)`
4. API Key: Do not hardcode the API Key. Use a defined constant `RESEND_API_KEY` from a `config.php` file.
5. HTML Body: Use a clean, professional HTML template for the email body.
6. Error Handling: Wrap the send request in a try-catch block and return `true` on success, `false` on failure.

## Verification Link Logic
- Base URL: `http://localhost/e-bhm_connect/`
- Endpoint: `verify.php`
- Parameter: `token` 