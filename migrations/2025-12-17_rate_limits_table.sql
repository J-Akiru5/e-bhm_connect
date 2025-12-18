-- Migration: Add rate_limits table for login rate limiting
-- Date: 2025-12-17

-- Rate limits table for tracking failed login attempts
CREATE TABLE IF NOT EXISTS `rate_limits` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `rate_key` VARCHAR(255) NOT NULL COMMENT 'Composite key: action:identifier (e.g., login_bhw:192.168.1.1)',
    `attempts` INT(11) NOT NULL DEFAULT 0,
    `first_attempt_at` DATETIME NOT NULL,
    `expires_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `rate_key_unique` (`rate_key`),
    KEY `expires_at_idx` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Rate limiting for security features';

-- Add index for faster cleanup of expired records
-- (Index already included in table definition above)
