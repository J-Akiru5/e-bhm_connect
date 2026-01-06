-- Migration: Remove Email Verification Requirement
-- Date: 2026-01-06
-- Description: Simplify registration by removing email verification requirement.
--              Set all users to verified and auto-approve pending accounts.

-- Update bhw_users table
-- Set all email_verified to 1 (verified)
UPDATE `bhw_users` SET `email_verified` = 1 WHERE `email_verified` = 0;

-- Auto-approve all pending BHW accounts (skip email verification step)
UPDATE `bhw_users` 
SET `account_status` = 'approved', 
    `email_verified` = 1,
    `verification_token` = NULL,
    `verification_token_expires` = NULL
WHERE `account_status` IN ('pending', 'verified');

-- Modify email_verified column default to 1 (auto-verified)
ALTER TABLE `bhw_users` 
MODIFY COLUMN `email_verified` TINYINT(1) DEFAULT 1;

-- Update patient_users table
-- Set all email_verified to 1 and activate accounts
UPDATE `patient_users` 
SET `email_verified` = 1, 
    `status` = 'active',
    `verification_token` = NULL,
    `verification_expires_at` = NULL
WHERE `email_verified` = 0 OR `status` = 'pending';

-- Modify patient_users email_verified column default to 1
ALTER TABLE `patient_users` 
MODIFY COLUMN `email_verified` TINYINT(1) DEFAULT 1;

-- Note: Keeping verification columns in schema for potential future use
-- They will simply remain NULL and unused unless re-enabled

