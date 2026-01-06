-- Migration: Fix SMS Queue Table
-- Date: 2026-01-06
-- Description: Add missing columns to sms_queue table

-- Add updated_at column (will fail silently if exists)
ALTER TABLE `sms_queue` ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `sent_at`;

-- Add last_response column for gateway error messages (will fail silently if exists)
ALTER TABLE `sms_queue` ADD COLUMN `last_response` TEXT NULL DEFAULT NULL;

-- Clear old stuck pending messages (older than 7 days)
DELETE FROM `sms_queue` WHERE status = 'pending' AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY);

-- Note: Run with: php run_migrations.php
-- Or copy these statements to phpMyAdmin
