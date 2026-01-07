-- Health Records Seeder Data
-- E-BHM Connect - Sample data for all 7 health record types
-- Run this after importing 2025-12-17_health_records_dashboard.sql
-- SAFE TO RE-RUN: Uses INSERT IGNORE to prevent duplicate errors

USE `e-bhw_connect`;

-- =====================================================
-- Pregnancy Tracking Records (10 samples)
-- =====================================================
INSERT IGNORE INTO `pregnancy_tracking` (`pregnant_woman_name`, `age`, `birth_date`, `husband_name`, `phone_number`, `date_of_identification`, `lmp`, `edc`, `tt_status`, `nhts_status`, `gravida_para`, `outcome_date_of_delivery`, `outcome_place_of_delivery`, `outcome_type_of_delivery`, `outcome_of_birth`, `remarks`, `patient_id`, `bhw_id`, `created_at`) VALUES
('Maria Santos', 29, '1995-03-15', 'Juan Santos', '09171234567', '2024-10-01', '2024-10-01', '2025-07-08', 'TT2', 'NHTS', 'G2P1', NULL, NULL, NULL, NULL, 'Regular checkups', NULL, 1, NOW()),
('Ana Cruz', 26, '1998-07-22', 'Pedro Cruz', '09182345678', '2024-11-15', '2024-11-15', '2025-08-22', 'TT1', 'Non-NHTS', 'G1P0', NULL, NULL, NULL, NULL, 'First pregnancy', NULL, 1, NOW()),
('Lucia Reyes', 32, '1992-11-08', 'Miguel Reyes', '09193456789', '2024-09-10', '2024-09-10', '2025-06-17', 'TT3', 'NHTS', 'G3P2', NULL, NULL, NULL, NULL, NULL, NULL, 1, NOW()),
('Carmen dela Cruz', 28, '1996-05-20', 'Roberto dela Cruz', '09204567890', '2024-08-20', '2024-08-20', '2025-05-27', 'TT2', 'Non-NHTS', 'G2P1', '2025-05-25', 'Rural Health Unit', 'Normal Delivery', 'Live birth, baby boy, 3.2kg', 'Successful delivery', NULL, 1, NOW()),
('Rosa Garcia', 30, '1994-09-12', 'Carlos Garcia', '09215678901', '2024-12-01', '2024-12-01', '2025-09-08', 'TT1', 'NHTS', 'G1P0', NULL, NULL, NULL, NULL, NULL, NULL, 1, NOW()),
('Elena Martinez', 27, '1997-02-28', 'Jose Martinez', '09226789012', '2024-07-15', '2024-07-15', '2025-04-22', 'TT4', 'NHTS', 'G4P3', '2025-04-20', 'Provincial Hospital', 'Cesarean Section', 'Live birth, baby girl, 2.9kg', 'C-section due to previous CS', NULL, 1, NOW()),
('Sofia Mendoza', 25, '1999-06-10', 'Rafael Mendoza', '09237890123', '2024-10-20', '2024-10-20', '2025-07-27', 'TT1', 'Non-NHTS', 'G1P0', NULL, NULL, NULL, NULL, 'Young mother', NULL, 1, NOW()),
('Isabel Torres', 31, '1993-12-05', 'Gabriel Torres', '09248901234', '2024-09-05', '2024-09-05', '2025-06-12', 'TT2', 'NHTS', 'G2P1', NULL, NULL, NULL, NULL, NULL, NULL, 1, NOW()),
('Gloria Ramos', 29, '1995-08-18', 'Antonio Ramos', '09259012345', '2024-11-10', '2024-11-10', '2025-08-17', 'TT3', 'Non-NHTS', 'G3P2', NULL, NULL, NULL, NULL, NULL, NULL, 1, NOW()),
('Patricia Santos', 28, '1996-04-25', 'Miguel Santos', '09260123456', '2024-08-01', '2024-08-01', '2025-05-08', 'TT2', 'NHTS', 'G2P1', '2025-05-10', 'Rural Health Unit', 'Normal Delivery', 'Live birth, baby girl, 3.0kg', NULL, NULL, 1, NOW());

