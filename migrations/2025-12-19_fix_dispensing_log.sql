-- Migration: Fix medicine_dispensing_log table schema
-- Date: 2025-12-19 (Updated 2026-01-07)
-- Issue: Table uses resident_id column for patient linkage

-- First check if table exists, if not create it properly
CREATE TABLE IF NOT EXISTS `medicine_dispensing_log` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `resident_id` INT(11) NOT NULL,
    `item_id` INT(11) NOT NULL,
    `quantity` INT(11) NOT NULL DEFAULT 1,
    `bhw_id` INT(11) DEFAULT NULL,
    `dispensed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_mdl_resident` (`resident_id`),
    KEY `item_id_idx` (`item_id`),
    KEY `bhw_id_idx` (`bhw_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add resident_id column if missing (in case table was created with different schema)
-- This is a safe operation that won't fail if column exists
ALTER TABLE `medicine_dispensing_log` 
ADD COLUMN IF NOT EXISTS `resident_id` INT(11) NOT NULL AFTER `id`;

-- Add created_at if missing
ALTER TABLE `medicine_dispensing_log` 
ADD COLUMN IF NOT EXISTS `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP AFTER `notes`;

-- Add foreign keys if they don't exist (ignore errors if they do)
-- These might fail if FK already exists, which is okay
