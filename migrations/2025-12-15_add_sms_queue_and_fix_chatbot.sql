-- Migration: Add sms_queue table and fix chatbot_history constraint
-- Generated: 2025-12-15
-- Run this on existing databases to add the sms_queue table

-- Create sms_queue table if missing
CREATE TABLE IF NOT EXISTS `sms_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone_number` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','sent','failed') NOT NULL DEFAULT 'pending',
  `attempts` int(11) NOT NULL DEFAULT 0,
  `last_attempt` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sent_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`),
  KEY `created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Fix chatbot_history to allow NULL user_id (for anonymous/guest usage)
-- First drop the foreign key if it exists, then modify the column
DELIMITER $$

CREATE PROCEDURE fix_chatbot_history()
BEGIN
  -- Check if chatbot_history table exists
  IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'chatbot_history') THEN
    -- Drop existing foreign key if it exists
    IF EXISTS (SELECT 1 FROM information_schema.table_constraints WHERE constraint_schema = DATABASE() AND table_name = 'chatbot_history' AND constraint_name = 'chatbot_history_ibfk_1') THEN
      ALTER TABLE chatbot_history DROP FOREIGN KEY chatbot_history_ibfk_1;
    END IF;
    
    -- Modify column to allow NULL
    ALTER TABLE chatbot_history MODIFY COLUMN user_id int(11) DEFAULT NULL;
    
    -- Re-add foreign key with SET NULL on delete
    ALTER TABLE chatbot_history ADD CONSTRAINT chatbot_history_ibfk_1 
      FOREIGN KEY (user_id) REFERENCES patient_users(user_id) ON DELETE SET NULL;
  END IF;
END$$

CALL fix_chatbot_history()$$
DROP PROCEDURE IF EXISTS fix_chatbot_history$$

DELIMITER ;

-- Notes:
-- 1) This migration creates the sms_queue table for SMS functionality
-- 2) It also fixes the chatbot_history table to allow anonymous (NULL) user_id
-- 3) Run this on staging/test DB first