-- =====================================================
-- Child Care Records (15 samples)
-- =====================================================
INSERT IGNORE INTO `child_care_records` (`child_name`, `date_of_birth`, `age_months`, `sex`, `vitamin_a_date`, `albendazole_date`, `record_number`, `patient_id`, `bhw_id`, `created_at`) VALUES
('Juan dela Cruz Jr.', '2022-01-15', 35, 'Male', '2024-12-01', '2024-12-01', 1, NULL, 1, NOW()),
('Maria Clara Santos', '2021-06-20', 42, 'Female', '2024-11-15', '2024-11-15', 2, NULL, 1, NOW()),
('Pedro Garcia', '2022-03-10', 33, 'Male', '2024-12-05', NULL, 3, NULL, 1, NOW()),
('Ana Marie Reyes', '2021-09-05', 39, 'Female', '2024-11-20', '2024-11-20', 4, NULL, 1, NOW()),
('Jose Miguel Cruz', '2022-05-18', 31, 'Male', NULL, NULL, 5, NULL, 1, NOW()),
('Sofia Isabel Torres', '2021-11-12', 37, 'Female', '2024-12-10', '2024-12-10', 6, NULL, 1, NOW()),
('Carlos Antonio Mendoza', '2022-02-28', 33, 'Male', '2024-12-01', '2024-12-01', 7, NULL, 1, NOW()),
('Isabela Cruz Martinez', '2021-08-15', 40, 'Female', '2024-11-25', '2024-11-25', 8, NULL, 1, NOW()),
('Miguel Angel Ramos', '2022-04-22', 32, 'Male', '2024-12-08', NULL, 9, NULL, 1, NOW()),
('Gabriela Santos', '2021-10-30', 38, 'Female', '2024-11-18', '2024-11-18', 10, NULL, 1, NOW()),
('Rafael Garcia', '2022-06-05', 30, 'Male', '2024-12-12', '2024-12-12', 11, NULL, 1, NOW()),
('Valentina Reyes', '2021-07-20', 41, 'Female', '2024-11-22', '2024-11-22', 12, NULL, 1, NOW()),
('Sebastian Cruz', '2022-01-08', 35, 'Male', NULL, NULL, 13, NULL, 1, NOW()),
('Camila Torres', '2021-12-15', 36, 'Female', '2024-12-05', '2024-12-05', 14, NULL, 1, NOW()),
('Diego Mendoza', '2022-03-25', 32, 'Male', '2024-12-01', '2024-12-01', 15, NULL, 1, NOW());

