-- Migration: BHW Account Verification & Approval System
-- Date: 2025-12-16
-- Description: Adds email verification, account status, and role management for BHW users

-- Add new columns to bhw_users table
ALTER TABLE bhw_users 
ADD COLUMN email VARCHAR(255) UNIQUE AFTER username,
ADD COLUMN email_verified TINYINT(1) DEFAULT 0 AFTER email,
ADD COLUMN verification_token VARCHAR(255) NULL AFTER email_verified,
ADD COLUMN verification_token_expires DATETIME NULL AFTER verification_token,
ADD COLUMN account_status ENUM('pending', 'verified', 'approved') DEFAULT 'pending' AFTER verification_token_expires,
ADD COLUMN role ENUM('bhw', 'superadmin') DEFAULT 'bhw' AFTER account_status,
ADD COLUMN approved_by INT NULL AFTER role,
ADD COLUMN approved_at DATETIME NULL AFTER approved_by;

-- Add index for faster lookups
CREATE INDEX idx_bhw_account_status ON bhw_users(account_status);
CREATE INDEX idx_bhw_verification_token ON bhw_users(verification_token);
CREATE INDEX idx_bhw_role ON bhw_users(role);

-- Add foreign key for approved_by (references the super admin who approved)
ALTER TABLE bhw_users 
ADD CONSTRAINT fk_bhw_approved_by 
FOREIGN KEY (approved_by) REFERENCES bhw_users(bhw_id) 
ON DELETE SET NULL;
