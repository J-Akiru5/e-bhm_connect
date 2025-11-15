-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `e-bhw_connect` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `e-bhw_connect`;

-- 1. BHW Users Table
CREATE TABLE `bhw_users` (
  `bhw_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `bhw_unique_id` varchar(100) NOT NULL,
  `training_cert` text DEFAULT NULL,
  `assigned_area` varchar(255) DEFAULT NULL,
  `employment_status` varchar(50) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`bhw_id`),
  UNIQUE KEY `bhw_unique_id` (`bhw_unique_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Patients Table
CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Patient Users (for Portal Login)
CREATE TABLE `patient_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `patient_id_fk` (`patient_id`),
  CONSTRAINT `patient_users_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Patient Health Records
CREATE TABLE `patient_health_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `medical_history` text DEFAULT NULL,
  `immunization_records` text DEFAULT NULL,
  `medication_records` text DEFAULT NULL,
  `maternal_child_health` text DEFAULT NULL,
  `chronic_disease_mgmt` text DEFAULT NULL,
  `referral_information` text DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`record_id`),
  KEY `patient_id_fk_health` (`patient_id`),
  CONSTRAINT `patient_health_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Patient Vitals
CREATE TABLE `patient_vitals` (
  `vital_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `blood_pressure` varchar(20) DEFAULT NULL,
  `heart_rate` int(11) DEFAULT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`vital_id`),
  KEY `patient_id_fk_vitals` (`patient_id`),
  CONSTRAINT `patient_vitals_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Health Visits
CREATE TABLE `health_visits` (
  `visit_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `bhw_id` int(11) NOT NULL,
  `visit_date` date NOT NULL,
  `visit_type` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`visit_id`),
  KEY `patient_id_fk_visits` (`patient_id`),
  KEY `bhw_id_fk_visits` (`bhw_id`),
  CONSTRAINT `health_visits_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  CONSTRAINT `health_visits_ibfk_2` FOREIGN KEY (`bhw_id`) REFERENCES `bhw_users` (`bhw_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Family Composition
CREATE TABLE `family_composition` (
  `family_member_id` int(11) NOT NULL AUTO_INCREMENT,
  `head_patient_id` int(11) NOT NULL,
  `member_name` varchar(255) NOT NULL,
  `relationship` varchar(100) DEFAULT NULL,
  `health_status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`family_member_id`),
  KEY `head_patient_id_fk` (`head_patient_id`),
  CONSTRAINT `family_composition_ibfk_1` FOREIGN KEY (`head_patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Medication Inventory
CREATE TABLE `medication_inventory` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity_in_stock` int(11) NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `last_restock` date DEFAULT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Health Programs
CREATE TABLE `health_programs` (
  `program_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Chatbot History
CREATE TABLE `chatbot_history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `prompt_text` text NOT NULL,
  `response_text` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`history_id`),
  KEY `user_id_fk_chat` (`user_id`),
  CONSTRAINT `chatbot_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `patient_users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;