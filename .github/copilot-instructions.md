# E-BHM Connect Agent Instructions

> **Stack**: Vanilla PHP 8.0 | MySQL/PDO | Bootstrap 5 | Glassmorphism CSS

You are an expert **PHP Backend Developer** paired with a **UI/UX Engineer** working on the E-BHM Connect healthcare management system. This is a **vanilla PHP** project without frameworks.

---

## 1. Tech Stack Compliance

### PHP Standards
```php
// ✅ ALWAYS use PDO with prepared statements
$stmt = $pdo->prepare('SELECT * FROM patients WHERE patient_id = :id');
$stmt->execute([':id' => $id]);

// ❌ NEVER use raw queries
$pdo->query("SELECT * FROM patients WHERE patient_id = $id"); // SQL INJECTION!

// ✅ Use require_once with __DIR__ for includes
require_once __DIR__ . '/../config/database.php';

// ❌ NEVER use relative paths without __DIR__
require_once '../config/database.php'; // UNRELIABLE!
```

### Security Patterns (MANDATORY)
```php
// Every action file MUST include:
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security_helper.php';

// POST handlers MUST validate CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();  // Dies with error if invalid
}

// Output MUST be escaped for XSS
echo h($user_input);           // For HTML context
echo js($user_input);          // For JavaScript context
echo htmlspecialchars($data);  // Alternative for HTML
```

### Database Access Pattern
```php
// Global $pdo is available from config/database.php
global $pdo;

// Or use the helper function
$pdo = get_db();

// Always wrap in try-catch for production
try {
    $stmt = $pdo->prepare('...');
    $stmt->execute([...]);
} catch (Throwable $e) {
    error_log('DB Error: ' . $e->getMessage());
    // User-friendly error message
}
```

---

## 2. Router Architecture

### Adding New Pages
1. Create file in `pages/admin/`, `pages/portal/`, or `pages/public/`
2. Register in `index.php` `$allowedPages` array:
```php
$allowedPages = [
    'my-new-page' => [
        'file' => $basePath . 'admin/my_new_page.php',
        'secure' => true  // true=BHW, 'superadmin', 'patient', false=public
    ],
];
```

### Adding New Actions
1. Create file in `actions/` folder
2. Register in `index.php` `$allowedActions` array:
```php
$allowedActions = [
    'my-action' => $actionPath . 'my_action.php',
];
```

### Form Submission Pattern
```php
<!-- Always use BASE_URL with action parameter -->
<form action="<?php echo BASE_URL; ?>?action=save-patient" method="POST">
    <?php echo csrf_input(); ?>  <!-- REQUIRED! -->
    <!-- form fields -->
</form>
```

---

## 3. Design System Compliance

### Read the Design Bible First!
Before writing CSS/HTML, cross-reference: `docs/design-system.md`

### Color Rules
| Element | Color | CSS Variable |
|---------|-------|--------------|
| All buttons | Primary green | `var(--primary)` |
| Delete only | Danger red | `var(--danger)` |
| Links/focus | Primary green | `var(--primary)` |
| Warnings | Amber | `var(--warning)` |

### Component Classes
```html
<!-- Glass card (primary container) -->
<div class="glass-card">
    <div class="glass-card-header">
        <h5 class="glass-card-title">Title</h5>
    </div>
    <div class="glass-card-body">Content</div>
</div>

<!-- Buttons -->
<button class="btn btn-primary">Save</button>      <!-- Primary actions -->
<button class="btn btn-glass">Cancel</button>      <!-- Secondary actions -->
<button class="btn btn-danger">Delete</button>     <!-- Destructive ONLY -->

<!-- Status badges -->
<span class="badge badge-active">Active</span>
<span class="badge badge-pending">Pending</span>
<span class="badge badge-inactive">Inactive</span>
```

### ❌ Do NOT:
- Use Tailwind CSS (this project uses Bootstrap 5 + custom CSS)
- Mix button colors arbitrarily
- Use inline styles for colors (use CSS variables)
- Create new color schemes without updating design-system.md

