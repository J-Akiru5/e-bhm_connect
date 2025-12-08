-- Migration: Create medicine_dispensing_log table
-- Date: 2025-12-08
-- Purpose: Track dispensing events for medications (which BHW dispensed which item to which patient)

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
  KEY `bhw_id_idx` (`bhw_id`),
  CONSTRAINT `medicine_dispensing_log_fk_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL,
  CONSTRAINT `medicine_dispensing_log_fk_item` FOREIGN KEY (`item_id`) REFERENCES `medication_inventory` (`item_id`) ON DELETE RESTRICT,
  CONSTRAINT `medicine_dispensing_log_fk_bhw` FOREIGN KEY (`bhw_id`) REFERENCES `bhw_users` (`bhw_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
