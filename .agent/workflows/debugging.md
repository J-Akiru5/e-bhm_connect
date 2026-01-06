---
description: Debugging common issues in e-bhm_connect PHP healthcare system
---

# E-BHM Connect Debugging Workflow

## Quick Diagnosis Matrix

| Symptom | Likely Cause | Check First |
|---------|--------------|-------------|
| 404 on page | Not in whitelist | `index.php` → `$allowedPages` |
| Action does nothing | Not in whitelist | `index.php` → `$allowedActions` |
| CSRF validation failed | Missing token | Form has `<?php echo csrf_input(); ?>` |
| "Too many attempts" | Rate limited | `rate_limits` table |
| Login redirect loop | Account status | `bhw_users.account_status` must be 'approved' |
| Permission denied | Role insufficient | Check `$_SESSION['bhw_role']` value |
| DB error | Query issue | Check PHP error log |
| Email not sending | Resend config | `config/secrets.php` → `RESEND_API_KEY` |

---

## Step-by-Step Debugging

### 1. Check Error Logs First
```bash
# XAMPP error log location
C:\xampp\apache\logs\error.log

# Or tail PHP errors in real-time (if configured)
tail -f C:\xampp\php\logs\php_error_log
```

### 2. Database Connection Issues
```php
// Test connection directly
// turbo
php config/db_test.php
```

### 3. CSRF Token Issues

**Symptoms**: Form submits but nothing happens, or "Invalid CSRF token" error

**Check**:
1. Form includes `<?php echo csrf_input(); ?>`
2. Action file has `require_csrf();` after POST check
3. Session hasn't expired

**Quick Fix**:
```php
// In action file, add debug before require_csrf()
error_log('CSRF Session: ' . ($_SESSION['csrf_token'] ?? 'NONE'));
error_log('CSRF POST: ' . ($_POST['csrf_token'] ?? 'NONE'));
require_csrf();
```

### 4. Rate Limiting Issues

**Symptoms**: "Too many failed login attempts"

**Clear rate limit**:
```sql
DELETE FROM rate_limits WHERE action = 'login_bhw' AND identifier = 'YOUR_IP';
```

**Or via PHP**:
```php
clear_rate_limit('login_bhw', get_client_ip());
```

### 5. Session/Auth Issues

**Debug current session**:
```php
// Add temporarily to any page
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
exit;
```

**Expected BHW session keys**:
- `bhw_id` - User ID
- `bhw_full_name` - Display name
- `bhw_role` - 'bhw', 'admin', or 'superadmin'
- `bhw_logged_in` - boolean

### 6. Permission Issues

**Check permission requirements**:
```php
// What does this permission check?
if (has_permission('manage_inventory')) { ... }

// Check user's role
echo $_SESSION['bhw_role']; // Should be 'admin' or 'superadmin'
```

---

## Common Fixes

### Add New Page
1. Create `pages/admin/my_page.php`
2. Add to `index.php`:
```php
'admin-my-page' => ['file' => $basePath . 'admin/my_page.php', 'secure' => true],
```

### Add New Action
1. Create `actions/my_action.php`
2. Add to `index.php`:
```php
'my-action' => $actionPath . 'my_action.php',
```

### Run Database Migrations
```bash
# turbo
php run_migrations.php
```

### Check Schema
```bash
# turbo
php check_schema.php
```

---

## External Service Debugging

### Gemini API (Chatbot)
- Check `GEMINI_API_KEY` in `config/secrets.php`
- Test endpoint: `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash`
- Error logs for cURL issues

### Resend (Email)
- Verify domain at [resend.com/domains](https://resend.com/domains)
- Check `RESEND_API_KEY` and `RESEND_FROM_EMAIL` in `config/secrets.php`
- View logs at [resend.com/emails](https://resend.com/emails)

### SMS Gateway
- Check `GATEWAY_TOKEN` in `config/secrets.php`
- Review `sms_queue` table for failed messages
- Check `process_queue.php` for queue processing

---

## Clean Start

If all else fails:
```sql
-- Clear sessions (forces re-login)
TRUNCATE TABLE sessions;

-- Clear rate limits
TRUNCATE TABLE rate_limits;

-- Check for schema issues
-- turbo
php run_migrations.php
```
