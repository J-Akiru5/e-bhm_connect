-- Migration: Fix medicine_dispensing_log table schema
-- Date: 2025-12-19
-- Issue: Table missing patient_id column for medicine dispensation tracking

-- First check if table exists, if not create it properly
CREATE TABLE IF NOT EXISTS `medicine_dispensing_log` (
    `dispense_id` INT(11) NOT NULL AUTO_INCREMENT,
    `patient_id` INT(11) DEFAULT NULL,
    `item_id` INT(11) NOT NULL,
    `quantity` INT(11) NOT NULL DEFAULT 1,
    `bhw_id` INT(11) DEFAULT NULL,
    `dispensed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `notes` TEXT DEFAULT NULL,
    PRIMARY KEY (`dispense_id`),
    KEY `patient_id_idx` (`patient_id`),
    KEY `item_id_idx` (`item_id`),
    KEY `bhw_id_idx` (`bhw_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- If table exists but missing patient_id column, add it
-- This will silently fail if column exists (which is fine)
ALTER TABLE `medicine_dispensing_log` 
ADD COLUMN IF NOT EXISTS `patient_id` INT(11) DEFAULT NULL AFTER `dispense_id`;

-- Add foreign keys if they don't exist (ignore errors if they do)
-- These might fail if FK already exists, which is okay
