-- Migration: Create audit_logs table
-- Date: 2025-12-19
-- Description: Creates audit_logs table for tracking all system activity

CREATE TABLE IF NOT EXISTS `audit_logs` (
  `log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_type` varchar(20) NOT NULL DEFAULT 'system',
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `idx_user` (`user_id`, `user_type`),
  KEY `idx_action` (`action`),
  KEY `idx_entity` (`entity_type`, `entity_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Instructions:
-- Run this SQL in your phpMyAdmin or MySQL client to create the audit_logs table
