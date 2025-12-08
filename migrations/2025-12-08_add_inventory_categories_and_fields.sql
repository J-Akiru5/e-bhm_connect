-- Migration: Add inventory_categories table and inventory fields
-- Generated: 2025-12-08
-- IMPORTANT: Run this on a staging/test DB first. This script is written to be idempotent
-- (it uses a small stored-procedure wrapper to check existence before altering).

-- Create categories table if missing
CREATE TABLE IF NOT EXISTS inventory_categories (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(191) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add medication_inventory columns only if they don't exist
DELIMITER $$
CREATE PROCEDURE add_inventory_columns()
BEGIN
  -- category_id
  IF (SELECT COUNT(*) FROM information_schema.COLUMNS 
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'medication_inventory' AND COLUMN_NAME = 'category_id') = 0 THEN
    ALTER TABLE medication_inventory ADD COLUMN category_id INT NULL AFTER item_id;
  END IF;

  -- batch_number
  IF (SELECT COUNT(*) FROM information_schema.COLUMNS 
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'medication_inventory' AND COLUMN_NAME = 'batch_number') = 0 THEN
    ALTER TABLE medication_inventory ADD COLUMN batch_number VARCHAR(100) NULL AFTER category_id;
  END IF;

  -- expiry_date
  IF (SELECT COUNT(*) FROM information_schema.COLUMNS 
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'medication_inventory' AND COLUMN_NAME = 'expiry_date') = 0 THEN
    ALTER TABLE medication_inventory ADD COLUMN expiry_date DATE NULL AFTER batch_number;
  END IF;

  -- stock_alert_limit
  IF (SELECT COUNT(*) FROM information_schema.COLUMNS 
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'medication_inventory' AND COLUMN_NAME = 'stock_alert_limit') = 0 THEN
    ALTER TABLE medication_inventory ADD COLUMN stock_alert_limit INT NOT NULL DEFAULT 10 AFTER expiry_date;
  END IF;

  -- Create index on category_id for faster joins (only if missing)
  IF (SELECT COUNT(*) FROM information_schema.STATISTICS 
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'medication_inventory' AND INDEX_NAME = 'idx_med_category') = 0 THEN
    ALTER TABLE medication_inventory ADD INDEX idx_med_category (category_id);
  END IF;
END$$

CALL add_inventory_columns();
DROP PROCEDURE IF EXISTS add_inventory_columns$$
DELIMITER ;

-- OPTIONAL: Add FK (only if you want referential integrity). Run this after verifying types/engines and orphan rows.
-- ALTER TABLE medication_inventory
--   ADD CONSTRAINT fk_med_category FOREIGN KEY (category_id) REFERENCES inventory_categories(category_id) ON DELETE SET NULL;

-- Notes:
-- 1) This script updates medication_inventory by adding nullable columns so no data is lost.
-- 2) If your MySQL version does not allow CREATE PROCEDURE in a migration runner, run individual ALTER statements manually.
-- 3) Test on staging before running on production.
