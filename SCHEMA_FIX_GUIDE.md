# Database Schema Fix Guide

## Problem Summary
When moving the E-BHM Connect project between computers, the database schemas may not match due to:
- Failed migrations with stored procedures
- Missing columns causing features to break
- Different MySQL/MariaDB versions

## Quick Fix Solution

### ✅ On the Computer WITH Issues:

#### Step 1: Run the Auto-Fix Script
```bash
cd C:\xampp\htdocs\e-bhm_connect
php fix_schema.php
```

**What it does:**
- ✓ Checks all required tables exist
- ✓ Adds missing columns automatically
- ✓ Creates necessary indexes
- ✓ Shows detailed report
- ✓ Safe to run multiple times

#### Expected Output:
```
✅ Schema validation complete! Database is ready.

Summary:
  ✓ Fixed/Added:  X
  ✓ Already OK:   X
  ✗ Errors:       0
```

#### Step 2: Verify Fixes
1. **Test Inventory:**
   - Go to: `http://localhost/e-bhm_connect/?page=admin-inventory`
   - Add a new item
   - **Should display** in the table immediately

2. **Test User Roles:**
   - Go to: `http://localhost/e-bhm_connect/?page=admin-user-roles`
   - Change a user's role
   - **Should show SweetAlert confirmation**
   - **Should save and show success modal**

---

## What Was Fixed

### 1. Inventory Display Issue
**Problem:** Items not showing after adding  
**Cause:** Missing columns: `category_id`, `batch_number`, `expiry_date`, `stock_alert_limit`  
**Fix:** Auto-added by `fix_schema.php`

### 2. User Role Permissions Not Saving
**Problem:** Role changes don't save  
**Cause:** Page expects columns that may not exist  
**Fix:** Schema validated, confirmation now uses SweetAlert

---

## Files Modified/Created

### New Files:
- ✅ `fix_schema.php` - Auto-repair script
- ✅ `SCHEMA_FIX_GUIDE.md` - This guide

### Modified Files:
- ✅ `pages/admin/user_roles.php` - Added SweetAlert confirmation
- ✅ `actions/user_role_update.php` - Added success modal trigger

---

## Alternative: Manual SQL Fix

If you prefer running SQL manually:

```sql
-- Add inventory columns
ALTER TABLE `medication_inventory` 
ADD COLUMN `category_id` INT(11) NULL DEFAULT NULL AFTER `item_id`,
ADD COLUMN `batch_number` VARCHAR(100) NULL DEFAULT NULL AFTER `category_id`,
ADD COLUMN `expiry_date` DATE NULL DEFAULT NULL AFTER `batch_number`,
ADD COLUMN `stock_alert_limit` INT(11) NOT NULL DEFAULT 10 AFTER `expiry_date`;

-- Add user columns (if missing)
ALTER TABLE `bhw_users` 
ADD COLUMN `last_login` DATETIME NULL DEFAULT NULL AFTER `created_at`;

-- Add indexes
ALTER TABLE `medication_inventory` ADD INDEX `idx_med_category` (`category_id`);
ALTER TABLE `bhw_users` ADD INDEX `idx_bhw_role` (`role`);
```

---

## For Future Migrations

### Best Practice:
1. **Before moving project:**
   ```bash
   php fix_schema.php
   ```

2. **After setting up on new computer:**
   ```bash
   php fix_schema.php
   ```

3. **Check schema anytime:**
   ```bash
   php check_schema.php
   ```

---

## Troubleshooting

### Issue: "Table doesn't exist"
**Solution:** Import `database.sql` first, then run `fix_schema.php`

### Issue: "Column already exists"
**Solution:** This is normal - script skips existing items

### Issue: Inventory still not showing
**Check:**
1. Run `DESCRIBE medication_inventory;` in phpMyAdmin
2. Verify `category_id` column exists
3. Check browser console for JavaScript errors
4. Clear browser cache

### Issue: Role change doesn't work
**Check:**
1. SweetAlert library loaded (check footer_admin.php)
2. No JavaScript errors in console
3. CSRF token valid (reload page)

---

## Migration System Notes

The project has these migration approaches:

1. **`run_migrations.php`** - Runs all migrations in order
   - ⚠️ Skips stored procedures (shows warning)
   
2. **`fix_schema.php`** - NEW! Auto-fixes missing items
   - ✅ Always works regardless of migration state
   - ✅ Safe to re-run
   - ✅ Checks actual database state

**Recommendation:** Use `fix_schema.php` for reliability

---

## Contact

If issues persist:
1. Check error logs: `C:\xampp\apache\logs\error.log`
2. Enable debug mode in `config/config.php`
3. Run with verbose MySQL errors

Created: 2026-01-07  
Version: 1.0