---

## 4. Admin Function Context

### Role Hierarchy
```
superadmin → Full system access
  ↓
admin → Most admin features (no audit/backup/roles)
  ↓
bhw → Standard BHW features
```

### Permission Checking
```php
// Check before rendering admin-only features
if (is_admin()) {
    // Show admin controls
}

if (is_superadmin()) {
    // Show superadmin-only features
}

// In action files, enforce permissions
require_superadmin();  // Redirects if not superadmin
require_admin($redirect_url);
require_permission('manage_inventory');
```

### Audit Logging (REQUIRED for data changes)
```php
// Log after successful operations
log_audit(
    'create_patient',           // action
    'patient',                   // entity_type
    $new_patient_id,            // entity_id
    ['name' => $patient_name]   // optional details
);
```

### Session Variables
```php
// BHW session (set on login)
$_SESSION['bhw_id']         // User ID
$_SESSION['bhw_full_name']  // Display name
$_SESSION['bhw_email']      // Email
$_SESSION['bhw_role']       // 'bhw', 'admin', 'superadmin'

// Patient session
$_SESSION['patient_id']
$_SESSION['patient_user_id']
$_SESSION['patient_full_name']
```

---

## 5. Debugging Workflow

### Common Issues & Solutions

| Symptom | Check | Solution |
|---------|-------|----------|
| 404 on page | `$allowedPages` in index.php | Add page to whitelist |
| CSRF error | Form has `csrf_input()` | Add `<?php echo csrf_input(); ?>` |
| Rate limited | `rate_limits` table | Clear: `clear_rate_limit('login_bhw', $ip)` |
| DB error | PDO exception | Check `error_log`, verify SQL syntax |
| Permission denied | Session role | Check `$_SESSION['bhw_role']` |
| Login loop | `account_status` column | Must be 'approved' |

### Debug Tools
```php
// Temporary debug output (remove before commit!)
error_log(print_r($variable, true));

// Check session state
var_dump($_SESSION); exit;

// Database connection test
require_once 'config/db_test.php';

// Schema verification
php check_schema.php
```

### Migration Workflow
```bash
# Apply pending migrations
php run_migrations.php

# Create new migration
# Filename format: YYYY-MM-DD_description.sql
# Place in migrations/ folder
```

---

## 6. Quick Reference

### File Locations
| Need | Location |
|------|----------|
| Security functions | `includes/security_helper.php` |
| Auth/RBAC functions | `includes/auth_helpers.php` |
| Email functions | `includes/email_helper.php` |
| Translations | `includes/translation_helper.php` |
| Admin CSS | `assets/css/admin.css` |
| Glass components | `assets/css/glass-components.css` |
| Design rules | `docs/design-system.md` |

### Common Includes (Admin Page)
```php
<?php
include __DIR__ . '/../../includes/header_admin.php';
// Page content
include __DIR__ . '/../../includes/footer_admin.php';
?>
```

### SweetAlert2 Pattern
```javascript
Swal.fire({
    icon: 'success',
    title: 'Saved!',
    text: 'Record has been saved.',
    confirmButtonColor: '#20c997'
});
```

---

## 8. SMS Gateway Integration

### Sending SMS Messages
```php
require_once __DIR__ . '/../includes/sms_helper.php';

// Queue a single SMS
queue_sms($phone_number, $message);

// Send broadcast to all patients
$patients = $pdo->query("SELECT contact FROM patients WHERE contact IS NOT NULL")->fetchAll();
foreach ($patients as $p) {
    queue_sms($p['contact'], $broadcast_message);
}
```

### Phone Number Format
```php
// Philippine format validation (from security_helper.php)
$phone = validate_phone($input); // Returns formatted or false

// Valid formats: 09171234567, +639171234567
// Stored as: +639171234567
```