-- =====================================================
-- Natality (Birth) Records (12 samples)
-- =====================================================
INSERT IGNORE INTO `natality_records` (`baby_complete_name`, `date_of_birth`, `time_of_birth`, `sex`, `weight_kg`, `place_of_delivery`, `delivery_type`, `mother_complete_name`, `mother_age`, `mother_patient_id`, `bhw_id`, `created_at`) VALUES
('Baby Boy Santos', '2025-01-05', '08:30:00', 'M', 3.20, 'Rural Health Unit', 'Normal', 'Maria Santos', 28, NULL, 1, NOW()),
('Baby Girl Cruz', '2025-01-10', '14:20:00', 'F', 2.90, 'Provincial Hospital', 'CS', 'Ana Cruz', 26, NULL, 1, NOW()),
('Carlos Miguel Reyes', '2024-12-20', '06:15:00', 'M', 3.50, 'Home', 'Normal', 'Lucia Reyes', 32, NULL, 1, NOW()),
('Sofia Marie Garcia', '2024-12-25', '22:45:00', 'F', 3.10, 'Rural Health Unit', 'Normal', 'Rosa Garcia', 30, NULL, 1, NOW()),
('Baby Boy Martinez', '2024-11-30', '10:30:00', 'M', 3.80, 'Provincial Hospital', 'CS', 'Elena Martinez', 27, NULL, 1, NOW()),
('Isabella Grace Mendoza', '2024-12-15', '16:00:00', 'F', 2.80, 'Rural Health Unit', 'Normal', 'Sofia Mendoza', 25, NULL, 1, NOW()),
('Gabriel Torres', '2025-01-02', '03:20:00', 'M', 3.30, 'Home', 'Normal', 'Isabel Torres', 31, NULL, 1, NOW()),
('Baby Girl Ramos', '2024-12-28', '19:50:00', 'F', 3.00, 'Provincial Hospital', 'Normal', 'Gloria Ramos', 29, NULL, 1, NOW()),
('Miguel Antonio Santos', '2025-01-08', '11:15:00', 'M', 3.60, 'Rural Health Unit', 'Normal', 'Patricia Santos', 28, NULL, 1, NOW()),
('Baby Girl dela Cruz', '2024-12-18', '07:40:00', 'F', 2.70, 'Home', 'Normal', 'Carmen dela Cruz', 33, NULL, 1, NOW()),
('Andres Garcia', '2024-11-25', '15:30:00', 'M', 3.40, 'Provincial Hospital', 'CS', 'Marissa Garcia', 35, NULL, 1, NOW()),
('Baby Girl Mercado', '2025-01-12', '09:00:00', 'F', 3.10, 'Rural Health Unit', 'Normal', 'Anna Mercado', 24, NULL, 1, NOW());

-- =====================================================
-- Mortality (Death) Records (8 samples)
-- =====================================================
INSERT IGNORE INTO `mortality_records` (`deceased_complete_name`, `date_of_death`, `age`, `sex`, `cause_of_death`, `place_of_death`, `record_number`, `patient_id`, `bhw_id`, `created_at`) VALUES
('Lolo Andres Cruz', '2024-12-01', 84, 'M', 'Cardiac Arrest', 'Home', 1, NULL, 1, NOW()),
('Lola Remedios Santos', '2024-11-15', 79, 'F', 'Pneumonia', 'Provincial Hospital', 2, NULL, 1, NOW()),
('Baby Boy Garcia', '2024-10-22', 0, 'M', 'Premature Birth Complications', 'Provincial Hospital', 3, NULL, 1, NOW()),
('Pedro Ramos', '2024-12-10', 69, 'M', 'Stroke', 'Home', 4, NULL, 1, NOW()),
('Maria Theresa Mendoza', '2024-11-20', 34, 'F', 'Postpartum Hemorrhage', 'Provincial Hospital', 5, NULL, 1, NOW()),
('Jose dela Cruz', '2024-12-05', 64, 'M', 'Heart Attack', 'On the way to hospital', 6, NULL, 1, NOW()),
('Baby Girl Torres', '2024-11-29', 0, 'F', 'Birth Asphyxia', 'Provincial Hospital', 7, NULL, 1, NOW()),
('Elena Reyes', '2024-11-25', 74, 'F', 'Cancer', 'Home', 8, NULL, 1, NOW());

