-- Migration: Add last_login column to bhw_users table
-- Date: 2026-01-07
-- Issue: Column last_login referenced in user_roles.php but missing from table

ALTER TABLE `bhw_users` 
ADD COLUMN IF NOT EXISTS `last_login` DATETIME NULL DEFAULT NULL AFTER `created_at`;

-- Create index for faster querying
-- ALTER TABLE `bhw_users` ADD INDEX IF NOT EXISTS `idx_last_login` (`last_login`);

SELECT 'last_login column added to bhw_users table' AS message;
