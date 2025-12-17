-- E-BHM Connect: Health Records Dashboard Migration
-- Created: December 17, 2025
-- Based on patient_record.json specification

USE `e-bhw_connect`;

-- =====================================================
-- 1. Pregnancy Tracking Table
-- =====================================================
CREATE TABLE IF NOT EXISTS `pregnancy_tracking` (
    `pregnancy_id` INT(11) NOT NULL AUTO_INCREMENT,
    `patient_id` INT(11) DEFAULT NULL,
    `date_of_identification` DATE NOT NULL,
    `pregnant_woman_name` VARCHAR(255) NOT NULL,
    `age` INT(11) DEFAULT NULL,
    `birth_date` DATE DEFAULT NULL,
    `husband_name` VARCHAR(255) DEFAULT NULL,
    `phone_number` VARCHAR(50) DEFAULT NULL,
    `lmp` DATE DEFAULT NULL COMMENT 'Last Menstrual Period',
    `edc` DATE DEFAULT NULL COMMENT 'Estimated Date of Confinement',
    `tt_status` VARCHAR(100) DEFAULT NULL COMMENT 'Tetanus Toxoid Status',
    `nhts_status` ENUM('NHTS', 'Non-NHTS') DEFAULT 'Non-NHTS',
    `gravida_para` VARCHAR(50) DEFAULT NULL COMMENT 'G-P Score',
    `outcome_date_of_delivery` DATE DEFAULT NULL,
    `outcome_place_of_delivery` VARCHAR(255) DEFAULT NULL,
    `outcome_type_of_delivery` VARCHAR(100) DEFAULT NULL,
    `outcome_of_birth` VARCHAR(255) DEFAULT NULL,
    `remarks` TEXT DEFAULT NULL,
    `bhw_id` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`pregnancy_id`),
    KEY `patient_id_idx` (`patient_id`),
    KEY `bhw_id_idx` (`bhw_id`),
    KEY `lmp_idx` (`lmp`),
    KEY `edc_idx` (`edc`),
    CONSTRAINT `pregnancy_tracking_patient_fk` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL,
    CONSTRAINT `pregnancy_tracking_bhw_fk` FOREIGN KEY (`bhw_id`) REFERENCES `bhw_users` (`bhw_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 2. Child Care Records (12-59 Months)
-- =====================================================
CREATE TABLE IF NOT EXISTS `child_care_records` (
    `child_care_id` INT(11) NOT NULL AUTO_INCREMENT,
    `patient_id` INT(11) DEFAULT NULL,
    `record_number` INT(11) DEFAULT NULL,
    `child_name` VARCHAR(255) NOT NULL,
    `age_months` INT(11) DEFAULT NULL,
    `date_of_birth` DATE DEFAULT NULL,
    `sex` ENUM('Male', 'Female') DEFAULT 'Female',
    `vitamin_a_date` DATE DEFAULT NULL COMMENT '200,000 IU Date Given',
    `albendazole_date` DATE DEFAULT NULL COMMENT '400mg Date Given',
    `bhw_id` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`child_care_id`),
    KEY `patient_id_idx` (`patient_id`),
    KEY `bhw_id_idx` (`bhw_id`),
    CONSTRAINT `child_care_patient_fk` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL,
    CONSTRAINT `child_care_bhw_fk` FOREIGN KEY (`bhw_id`) REFERENCES `bhw_users` (`bhw_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 3. Natality (Birth) Records
-- =====================================================
CREATE TABLE IF NOT EXISTS `natality_records` (
    `natality_id` INT(11) NOT NULL AUTO_INCREMENT,
    `date_of_birth` DATE NOT NULL,
    `baby_complete_name` VARCHAR(255) NOT NULL,
    `sex` ENUM('M', 'F') NOT NULL,
    `weight_kg` DECIMAL(5,2) DEFAULT NULL,
    `time_of_birth` TIME DEFAULT NULL,
    `delivery_type` ENUM('CS', 'Normal') DEFAULT 'Normal',
    `place_of_delivery` VARCHAR(255) DEFAULT NULL,
    `mother_complete_name` VARCHAR(255) DEFAULT NULL,
    `mother_patient_id` INT(11) DEFAULT NULL,
    `mother_age` INT(11) DEFAULT NULL,
    `bhw_id` INT(11) DEFAULT NULL COMMENT 'BHW In Charge',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`natality_id`),
    KEY `mother_patient_id_idx` (`mother_patient_id`),
    KEY `bhw_id_idx` (`bhw_id`),
    KEY `date_of_birth_idx` (`date_of_birth`),
    CONSTRAINT `natality_mother_fk` FOREIGN KEY (`mother_patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL,
    CONSTRAINT `natality_bhw_fk` FOREIGN KEY (`bhw_id`) REFERENCES `bhw_users` (`bhw_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 4. Mortality (Death) Records
-- =====================================================
CREATE TABLE IF NOT EXISTS `mortality_records` (
    `mortality_id` INT(11) NOT NULL AUTO_INCREMENT,
    `record_number` INT(11) DEFAULT NULL,
    `date_of_death` DATE NOT NULL,
    `deceased_complete_name` VARCHAR(255) NOT NULL,
    `patient_id` INT(11) DEFAULT NULL,
    `age` INT(11) DEFAULT NULL,
    `sex` ENUM('M', 'F') DEFAULT NULL,
    `place_of_death` VARCHAR(255) DEFAULT NULL,
    `cause_of_death` TEXT DEFAULT NULL,
    `bhw_id` INT(11) DEFAULT NULL COMMENT 'BHW In Charge',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`mortality_id`),
    KEY `patient_id_idx` (`patient_id`),
    KEY `bhw_id_idx` (`bhw_id`),
    KEY `date_of_death_idx` (`date_of_death`),
    CONSTRAINT `mortality_patient_fk` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL,
    CONSTRAINT `mortality_bhw_fk` FOREIGN KEY (`bhw_id`) REFERENCES `bhw_users` (`bhw_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 5. Hypertensive & Diabetic Masterlist
-- =====================================================
CREATE TABLE IF NOT EXISTS `chronic_disease_masterlist` (
    `chronic_id` INT(11) NOT NULL AUTO_INCREMENT,
    `record_number` INT(11) DEFAULT NULL,
    `patient_id` INT(11) DEFAULT NULL,
    `nhts_member` TINYINT(1) DEFAULT 0,
    `date_of_enrollment` DATE DEFAULT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `middle_name` VARCHAR(100) DEFAULT NULL,
    `sex` ENUM('M', 'F') DEFAULT NULL,
    `age` INT(11) DEFAULT NULL,
    `date_of_birth` DATE DEFAULT NULL,
    `philhealth_no` VARCHAR(50) DEFAULT NULL,
    `is_hypertensive` TINYINT(1) DEFAULT 0,
    `is_diabetic` TINYINT(1) DEFAULT 0,
    `test_type` ENUM('FBS', 'HbA1c', 'OGTT', 'RBS') DEFAULT NULL,
    `blood_sugar_level` DECIMAL(6,2) DEFAULT NULL,
    -- Medications
    `med_amlo5` TINYINT(1) DEFAULT 0,
    `med_amlo10` TINYINT(1) DEFAULT 0,
    `med_losartan50` TINYINT(1) DEFAULT 0,
    `med_losartan100` TINYINT(1) DEFAULT 0,
    `med_metoprolol` TINYINT(1) DEFAULT 0,
    `med_simvastatin` TINYINT(1) DEFAULT 0,
    `med_metformin` TINYINT(1) DEFAULT 0,
    `med_gliclazide` TINYINT(1) DEFAULT 0,
    `med_insulin` TINYINT(1) DEFAULT 0,
    `remarks` TEXT DEFAULT NULL,
    `bhw_id` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`chronic_id`),
    KEY `patient_id_idx` (`patient_id`),
    KEY `bhw_id_idx` (`bhw_id`),
    KEY `philhealth_idx` (`philhealth_no`),
    CONSTRAINT `chronic_patient_fk` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL,
    CONSTRAINT `chronic_bhw_fk` FOREIGN KEY (`bhw_id`) REFERENCES `bhw_users` (`bhw_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 6. NTP (Tuberculosis Program) Client Monitoring
-- =====================================================
CREATE TABLE IF NOT EXISTS `ntp_client_monitoring` (
    `ntp_id` INT(11) NOT NULL AUTO_INCREMENT,
    `patient_id` INT(11) DEFAULT NULL,
    `date_tx_started` DATE NOT NULL COMMENT 'Treatment Start Date',
    `patient_complete_name` VARCHAR(255) NOT NULL,
    `age` INT(11) DEFAULT NULL,
    `sex` ENUM('M', 'F') DEFAULT NULL,
    `barangay_address` VARCHAR(255) DEFAULT NULL,
    `tb_case_no` VARCHAR(100) DEFAULT NULL,
    `date_exam_before_tx` DATE DEFAULT NULL,
    `registration_type` ENUM('New', 'Relapsed') DEFAULT 'New',
    `initial_weight` DECIMAL(5,2) DEFAULT NULL,
    -- Monthly weighing schedule
    `weight_month_1` DECIMAL(5,2) DEFAULT NULL,
    `weight_month_2` DECIMAL(5,2) DEFAULT NULL,
    `weight_month_3` DECIMAL(5,2) DEFAULT NULL,
    `weight_month_4` DECIMAL(5,2) DEFAULT NULL,
    `weight_month_5` DECIMAL(5,2) DEFAULT NULL,
    `weight_month_6` DECIMAL(5,2) DEFAULT NULL,
    `disease_classification` VARCHAR(255) DEFAULT NULL,
    `end_of_treatment` DATE DEFAULT NULL,
    `outcome` VARCHAR(255) DEFAULT NULL,
    `bhw_id` INT(11) DEFAULT NULL COMMENT 'BHW In Charge',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`ntp_id`),
    KEY `patient_id_idx` (`patient_id`),
    KEY `bhw_id_idx` (`bhw_id`),
    KEY `tb_case_no_idx` (`tb_case_no`),
    CONSTRAINT `ntp_patient_fk` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL,
    CONSTRAINT `ntp_bhw_fk` FOREIGN KEY (`bhw_id`) REFERENCES `bhw_users` (`bhw_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 7. Women of Reproductive Age (WRA) Tracking
-- =====================================================
CREATE TABLE IF NOT EXISTS `wra_tracking` (
    `wra_id` INT(11) NOT NULL AUTO_INCREMENT,
    `patient_id` INT(11) DEFAULT NULL,
    `name` VARCHAR(255) NOT NULL,
    `is_nhts` TINYINT(1) DEFAULT 0,
    `age` INT(11) DEFAULT NULL,
    `birthdate` DATE DEFAULT NULL,
    `complete_address` TEXT DEFAULT NULL,
    `contact_number` VARCHAR(50) DEFAULT NULL,
    -- Monthly Status (FP method tracking)
    `status_jan` VARCHAR(50) DEFAULT NULL,
    `status_feb` VARCHAR(50) DEFAULT NULL,
    `status_mar` VARCHAR(50) DEFAULT NULL,
    `status_apr` VARCHAR(50) DEFAULT NULL,
    `status_may` VARCHAR(50) DEFAULT NULL,
    `status_jun` VARCHAR(50) DEFAULT NULL,
    `status_jul` VARCHAR(50) DEFAULT NULL,
    `status_aug` VARCHAR(50) DEFAULT NULL,
    `status_sep` VARCHAR(50) DEFAULT NULL,
    `status_oct` VARCHAR(50) DEFAULT NULL,
    `status_nov` VARCHAR(50) DEFAULT NULL,
    `status_dec` VARCHAR(50) DEFAULT NULL,
    `remarks` TEXT DEFAULT NULL,
    `family_planning_method` VARCHAR(255) DEFAULT NULL,
    `bhw_id` INT(11) DEFAULT NULL COMMENT 'Health Personnel Assigned',
    `tracking_year` YEAR DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`wra_id`),
    KEY `patient_id_idx` (`patient_id`),
    KEY `bhw_id_idx` (`bhw_id`),
    KEY `tracking_year_idx` (`tracking_year`),
    CONSTRAINT `wra_patient_fk` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL,
    CONSTRAINT `wra_bhw_fk` FOREIGN KEY (`bhw_id`) REFERENCES `bhw_users` (`bhw_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- Create indexes for better query performance
-- =====================================================
CREATE INDEX IF NOT EXISTS `pregnancy_tracking_created_idx` ON `pregnancy_tracking` (`created_at`);
CREATE INDEX IF NOT EXISTS `child_care_created_idx` ON `child_care_records` (`created_at`);
CREATE INDEX IF NOT EXISTS `natality_created_idx` ON `natality_records` (`created_at`);
CREATE INDEX IF NOT EXISTS `mortality_created_idx` ON `mortality_records` (`created_at`);
CREATE INDEX IF NOT EXISTS `chronic_created_idx` ON `chronic_disease_masterlist` (`created_at`);
CREATE INDEX IF NOT EXISTS `ntp_created_idx` ON `ntp_client_monitoring` (`created_at`);
CREATE INDEX IF NOT EXISTS `wra_created_idx` ON `wra_tracking` (`created_at`);