-- =====================================================
-- Chronic Disease Masterlist (20 samples)
-- =====================================================
INSERT IGNORE INTO `chronic_disease_masterlist` (`record_number`, `last_name`, `first_name`, `middle_name`, `sex`, `age`, `date_of_birth`, `philhealth_no`, `nhts_member`, `date_of_enrollment`, `is_hypertensive`, `is_diabetic`, `test_type`, `blood_sugar_level`, `med_amlo5`, `med_amlo10`, `med_losartan50`, `med_losartan100`, `med_metoprolol`, `med_simvastatin`, `med_metformin`, `med_gliclazide`, `med_insulin`, `remarks`, `patient_id`, `bhw_id`, `created_at`) VALUES
(1, 'Santos', 'Roberto', 'M', 'M', 59, '1965-03-15', '1234567890', 1, '2024-01-15', 1, 0, NULL, NULL, 0, 0, 1, 0, 0, 0, 0, 0, 0, 'Controlled hypertension', NULL, 1, NOW()),
(2, 'Cruz', 'Leonora', 'A', 'F', 66, '1958-07-20', '2345678901', 1, '2023-06-10', 1, 1, 'FBS', 130.00, 1, 0, 0, 0, 0, 0, 1, 0, 0, 'Both conditions stable', NULL, 1, NOW()),
(3, 'Garcia', 'Fernando', 'B', 'M', 54, '1970-11-08', '3456789012', 0, '2024-03-20', 0, 1, 'RBS', 145.00, 0, 0, 0, 0, 0, 0, 0, 1, 0, 'Needs diet adjustment', NULL, 1, NOW()),
(4, 'Reyes', 'Victoria', 'C', 'F', 62, '1962-05-12', '4567890123', 1, '2023-09-15', 1, 1, 'FBS', 160.00, 0, 0, 1, 0, 0, 0, 0, 0, 1, 'Uncontrolled diabetes', NULL, 1, NOW()),
(5, 'Mendoza', 'Alberto', 'D', 'M', 56, '1968-09-25', '5678901234', 1, '2024-02-28', 1, 0, NULL, NULL, 0, 0, 0, 1, 0, 0, 0, 0, 0, 'Regular checkup', NULL, 1, NOW()),
(6, 'Torres', 'Carmela', 'E', 'F', 69, '1955-02-14', '6789012345', 1, '2023-05-10', 1, 1, 'FBS', 120.00, 1, 0, 0, 0, 0, 1, 1, 0, 0, 'Compliant with meds', NULL, 1, NOW()),
(7, 'Ramos', 'Manuel', 'F', 'M', 64, '1960-06-30', '7890123456', 0, '2024-04-05', 0, 1, 'FBS', 110.00, 0, 0, 0, 0, 0, 0, 1, 0, 0, 'Well controlled', NULL, 1, NOW()),
(8, 'Martinez', 'Angelina', 'G', 'F', 61, '1963-10-18', '8901234567', 1, '2023-11-20', 1, 0, NULL, NULL, 0, 0, 1, 0, 0, 1, 0, 0, 0, NULL, NULL, 1, NOW()),
(9, 'dela Cruz', 'Ricardo', 'H', 'M', 67, '1957-12-05', '9012345678', 1, '2023-08-15', 1, 1, 'RBS', 155.00, 0, 0, 0, 1, 0, 0, 0, 1, 0, 'Needs monitoring', NULL, 1, NOW()),
(10, 'Santos', 'Esperanza', 'I', 'F', 58, '1966-04-22', '0123456789', 0, '2024-05-30', 1, 0, NULL, NULL, 1, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, 1, NOW()),
(11, 'Garcia', 'Teodoro', 'J', 'M', 65, '1959-08-15', '1230987654', 1, '2023-07-25', 0, 1, 'FBS', 180.00, 0, 0, 0, 0, 0, 0, 0, 0, 1, 'Type 1 diabetes', NULL, 1, NOW()),
(12, 'Cruz', 'Rosario', 'K', 'F', 60, '1964-01-28', '2341098765', 1, '2024-01-10', 1, 1, 'FBS', 125.00, 0, 0, 1, 0, 0, 0, 1, 0, 0, NULL, NULL, 1, NOW()),
(13, 'Reyes', 'Ernesto', 'L', 'M', 63, '1961-07-10', '3452109876', 0, '2024-06-15', 1, 0, NULL, NULL, 0, 0, 0, 1, 0, 0, 0, 0, 0, NULL, NULL, 1, NOW()),
(14, 'Mendoza', 'Luzviminda', 'M', 'F', 57, '1967-11-03', '4563210987', 1, '2023-12-20', 1, 1, 'RBS', 140.00, 1, 0, 0, 0, 0, 0, 0, 1, 0, NULL, NULL, 1, NOW()),
(15, 'Torres', 'Antonio', 'N', 'M', 68, '1956-03-20', '5674321098', 1, '2023-10-05', 0, 1, 'FBS', 115.00, 0, 0, 0, 0, 0, 0, 1, 0, 0, 'Good control', NULL, 1, NOW()),
(16, 'Ramos', 'Milagros', 'O', 'F', 55, '1969-09-12', '6785432109', 0, '2024-07-20', 1, 0, NULL, NULL, 0, 0, 1, 0, 0, 0, 0, 0, 0, NULL, NULL, 1, NOW()),
(17, 'Martinez', 'Francisco', 'P', 'M', 66, '1958-05-08', '7896543210', 1, '2023-04-15', 1, 1, 'FBS', 165.00, 0, 0, 0, 1, 0, 0, 0, 0, 1, 'Uncontrolled', NULL, 1, NOW()),
(18, 'dela Cruz', 'Soledad', 'Q', 'F', 59, '1965-12-15', '8907654321', 1, '2024-02-10', 1, 0, NULL, NULL, 1, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, 1, NOW()),
(19, 'Santos', 'Raul', 'R', 'M', 62, '1962-02-28', '9018765432', 0, '2024-08-25', 0, 1, 'FBS', 135.00, 0, 0, 0, 0, 0, 0, 1, 1, 0, NULL, NULL, 1, NOW()),
(20, 'Garcia', 'Felicidad', 'S', 'F', 64, '1960-06-18', '0129876543', 1, '2023-03-30', 1, 1, 'FBS', 128.00, 0, 0, 1, 0, 0, 0, 1, 0, 0, 'Stable', NULL, 1, NOW());