### SMS Queue Processing
```bash
# Process pending SMS queue (run via cron or manually)
php process_queue.php
```

---

## 9. Chatbot (Gemini API) Integration

### API Endpoint Structure
```php
// actions/chatbot_api.php handles all chatbot requests
// POST JSON: { "message": "user query" }
// Response JSON: { "reply": "bot response" }
```

### Context Prompts
```php
// BHW context (logged in admin)
if (isset($_SESSION['bhw_id'])) {
    $prompt = "You are speaking to a Barangay Health Worker...";
}

// Public context (anonymous)
else {
    $prompt = "You are 'Gabby', a health assistant for Barangay Bacong...";
}
```

### API Key Configuration
```php
// config/secrets.php
define('GEMINI_API_KEY', 'your-api-key-here');

// Model endpoint
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
```

### Frontend Integration
```javascript
// Chat sends via fetch to ?action=chatbot-api
// Response is parsed with marked.js for markdown
messageDiv.innerHTML = marked.parse(botReply);
```

---

## 10. PDF Report Generation (FPDF)

### Basic Report Pattern
```php
require_once __DIR__ . '/../lib/fpdf/fpdf.php';

// Create PDF instance
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Header
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'E-BHM Connect Report', 0, 1, 'C');

// Table
$pdf->SetFont('Arial', '', 12);
foreach ($data as $row) {
    $pdf->Cell(60, 8, $row['name'], 1);
    $pdf->Cell(40, 8, $row['value'], 1);
    $pdf->Ln();
}

// Output
$pdf->Output('D', 'report.pdf'); // D = Download, I = Inline
exit();
```

### Report Action Naming
```php
// actions/report_*.php naming convention
// Example files:
// - report_patient_list.php
// - report_health_records.php
// - report_bhw_activity.php
```

### Common FPDF Methods
```php
$pdf->SetFont('Arial', 'B', 14);    // Font: family, style, size
$pdf->Cell(width, height, text);    // Single cell
$pdf->MultiCell(width, height, text); // Wrapped text
$pdf->Ln();                          // Line break
$pdf->Image(path, x, y, w, h);      // Image
```

---

## 11. Git Workflow & Conventions

### Branch Naming
```
main           - Production-ready code
develop        - Integration branch
feature/XXX    - New features (e.g., feature/patient-portal)
bugfix/XXX     - Bug fixes (e.g., bugfix/csrf-validation)
hotfix/XXX     - Urgent production fixes
```

### Commit Message Format
```
type(scope): description

[optional body]

[optional footer]
```

**Types**:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation only
- `style`: Formatting, no code change
- `refactor`: Code restructuring
- `security`: Security improvements
- `perf`: Performance improvements

**Examples**:
```
feat(announcements): add delete functionality with CSRF validation

fix(chatbot): add markdown styling for bot messages

docs(agent-rules): add SMS and PDF generation patterns

security(auth): add rate limiting to login actions
```

### Pre-Commit Checklist
```bash
# 1. Check for PHP syntax errors
php -l actions/my_file.php

# 2. Ensure no secrets in code
git diff --staged | grep -i "api_key\|password\|secret"

# 3. Run migrations if schema changed
php run_migrations.php
```

### .gitignore Essentials
```
config/secrets.php
vendor/
*.log
uploads/*
!uploads/.gitkeep
```

---

## 12. Code Review Checklist

Before committing, verify:

- [ ] PDO prepared statements used (no raw SQL)
- [ ] CSRF protection on all POST forms
- [ ] Output escaped with `h()` or `htmlspecialchars()`
- [ ] Audit logging for data modifications
- [ ] Permission checks for admin functions
- [ ] Mobile-responsive (test at 375px width)
- [ ] Design system colors used (no arbitrary colors)
- [ ] Error handling with try-catch
- [ ] No hardcoded credentials or API keys

---

**Version**: 1.1.0  
**Last Updated**: 2026-01-05  
**Maintainer**: JeffDev Studio