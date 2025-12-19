-- Migration: Patient Portal Access Improvements
-- Date: 2025-12-19
-- Description: Add email verification and account status to patient_users

-- Add status column for account management
ALTER TABLE `patient_users` 
ADD COLUMN IF NOT EXISTS `status` ENUM('pending', 'active', 'suspended') DEFAULT 'pending' AFTER `password_hash`;

-- Add email verification columns
ALTER TABLE `patient_users` 
ADD COLUMN IF NOT EXISTS `email_verified` TINYINT(1) DEFAULT 0 AFTER `status`;

ALTER TABLE `patient_users` 
ADD COLUMN IF NOT EXISTS `verification_token` VARCHAR(64) NULL AFTER `email_verified`;

ALTER TABLE `patient_users` 
ADD COLUMN IF NOT EXISTS `verification_expires_at` DATETIME NULL AFTER `verification_token`;

-- Update existing users to be active and verified (grandfather in existing accounts)
UPDATE `patient_users` SET `status` = 'active', `email_verified` = 1 WHERE `status` = 'pending' OR `status` IS NULL;

-- Add portal registration mode to app_settings if not exists
INSERT INTO `app_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) 
VALUES ('portal_registration_mode', 'linked_only', 'string', 'Controls patient portal registration: open, linked_only, or approval_required')
ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;