-- =====================================================
-- NTP Client Monitoring (10 samples)
-- =====================================================
INSERT IGNORE INTO `ntp_client_monitoring` (`patient_complete_name`, `age`, `sex`, `barangay_address`, `tb_case_no`, `date_tx_started`, `date_exam_before_tx`, `registration_type`, `initial_weight`, `weight_month_1`, `weight_month_2`, `weight_month_3`, `weight_month_4`, `weight_month_5`, `weight_month_6`, `disease_classification`, `end_of_treatment`, `outcome`, `patient_id`, `bhw_id`, `created_at`) VALUES
('Juan dela Cruz', 39, 'M', 'Barangay 1, Poblacion', 'TB2024-001', '2024-07-05', '2024-07-01', 'New', 58.5, 59.2, 60.1, 60.8, 61.5, 62.0, 62.8, 'Pulmonary TB', '2024-12-31', 'Cured', NULL, 1, NOW()),
('Maria Santos', 34, 'F', 'Barangay 2, San Jose', 'TB2024-002', '2024-08-15', '2024-08-10', 'New', 52.0, 52.8, 53.5, 54.2, NULL, NULL, NULL, 'Pulmonary TB', NULL, 'On Treatment', NULL, 1, NOW()),
('Pedro Garcia', 46, 'M', 'Barangay 3, Santa Cruz', 'TB2024-003', '2024-06-20', '2024-06-15', 'Relapsed', 61.0, 61.5, 62.3, 63.0, 63.8, 64.5, 65.2, 'Pulmonary TB', '2024-11-30', 'Cured', NULL, 1, NOW()),
('Ana Reyes', 36, 'F', 'Barangay 4, San Pedro', 'TB2024-004', '2024-09-05', '2024-09-01', 'New', 48.5, 49.0, 49.8, 50.5, NULL, NULL, NULL, 'Pulmonary TB', NULL, 'On Treatment', NULL, 1, NOW()),
('Carlos Mendoza', 42, 'M', 'Barangay 5, San Antonio', 'TB2024-005', '2024-05-25', '2024-05-20', 'New', 65.0, 65.8, 66.5, 67.2, 68.0, 68.8, 69.5, 'Extra-pulmonary TB', '2024-10-31', 'Cured', NULL, 1, NOW()),
('Rosa Torres', 29, 'F', 'Barangay 1, Poblacion', 'TB2024-006', '2024-10-15', '2024-10-10', 'New', 50.0, 50.5, 51.2, NULL, NULL, NULL, NULL, 'Pulmonary TB', NULL, 'On Treatment', NULL, 1, NOW()),
('Miguel Cruz', 49, 'M', 'Barangay 2, San Jose', 'TB2024-007', '2024-04-10', '2024-04-05', 'Relapsed', 59.0, 59.8, 60.5, 61.2, 61.8, 62.5, 63.2, 'Pulmonary TB', '2024-09-30', 'Treatment Completed', NULL, 1, NOW()),
('Elena Martinez', 32, 'F', 'Barangay 3, Santa Cruz', 'TB2024-008', '2024-11-05', '2024-11-01', 'New', 47.5, 48.2, NULL, NULL, NULL, NULL, NULL, 'Pulmonary TB', NULL, 'On Treatment', NULL, 1, NOW()),
('Roberto Ramos', 44, 'M', 'Barangay 4, San Pedro', 'TB2024-009', '2024-07-25', '2024-07-20', 'New', 63.0, 63.5, 64.2, 65.0, 65.8, 66.5, 67.2, 'Pulmonary TB', '2024-12-20', 'Cured', NULL, 1, NOW()),
('Sophia Garcia', 37, 'F', 'Barangay 5, San Antonio', 'TB2024-010', '2024-09-20', '2024-09-15', 'New', 51.0, 51.8, 52.5, 53.2, NULL, NULL, NULL, 'Pulmonary TB', NULL, 'On Treatment', NULL, 1, NOW());

