-- Seeder: Create Super Admin Account for Healthcare Center Head
-- Date: 2025-12-16
-- Description: Seeds the initial super admin account for the healthcare center head
-- 
-- IMPORTANT: Change the password hash before running in production!
-- The default password is: SuperAdmin@2025
-- Generate a new hash using: password_hash('YourSecurePassword', PASSWORD_BCRYPT)

-- First, ensure the columns exist (run migration first if not)
-- Then insert the super admin account

INSERT INTO bhw_users (
    full_name,
    username,
    email,
    password_hash,
    bhw_unique_id,
    email_verified,
    account_status,
    role,
    created_at
) VALUES (
    'Healthcare Center Head',
    'superadmin',
    'healthcenter.head@example.com',
    -- Default password: SuperAdmin@2025 (CHANGE IN PRODUCTION!)
    '$2y$10$nY9WrJoLEYPtbxbnO/Wh7e2d.WBQdUOZ/auPwYWRT0LJzIcaXVF4G',
    'SUPERADMIN-001',
    1,
    'approved',
    'superadmin',
    NOW()
);

-- Verify the insertion
SELECT bhw_id, full_name, username, email, account_status, role 
FROM bhw_users 
WHERE role = 'superadmin';
