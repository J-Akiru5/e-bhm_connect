-- ============================================
-- E-BHM Connect - Record Changes Migration
-- Created: 2026-01-07
-- Purpose: Full history modification tracking
-- ============================================

-- Create record_changes table for comprehensive audit trail
CREATE TABLE IF NOT EXISTS `record_changes` (
    `change_id` INT(11) NOT NULL AUTO_INCREMENT,
    `table_name` VARCHAR(100) NOT NULL COMMENT 'Name of the table being modified',
    `record_id` INT(11) NOT NULL COMMENT 'Primary key of the record being modified',
    `action` ENUM('insert', 'update', 'delete') NOT NULL COMMENT 'Type of modification',
    `changed_by` INT(11) DEFAULT NULL COMMENT 'BHW ID who made the change',
    `changed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When the change was made',
    `old_values` JSON DEFAULT NULL COMMENT 'Previous values before change (NULL for inserts)',
    `new_values` JSON DEFAULT NULL COMMENT 'New values after change (NULL for deletes)',
    `ip_address` VARCHAR(45) DEFAULT NULL COMMENT 'IP address of the user',
    PRIMARY KEY (`change_id`),
    KEY `idx_table_record` (`table_name`, `record_id`),
    KEY `idx_changed_by` (`changed_by`),
    KEY `idx_changed_at` (`changed_at`),
    KEY `idx_action` (`action`),
    CONSTRAINT `record_changes_fk_user` FOREIGN KEY (`changed_by`) 
        REFERENCES `bhw_users` (`bhw_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add index for faster lookups by table
CREATE INDEX IF NOT EXISTS `idx_record_changes_lookup` ON `record_changes` (`table_name`, `record_id`, `changed_at` DESC);

-- Success message
SELECT 'record_changes table created successfully' AS message;