-- =====================================================
-- WRA Tracking (15 samples)
-- =====================================================
INSERT IGNORE INTO `wra_tracking` (`name`, `age`, `birthdate`, `is_nhts`, `complete_address`, `contact_number`, `tracking_year`, `status_jan`, `status_feb`, `status_mar`, `status_apr`, `status_may`, `status_jun`, `status_jul`, `status_aug`, `status_sep`, `status_oct`, `status_nov`, `status_dec`, `family_planning_method`, `remarks`, `patient_id`, `bhw_id`, `created_at`) VALUES
('Maria Clara Santos', 29, '1995-03-10', 1, 'Barangay 1, Poblacion', '09171234567', 2024, 'P', 'P', 'P', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Pills', 'Pregnant Jan-Mar', NULL, 1, NOW()),
('Ana Rose Cruz', 32, '1992-07-15', 0, 'Barangay 2, San Jose', '09182345678', 2024, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Injectable', NULL, NULL, 1, NOW()),
('Lucia Garcia', 26, '1998-11-20', 1, 'Barangay 3, Santa Cruz', '09193456789', 2024, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'P', 'P', 'P', 'P', 'P', 'None', 'Pregnant starting Aug', NULL, 1, NOW()),
('Carmen Reyes', 34, '1990-02-28', 0, 'Barangay 4, San Pedro', '09204567890', 2024, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'IUD', NULL, NULL, 1, NOW()),
('Rosa Mendoza', 28, '1996-06-12', 1, 'Barangay 5, San Antonio', '09215678901', 2024, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Pills', NULL, NULL, 1, NOW()),
('Elena Torres', 30, '1994-09-05', 0, 'Barangay 1, Poblacion', '09226789012', 2024, 'P', 'P', 'P', 'P', 'P', 'P', 'L', 'N', 'N', 'N', 'N', 'N', 'None', 'Live birth July', NULL, 1, NOW()),
('Sofia Martinez', 27, '1997-12-18', 1, 'Barangay 2, San Jose', '09237890123', 2024, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Implant', NULL, NULL, 1, NOW()),
('Isabel Ramos', 31, '1993-04-22', 0, 'Barangay 3, Santa Cruz', '09248901234', 2024, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Injectable', NULL, NULL, 1, NOW()),
('Gloria dela Cruz', 33, '1991-08-30', 1, 'Barangay 4, San Pedro', '09259012345', 2024, 'N', 'N', 'N', 'N', 'P', 'P', 'P', 'P', 'P', 'P', 'P', 'P', 'None', 'Pregnant May onwards', NULL, 1, NOW()),
('Patricia Santos', 25, '1999-01-15', 0, 'Barangay 5, San Antonio', '09260123456', 2024, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Pills', NULL, NULL, 1, NOW()),
('Angelina Garcia', 29, '1995-05-25', 1, 'Barangay 1, Poblacion', '09271234567', 2024, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'IUD', NULL, NULL, 1, NOW()),
('Victoria Cruz', 32, '1992-10-10', 0, 'Barangay 2, San Jose', '09282345678', 2024, 'P', 'P', 'P', 'P', 'P', 'P', 'P', 'P', 'P', 'L', 'N', 'N', 'None', 'Live birth Oct', NULL, 1, NOW()),
('Camila Reyes', 28, '1996-03-08', 1, 'Barangay 3, Santa Cruz', '09293456789', 2024, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Injectable', NULL, NULL, 1, NOW()),
('Valentina Mendoza', 26, '1998-07-14', 0, 'Barangay 4, San Pedro', '09204567890', 2024, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Implant', NULL, NULL, 1, NOW()),
('Gabriela Torres', 30, '1994-11-28', 1, 'Barangay 5, San Antonio', '09215678901', 2024, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Pills', NULL, NULL, 1, NOW());

-- =====================================================
-- Inventory Categories (Sample categories)
-- =====================================================
INSERT IGNORE INTO `inventory_categories` (`category_name`, `created_at`) VALUES
('Antibiotics', NOW()),
('Pain Relievers', NOW()),
('Vitamins & Supplements', NOW()),
('First Aid Supplies', NOW()),
('Maternal & Child Care', NOW()),
('Chronic Disease Meds', NOW()),
('Emergency Supplies', NOW());

-- =====================================================
-- Medication Inventory (Sample medicines)
-- =====================================================
INSERT IGNORE INTO `medication_inventory` (`item_name`, `description`, `quantity_in_stock`, `unit`, `stock_alert_limit`, `expiry_date`) VALUES
('Paracetamol 500mg', 'For fever and mild pain', 500, 'tablets', 50, '2025-12-31'),
('Amoxicillin 500mg', 'Antibiotic for bacterial infections', 200, 'capsules', 30, '2025-06-30'),
('Mefenamic Acid 500mg', 'Pain reliever', 300, 'tablets', 40, '2025-09-30'),
('Multivitamins', 'Daily vitamin supplement', 400, 'tablets', 50, '2026-03-31'),
('Vitamin C 500mg', 'Immune system support', 500, 'tablets', 60, '2025-12-31'),
('Ferrous Sulfate', 'Iron supplement for anemia', 250, 'tablets', 30, '2025-08-31'),
('Losartan 50mg', 'For hypertension', 200, 'tablets', 30, '2025-07-31'),
('Metformin 500mg', 'For type 2 diabetes', 300, 'tablets', 40, '2025-10-31'),
('Amlodipine 5mg', 'For high blood pressure', 200, 'tablets', 25, '2025-11-30'),
('ORS Sachets', 'Oral rehydration solution', 100, 'sachets', 20, '2026-01-31'),
('Bandages (Assorted)', 'First aid wound dressing', 50, 'packs', 10, '2027-12-31'),
('Alcohol 70%', 'Antiseptic disinfectant', 30, 'bottles', 10, '2026-06-30');

-- Success message
SELECT 'Health Records and Inventory seeder data inserted successfully! (Duplicates were ignored)' AS message;
