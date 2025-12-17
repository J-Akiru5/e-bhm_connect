-- Migration: Admin Portal Overhaul - New Tables
-- Date: 2025-12-16
-- Description: Creates tables for audit logs, notifications, user preferences, and app settings

-- ============================================
-- 1. Audit Logs Table
-- Tracks all significant user actions in the system
-- ============================================
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `log_id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) DEFAULT NULL,
    `user_type` ENUM('bhw', 'patient', 'system') NOT NULL DEFAULT 'bhw',
    `action` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(50) DEFAULT NULL COMMENT 'e.g., patient, inventory, bhw_user, announcement',
    `entity_id` INT(11) DEFAULT NULL,
    `old_values` JSON DEFAULT NULL,
    `new_values` JSON DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`log_id`),
    KEY `idx_audit_user` (`user_id`, `user_type`),
    KEY `idx_audit_action` (`action`),
    KEY `idx_audit_entity` (`entity_type`, `entity_id`),
    KEY `idx_audit_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- 2. Notifications Table
-- Stores user notifications for both BHW and patients
-- ============================================
CREATE TABLE IF NOT EXISTS `notifications` (
    `notification_id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `user_type` ENUM('bhw', 'patient') NOT NULL DEFAULT 'bhw',
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('info', 'success', 'warning', 'error') NOT NULL DEFAULT 'info',
    `link` VARCHAR(255) DEFAULT NULL COMMENT 'Optional URL to navigate to',
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `read_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`notification_id`),
    KEY `idx_notif_user` (`user_id`, `user_type`),
    KEY `idx_notif_unread` (`user_id`, `user_type`, `is_read`),
    KEY `idx_notif_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- 3. User Preferences Table
-- Stores UI preferences (theme, language) for all users
-- ============================================
CREATE TABLE IF NOT EXISTS `user_preferences` (
    `pref_id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `user_type` ENUM('bhw', 'patient') NOT NULL DEFAULT 'bhw',
    `theme` ENUM('light', 'dark', 'system') NOT NULL DEFAULT 'system',
    `language` ENUM('en', 'tl') NOT NULL DEFAULT 'en' COMMENT 'en=English, tl=Tagalog',
    `sidebar_collapsed` TINYINT(1) NOT NULL DEFAULT 0,
    `dashboard_widgets` JSON DEFAULT NULL COMMENT 'Widget visibility/order preferences',
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`pref_id`),
    UNIQUE KEY `idx_user_pref` (`user_id`, `user_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- 4. App Settings Table
-- System-wide application configuration (key-value store)
-- ============================================
CREATE TABLE IF NOT EXISTS `app_settings` (
    `setting_id` INT(11) NOT NULL AUTO_INCREMENT,
    `setting_key` VARCHAR(100) NOT NULL,
    `setting_value` TEXT DEFAULT NULL,
    `setting_type` ENUM('string', 'number', 'boolean', 'json') NOT NULL DEFAULT 'string',
    `category` VARCHAR(50) NOT NULL DEFAULT 'general',
    `description` VARCHAR(255) DEFAULT NULL,
    `is_public` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Whether visible to non-admin users',
    `updated_by` INT(11) DEFAULT NULL,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`setting_id`),
    UNIQUE KEY `idx_setting_key` (`setting_key`),
    KEY `idx_setting_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- 5. Seed Default App Settings
-- ============================================
INSERT INTO `app_settings` (`setting_key`, `setting_value`, `setting_type`, `category`, `description`, `is_public`) VALUES
-- General Settings
('site_name', 'E-BHM Connect', 'string', 'general', 'Application display name', 1),
('site_tagline', 'Barangay Health Management System', 'string', 'general', 'Site tagline/subtitle', 1),
('barangay_name', 'Bacong', 'string', 'general', 'Barangay name', 1),
('municipality', 'Dumangas', 'string', 'general', 'Municipality name', 1),
('province', 'Iloilo', 'string', 'general', 'Province name', 1),
('health_center_name', 'Bacong Barangay Health Center', 'string', 'general', 'Health center name', 1),
('health_center_contact', '(033) 123-4567', 'string', 'general', 'Health center contact number', 1),
('health_center_email', 'healthcenter@bacong.gov', 'string', 'general', 'Health center email', 1),

-- Feature Toggles
('enable_sms_notifications', 'true', 'boolean', 'features', 'Enable SMS notifications', 0),
('enable_email_notifications', 'true', 'boolean', 'features', 'Enable email notifications', 0),
('enable_chatbot', 'true', 'boolean', 'features', 'Enable AI chatbot (Gabby)', 0),
('enable_patient_portal', 'true', 'boolean', 'features', 'Enable patient portal registration', 0),
('require_bhw_approval', 'true', 'boolean', 'features', 'Require superadmin approval for BHW accounts', 0),

-- Maintenance
('maintenance_mode', 'false', 'boolean', 'maintenance', 'Enable maintenance mode', 0),
('maintenance_message', 'The system is currently under maintenance. Please try again later.', 'string', 'maintenance', 'Maintenance mode message', 1),

-- Audit Settings
('audit_retention_days', '90', 'number', 'audit', 'Days to retain audit logs (0 = forever)', 0),
('log_login_attempts', 'true', 'boolean', 'audit', 'Log login attempts', 0),
('log_data_changes', 'true', 'boolean', 'audit', 'Log data create/update/delete', 0)

ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- ============================================
-- 6. Add avatar column to bhw_users if not exists
-- ============================================
-- Check and add avatar column
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'bhw_users' 
    AND COLUMN_NAME = 'avatar');

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `bhw_users` ADD COLUMN `avatar` VARCHAR(255) DEFAULT NULL AFTER `role`',
    'SELECT "avatar column already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- 7. Add avatar column to patient_users if not exists
-- ============================================
SET @col_exists2 = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'patient_users' 
    AND COLUMN_NAME = 'avatar');

SET @sql2 = IF(@col_exists2 = 0, 
    'ALTER TABLE `patient_users` ADD COLUMN `avatar` VARCHAR(255) DEFAULT NULL AFTER `password_hash`',
    'SELECT "avatar column already exists in patient_users"');
PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

-- ============================================
-- Verify tables created
-- ============================================
SELECT 'Migration completed successfully!' AS status;
SELECT TABLE_NAME, TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME IN ('audit_logs', 'notifications', 'user_preferences', 'app_settings');
