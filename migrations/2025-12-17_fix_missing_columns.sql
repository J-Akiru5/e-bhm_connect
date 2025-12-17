-- Migration: Fix Missing Columns
-- Date: 2025-12-17
-- Description: Adds missing columns to user_preferences, audit_logs, and health_visits tables

USE `e-bhw_connect`;

-- ============================================
-- 1. Add missing columns to user_preferences
-- ============================================
ALTER TABLE `user_preferences`
ADD COLUMN IF NOT EXISTS `notifications_enabled` TINYINT(1) NOT NULL DEFAULT 1 AFTER `dashboard_widgets`,
ADD COLUMN IF NOT EXISTS `email_notifications` TINYINT(1) NOT NULL DEFAULT 1 AFTER `notifications_enabled`;

-- ============================================
-- 2. Add missing 'details' column to audit_logs
-- ============================================
ALTER TABLE `audit_logs`
ADD COLUMN IF NOT EXISTS `details` TEXT DEFAULT NULL AFTER `new_values`;

-- ============================================
-- 3. Add missing 'notes' column to health_visits (if table exists)
-- ============================================
-- Check if health_visits table exists before adding notes column
SET @table_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'health_visits');

SET @sql_hv = IF(@table_exists > 0,
    'ALTER TABLE `health_visits` ADD COLUMN IF NOT EXISTS `notes` TEXT DEFAULT NULL',
    'SELECT "health_visits table does not exist, skipping"');
    
PREPARE stmt_hv FROM @sql_hv;
EXECUTE stmt_hv;
DEALLOCATE PREPARE stmt_hv;

-- ============================================
-- Verify columns added
-- ============================================
SELECT 'Migration completed successfully!' AS status;
SELECT 
    TABLE_NAME, 
    COLUMN_NAME, 
    DATA_TYPE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND (
    (TABLE_NAME = 'user_preferences' AND COLUMN_NAME IN ('notifications_enabled', 'email_notifications'))
    OR (TABLE_NAME = 'audit_logs' AND COLUMN_NAME = 'details')
    OR (TABLE_NAME = 'health_visits' AND COLUMN_NAME = 'notes')
)
ORDER BY TABLE_NAME, ORDINAL_POSITION;
