-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2026 at 03:24 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.5.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tacunan_hims_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `scheduled_by` bigint(20) UNSIGNED NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time DEFAULT NULL,
  `service_type` varchar(50) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `status` enum('scheduled','attended','cancelled','missed') NOT NULL DEFAULT 'scheduled',
  `status_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `scheduled_by`, `appointment_date`, `appointment_time`, `service_type`, `purpose`, `status`, `status_notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2026-09-20', '09:00:00', 'cvd_screening', '2-week BP re-check and maintenance medication refill', 'scheduled', 'Bring empty medicine blister pack and PhilHealth ID.', '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(2, 2, 3, '2026-09-30', '10:00:00', 'prenatal_care', '3rd Prenatal Checkup and Gestational Diabetes Screen', 'scheduled', 'Bring maternal health record booklet (Pink book).', '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(3, 3, 2, '2026-09-20', '08:30:00', 'immunization', 'Pentavalent Dose 3 and OPV Dose 3 + IPV injection', 'scheduled', 'Bring Child Immunization Record (Yellow Card).', '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(4, 4, 2, '2026-10-13', '09:00:00', 'philpen', 'Monthly FBS test and maintenance medication refill', 'scheduled', 'Fasting 8-10 hours prior to morning blood extraction.', '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(5, 5, 3, '2026-10-20', '11:00:00', 'family_planning', 'Quarterly DMPA Injectable Re-injection', 'scheduled', 'Bring Family Planning Card.', '2026-09-20 04:02:58', '2026-09-20 04:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(100) NOT NULL,
  `record_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'LOGIN', 'Auth', NULL, 'User logged in to the system', '127.0.0.1', '2026-09-21 03:34:27'),
(2, 1, 'LOGIN', 'Auth', NULL, 'User logged in to the system', '127.0.0.1', '2026-09-21 19:32:12');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `health_assessments`
--

CREATE TABLE `health_assessments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `assessment_date` date NOT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `bmi` decimal(4,1) DEFAULT NULL,
  `systolic_bp` smallint(5) UNSIGNED DEFAULT NULL,
  `diastolic_bp` smallint(5) UNSIGNED DEFAULT NULL,
  `pulse_rate` smallint(5) UNSIGNED DEFAULT NULL,
  `respiratory_rate` smallint(5) UNSIGNED DEFAULT NULL,
  `temperature_celsius` decimal(4,2) DEFAULT NULL,
  `nutritional_status` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `health_assessments`
--

INSERT INTO `health_assessments` (`id`, `patient_id`, `user_id`, `assessment_date`, `weight_kg`, `height_cm`, `bmi`, `systolic_bp`, `diastolic_bp`, `pulse_rate`, `respiratory_rate`, `temperature_celsius`, `nutritional_status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2026-09-06', 74.00, 168.00, 26.2, 140, 90, 78, 18, 36.60, 'Overweight', 'Patient reports occasional morning occipital headache. Advised low-salt diet.', '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(2, 2, 3, '2026-08-31', 58.50, 155.00, 24.3, 110, 70, 74, 16, 36.50, 'Normal', '2nd Trimester Prenatal checkup. Fetal heart tone good.', '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(3, 3, 2, '2026-08-21', 7.40, 66.00, 17.0, NULL, NULL, 110, 30, 36.70, 'Underweight', '6-month well baby check. Active, alert, good milestones.', '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(4, 4, 2, '2026-09-13', 62.00, 150.00, 27.6, 130, 85, 76, 17, 36.40, 'Overweight', 'Senior citizen PhilPEN health assessment. Known Type 2 Diabetes.', '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(5, 5, 3, '2026-07-22', 54.00, 153.00, 23.1, 115, 75, 72, 16, 36.50, 'Normal', 'FP client for routine DMPA re-injection.', '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(6, 6, 4, '2026-09-15', 13.80, 94.00, 15.6, NULL, NULL, 95, 24, 36.60, 'Underweight', 'Operation Timbang (OPT) Plus quarterly child measurement.', '2026-09-20 04:02:58', '2026-09-20 04:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `health_service_records`
--

CREATE TABLE `health_service_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `service_date` date NOT NULL,
  `complaint_or_reason` text DEFAULT NULL,
  `findings_and_notes` text DEFAULT NULL,
  `service_specific_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`service_specific_data`)),
  `next_follow_up_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `health_service_records`
--

INSERT INTO `health_service_records` (`id`, `patient_id`, `user_id`, `service_type`, `service_date`, `complaint_or_reason`, `findings_and_notes`, `service_specific_data`, `next_follow_up_date`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'cvd_screening', '2026-09-06', 'Routine adult blood pressure check', 'Elevated BP 140/90. Stage 1 Hypertension. Prescribed Losartan 50mg OD.', '{\"has_hypertension_history\":\"yes\",\"has_diabetes_history\":\"no\",\"smoker_status\":\"non_smoker\",\"systolic_bp\":140,\"diastolic_bp\":90,\"cvd_risk_level\":\"Moderate Risk (10-20%)\"}', '2026-09-20', '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(2, 2, 3, 'prenatal_care', '2026-08-31', '2nd Trimester regular follow-up', 'Gravida 1 Para 0. AOG 24 weeks. Fundic height 23cm. FHT 142 bpm. Prescribed Ferrous Sulfate with Folic Acid.', '{\"gravida\":\"1\",\"para\":\"0\",\"lmp\":\"2026-04-05\",\"edc\":\"2027-01-10\",\"trimester\":\"2nd Trimester\",\"fundic_height_cm\":23,\"fetal_heart_tone\":\"142 bpm\",\"tetanus_toxoid_status\":\"Td 2 Given\"}', '2026-09-30', '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(3, 3, 2, 'immunization', '2026-08-21', 'Routine EPI Infant Immunization', 'Administered Pentavalent Vaccine (Dose 2) left anterolateral thigh, OPV 2 oral drops.', '{\"vaccine_administered\":\"Pentavalent + OPV\",\"dose_sequence\":\"Dose 2\",\"batch_lot_number\":\"LOT-2026-PENT-001\",\"site_of_injection\":\"Left vastus lateralis (IM)\",\"adverse_events\":\"None observed in 15 min monitoring\"}', '2026-09-20', '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(4, 4, 2, 'philpen', '2026-09-13', 'Annual PhilPEN Non-Communicable Disease Risk Evaluation', 'FBS: 126 mg/dL. Moderately controlled. Refilled Metformin 500mg #60.', '{\"tobacco_use\":\"Non-smoker\",\"alcohol_consumption\":\"None\",\"fasting_blood_sugar\":\"126 mg\\/dL\",\"total_cholesterol\":\"190 mg\\/dL\",\"risk_stratification\":\"Low to Moderate (<10%)\"}', '2026-10-13', '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(5, 5, 3, 'family_planning', '2026-07-22', 'Family planning renewal', 'Current user, no complaints or side effects. Administered DMPA 150mg/mL IM right deltoid.', '{\"client_type\":\"Current User\",\"method_accepted\":\"DMPA (Injectable)\",\"drop_out_reason\":null,\"source\":\"Barangay Health Center\"}', '2026-10-20', '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(6, 6, 4, 'bns_program', '2026-09-15', 'Quarterly BNS nutrition and growth monitoring', 'Normal nutritional status for age. Administered Vitamin A capsule 200,000 IU and Albendazole 400mg deworming tablet.', '{\"target_group\":\"Preschool child (12-59 months)\",\"weight_for_age\":\"Normal\",\"height_for_age\":\"Normal\",\"weight_for_length\":\"Normal\",\"vitamin_a_given\":\"Yes (200,000 IU)\",\"deworming_given\":\"Yes (Albendazole 400mg)\"}', '2026-12-20', '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(7, 7, 2, 'ntp_tb', '2026-09-08', 'Cough for 3 weeks and afternoon chills', 'Presumptive TB evaluated. GeneXpert test performed: MTB NOT DETECTED (Negative). Diagnosed with Acute Bronchitis. Prescribed Amoxicillin 500mg TID x 7 days.', '{\"tb_presumptive\":\"Yes\",\"cough_duration\":\"3 weeks\",\"sputum_genexpert_result\":\"MTB Not Detected (Negative)\",\"chest_xray_findings\":\"Clear lung fields\",\"treatment_regimen\":\"Symptomatic \\/ Antibiotics for Bronchitis\"}', NULL, '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(8, 8, 5, 'purok_kalusugan', '2026-09-17', 'Purok Kalusugan community home visit', 'Household health survey completed. Environmental sanitation check (clean water source, sanitary toilet, dengue prevention/water container inspection). Health education on proper handwashing provided.', '{\"activity_type\":\"Household Health Education & Sanitation Visit\",\"household_members_screened\":4,\"sanitation_status\":\"Satisfactory (Water sealed toilet, covered water storage)\",\"health_education_topic\":\"Dengue 4S Strategy & Hand Hygiene\"}', NULL, '2026-09-20 04:02:58', '2026-09-20 04:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_batches`
--

CREATE TABLE `inventory_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `batch_number` varchar(100) NOT NULL,
  `date_received` date NOT NULL,
  `expiration_date` date NOT NULL,
  `quantity_received` int(10) UNSIGNED NOT NULL,
  `current_quantity` int(10) UNSIGNED NOT NULL,
  `supplier_or_source` varchar(150) DEFAULT NULL,
  `status` enum('active','depleted','expired') NOT NULL DEFAULT 'active',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_batches`
--

INSERT INTO `inventory_batches` (`id`, `inventory_item_id`, `batch_number`, `date_received`, `expiration_date`, `quantity_received`, `current_quantity`, `supplier_or_source`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 'AMX-2026-01', '2026-08-19', '2027-11-19', 500, 350, 'City Health Office (CHO) Davao', 'active', NULL, '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(2, 2, 'PCM-2026-A1', '2026-07-19', '2026-11-03', 200, 45, 'City Health Office (CHO) Davao', 'active', NULL, '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(3, 3, 'LST-2026-88', '2026-08-29', '2028-03-19', 300, 260, 'DOH Regional Office XI', 'active', NULL, '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(4, 4, 'ORS-2026-B', '2026-08-19', '2027-09-19', 150, 120, 'City Health Office (CHO) Davao', 'active', NULL, '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(5, 5, 'BCG-2026-09', '2026-09-05', '2027-05-19', 30, 24, 'DOH Cold Chain Facility', 'active', NULL, '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(6, 6, 'PENT-2026-44', '2026-08-19', '2027-07-19', 40, 32, 'DOH Cold Chain Facility', 'active', NULL, '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(7, 7, 'MMR-2026-12', '2026-07-19', '2027-03-19', 25, 18, 'City Health Office (CHO) Davao', 'active', NULL, '2026-09-19 07:32:26', '2026-09-19 07:32:26');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `generic_name` varchar(150) DEFAULT NULL,
  `category` enum('medicine','vaccine') NOT NULL,
  `dosage_form` varchar(100) DEFAULT NULL,
  `unit_of_measure` varchar(50) NOT NULL,
  `minimum_stock_alert` int(10) UNSIGNED NOT NULL DEFAULT 20,
  `is_cold_chain` tinyint(1) NOT NULL DEFAULT 0,
  `storage_temperature_note` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`id`, `item_code`, `name`, `generic_name`, `category`, `dosage_form`, `unit_of_measure`, `minimum_stock_alert`, `is_cold_chain`, `storage_temperature_note`, `description`, `created_at`, `updated_at`) VALUES
(1, 'MED-AMX-500', 'Amoxicillin 500mg', 'Amoxicillin Trihydrate', 'medicine', 'Capsule', 'Capsule', 100, 0, 'Store at temperature not exceeding 30°C', 'First-line oral antibiotic for bacterial infections', '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(2, 'MED-PCM-500', 'Paracetamol 500mg', 'Paracetamol', 'medicine', 'Tablet', 'Tablet', 150, 0, 'Store below 30°C', 'Antipyretic and analgesic for pain and fever', '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(3, 'MED-LST-50', 'Losartan Potassium 50mg', 'Losartan Potassium', 'medicine', 'Tablet', 'Tablet', 50, 0, 'Store at 15°C to 30°C', 'Antihypertensive maintenance medication', '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(4, 'MED-ORS-S1', 'Oral Rehydration Salts', 'Oral Rehydration Salts', 'medicine', 'Powder for Oral Solution', 'Sachet', 50, 0, 'Store in dry place below 30°C', 'Electrolyte replenishment for acute diarrhea and dehydration', '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(5, 'VAC-BCG-01', 'BCG Vaccine', 'Bacillus Calmette-Guérin Vaccine', 'vaccine', 'Freeze-dried powder with diluent', 'Vial', 10, 1, 'Cold Chain: +2°C to +8°C (Protect from light)', 'Tuberculosis vaccine given at birth / early infancy', '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(6, 'VAC-PENT-01', 'Pentavalent Vaccine', 'DTP-HepB-Hib Vaccine', 'vaccine', 'Liquid suspension', 'Vial', 15, 1, 'Cold Chain: +2°C to +8°C (Do Not Freeze)', 'Protection against Diphtheria, Tetanus, Pertussis, Hepatitis B, and Hib', '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(7, 'VAC-MMR-01', 'MMR Vaccine', 'Measles, Mumps, and Rubella Vaccine', 'vaccine', 'Lyophilized powder with diluent', 'Vial', 15, 1, 'Cold Chain: +2°C to +8°C', 'Routine childhood immunization for MMR', '2026-09-19 07:32:26', '2026-09-19 07:32:26');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transactions`
--

CREATE TABLE `inventory_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_batch_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transaction_type` enum('received','dispensed','disposed_expired','adjustment') NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `transaction_date` date NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_transactions`
--

INSERT INTO `inventory_transactions` (`id`, `inventory_item_id`, `inventory_batch_id`, `user_id`, `patient_id`, `transaction_type`, `quantity`, `transaction_date`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, NULL, 'received', 500, '2026-08-19', 'Initial stock intake from City Health Office (CHO) Davao', '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(2, 2, 2, 1, NULL, 'received', 200, '2026-07-19', 'Initial stock intake from City Health Office (CHO) Davao', '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(3, 3, 3, 1, NULL, 'received', 300, '2026-08-29', 'Initial stock intake from DOH Regional Office XI', '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(4, 4, 4, 1, NULL, 'received', 150, '2026-08-19', 'Initial stock intake from City Health Office (CHO) Davao', '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(5, 5, 5, 1, NULL, 'received', 30, '2026-09-05', 'Initial stock intake from DOH Cold Chain Facility', '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(6, 6, 6, 1, NULL, 'received', 40, '2026-08-19', 'Initial stock intake from DOH Cold Chain Facility', '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(7, 7, 7, 1, NULL, 'received', 25, '2026-07-19', 'Initial stock intake from City Health Office (CHO) Davao', '2026-09-19 07:32:26', '2026-09-19 07:32:26');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_09_19_152734_create_puroks_table', 1),
(5, '2026_09_19_152807_create_patients_table', 1),
(6, '2026_09_19_152808_create_health_assessments_table', 1),
(7, '2026_09_19_152809_create_health_service_records_table', 1),
(8, '2026_09_19_152810_create_appointments_table', 1),
(9, '2026_09_19_152811_create_inventory_items_table', 1),
(10, '2026_09_19_152812_create_inventory_batches_table', 1),
(11, '2026_09_19_152813_create_inventory_transactions_table', 1),
(13, '2026_09_19_152814_create_audit_logs_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_control_number` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `date_of_birth` date NOT NULL,
  `civil_status` varchar(30) DEFAULT NULL,
  `purok_id` bigint(20) UNSIGNED NOT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(30) DEFAULT NULL,
  `philhealth_number` varchar(50) DEFAULT NULL,
  `blood_type` varchar(10) DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_number` varchar(30) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `patient_control_number`, `first_name`, `middle_name`, `last_name`, `suffix`, `sex`, `date_of_birth`, `civil_status`, `purok_id`, `street_address`, `contact_number`, `philhealth_number`, `blood_type`, `emergency_contact_name`, `emergency_contact_number`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'TAC-2026-0001', 'Juan', 'Mercado', 'Dela Cruz', NULL, 'Male', '1981-04-12', 'Married', 3, 'Km. 14, Purok 3', '09171234567', '01-234567890-1', 'O+', 'Maria Dela Cruz (Spouse)', '09171234568', 5, '2026-09-20 04:02:57', '2026-09-20 04:02:57'),
(2, 'TAC-2026-0002', 'Maria Clara', 'Reyes', 'Santos', NULL, 'Female', '1998-09-24', 'Married', 1, 'Near Chapel, Purok 1', '09289876543', '02-345678901-2', 'A+', 'Crisostomo Santos (Husband)', '09289876544', 3, '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(3, 'TAC-2026-0003', 'Ethan Gabriel', 'Cruz', 'Reyes', NULL, 'Male', '2026-03-20', 'Single', 2, 'Block 4, Purok 2', '09195554321', NULL, 'B+', 'Elena Reyes (Mother)', '09195554321', 2, '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(4, 'TAC-2026-0004', 'Teresa', 'Aquino', 'Bautista', NULL, 'Female', '1962-11-05', 'Widowed', 5, 'Purok 5, Upper Tacunan', '09391238901', '05-998877665-3', 'O+', 'Grace Bautista (Daughter)', '09391238902', 5, '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(5, 'TAC-2026-0005', 'Rosa', 'Dimagiba', 'Batungbakal', NULL, 'Female', '1992-06-18', 'Married', 4, 'Sitio Central, Purok 4', '09187778899', '04-112233445-4', 'AB+', 'Fernando Batungbakal (Husband)', '09187778800', 3, '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(6, 'TAC-2026-0006', 'Totoy', 'Cruz', 'Magtanggol', NULL, 'Male', '2023-09-20', 'Single', 6, 'Near Purok 6 Hall', '09223344556', NULL, 'O+', 'Lourdes Magtanggol (Mother)', '09223344556', 4, '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(7, 'TAC-2026-0007', 'Ricardo', 'Valdez', 'Dalisay', NULL, 'Male', '1985-02-14', 'Married', 7, 'Purok 7 Riverside', '09276543210', '07-334455667-7', 'B+', 'Alyana Dalisay (Spouse)', '09276543211', 2, '2026-09-20 04:02:58', '2026-09-20 04:02:58'),
(8, 'TAC-2026-0008', 'Elena', 'Manalo', 'Villanueva', NULL, 'Female', '1995-10-30', 'Single', 8, 'Purok 8 Extension', '09451122334', NULL, 'A+', 'Corazon Villanueva (Mother)', '09451122335', 5, '2026-09-20 04:02:58', '2026-09-20 04:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `puroks`
--

CREATE TABLE `puroks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `puroks`
--

INSERT INTO `puroks` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Purok 1', 'Barangay Tacunan, Davao City', '2026-09-19 07:32:23', '2026-09-19 07:32:23'),
(2, 'Purok 2', 'Barangay Tacunan, Davao City', '2026-09-19 07:32:23', '2026-09-19 07:32:23'),
(3, 'Purok 3', 'Barangay Tacunan, Davao City', '2026-09-19 07:32:23', '2026-09-19 07:32:23'),
(4, 'Purok 4', 'Barangay Tacunan, Davao City', '2026-09-19 07:32:23', '2026-09-19 07:32:23'),
(5, 'Purok 5', 'Barangay Tacunan, Davao City', '2026-09-19 07:32:23', '2026-09-19 07:32:23'),
(6, 'Purok 6', 'Barangay Tacunan, Davao City', '2026-09-19 07:32:23', '2026-09-19 07:32:23'),
(7, 'Purok 7', 'Barangay Tacunan, Davao City', '2026-09-19 07:32:23', '2026-09-19 07:32:23'),
(8, 'Purok 8', 'Barangay Tacunan, Davao City', '2026-09-19 07:32:23', '2026-09-19 07:32:23'),
(9, 'Purok 9', 'Barangay Tacunan, Davao City', '2026-09-19 07:32:23', '2026-09-19 07:32:23'),
(10, 'Purok 10', 'Barangay Tacunan, Davao City', '2026-09-19 07:32:23', '2026-09-19 07:32:23'),
(11, 'Purok 11', 'Barangay Tacunan, Davao City', '2026-09-19 07:32:23', '2026-09-19 07:32:23'),
(12, 'Purok 12', 'Barangay Tacunan, Davao City', '2026-09-19 07:32:23', '2026-09-19 07:32:23');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('LiS8OXk5xIIUwqnU9RKjBSLoWiskjTHzEmFFsqpZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJyZWFtWFFBSTl3RjZ0dzJkeUVKQ2taVmwwOHhWUnBtRVJrT05XYlA0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1790086313),
('PB4VPFqD7o8zm1SmonJcjZsTk7Hxj1ERlQ96v7rY', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJlR09VemRnRUxtcWxMSkVaYTFsUWtmRzA4UlFnUTNkZXJhRU1WNnRrIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2Rhc2hib2FyZCJ9LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1790069524),
('s2Jx49Lyq3D8KiEUXJU0E3wYPoXXcZKxILw5GLha', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJha0F2NjQwMDVUWlplNWNpRGJIMVVwQ284WUZNVzZBY2QxUWlvWm9sIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==', 1790052112);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'bhw',
  `contact_number` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `contact_number`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Tacunan Health Admin', 'admin@tacunan.gov.ph', NULL, '$2y$12$QZsdOMPm3MTnSrOGhhnH9.Kef3r.25Wl9maqrOJwXCATlrtdbNRyW', 'admin', '09171234567', 1, NULL, '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(2, 'Maria Santos, RN', 'nurse@tacunan.gov.ph', NULL, '$2y$12$XjKUAzhdsaQYVDPTEmU9meTVnZyydTR4B24niccAB8pgbvm49haum', 'nurse', '09181234567', 1, NULL, '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(3, 'Elena Reyes, RMW', 'midwife@tacunan.gov.ph', NULL, '$2y$12$DkiWHMHSwRmI5f7EcZg1T.gOSf6pgbzIft3CJfRFTrL5iDIcIE65G', 'midwife', '09191234567', 1, NULL, '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(4, 'Juana Dela Cruz (BNS)', 'bns@tacunan.gov.ph', NULL, '$2y$12$IO9lbP6g1dkhHNnswRbUk.wxsXD6bUfmh4k17mOAniPNCs/yEyWJ.', 'bns', '09201234567', 1, NULL, '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(5, 'Rosa Flores (BHW)', 'bhw@tacunan.gov.ph', NULL, '$2y$12$6F8cOwfD9hDCMPVHrOl/B.8DNa/dYxPzWGfNgta48SotADjtmQfEu', 'bhw', '09211234567', 1, NULL, '2026-09-19 07:32:26', '2026-09-19 07:32:26'),
(6, 'Carmen Lim (BHV)', 'bhv@tacunan.gov.ph', NULL, '$2y$12$Nke2v.k6ubQ9TyraccchWuRE3dq2L9ND0XXTCvMmne2uVADK3SJHy', 'bhv', '09221234567', 1, NULL, '2026-09-19 07:32:26', '2026-09-19 07:32:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointments_patient_id_foreign` (`patient_id`),
  ADD KEY `appointments_scheduled_by_foreign` (`scheduled_by`),
  ADD KEY `appointments_appointment_date_index` (`appointment_date`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_foreign` (`user_id`),
  ADD KEY `audit_logs_action_index` (`action`),
  ADD KEY `audit_logs_module_index` (`module`),
  ADD KEY `audit_logs_record_id_index` (`record_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `health_assessments`
--
ALTER TABLE `health_assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `health_assessments_patient_id_foreign` (`patient_id`),
  ADD KEY `health_assessments_user_id_foreign` (`user_id`);

--
-- Indexes for table `health_service_records`
--
ALTER TABLE `health_service_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `health_service_records_patient_id_foreign` (`patient_id`),
  ADD KEY `health_service_records_user_id_foreign` (`user_id`),
  ADD KEY `health_service_records_service_type_index` (`service_type`);

--
-- Indexes for table `inventory_batches`
--
ALTER TABLE `inventory_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_batches_inventory_item_id_foreign` (`inventory_item_id`),
  ADD KEY `inventory_batches_batch_number_index` (`batch_number`),
  ADD KEY `inventory_batches_expiration_date_index` (`expiration_date`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inventory_items_item_code_unique` (`item_code`),
  ADD KEY `inventory_items_category_index` (`category`);

--
-- Indexes for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_transactions_inventory_item_id_foreign` (`inventory_item_id`),
  ADD KEY `inventory_transactions_inventory_batch_id_foreign` (`inventory_batch_id`),
  ADD KEY `inventory_transactions_user_id_foreign` (`user_id`),
  ADD KEY `inventory_transactions_patient_id_foreign` (`patient_id`),
  ADD KEY `inventory_transactions_transaction_type_index` (`transaction_type`),
  ADD KEY `inventory_transactions_transaction_date_index` (`transaction_date`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patients_patient_control_number_unique` (`patient_control_number`),
  ADD KEY `patients_purok_id_foreign` (`purok_id`),
  ADD KEY `patients_created_by_foreign` (`created_by`);

--
-- Indexes for table `puroks`
--
ALTER TABLE `puroks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `puroks_name_unique` (`name`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `health_assessments`
--
ALTER TABLE `health_assessments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `health_service_records`
--
ALTER TABLE `health_service_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `inventory_batches`
--
ALTER TABLE `inventory_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `puroks`
--
ALTER TABLE `puroks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_scheduled_by_foreign` FOREIGN KEY (`scheduled_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `health_assessments`
--
ALTER TABLE `health_assessments`
  ADD CONSTRAINT `health_assessments_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `health_assessments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `health_service_records`
--
ALTER TABLE `health_service_records`
  ADD CONSTRAINT `health_service_records_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `health_service_records_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `inventory_batches`
--
ALTER TABLE `inventory_batches`
  ADD CONSTRAINT `inventory_batches_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD CONSTRAINT `inventory_transactions_inventory_batch_id_foreign` FOREIGN KEY (`inventory_batch_id`) REFERENCES `inventory_batches` (`id`),
  ADD CONSTRAINT `inventory_transactions_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`),
  ADD CONSTRAINT `inventory_transactions_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `patients_purok_id_foreign` FOREIGN KEY (`purok_id`) REFERENCES `puroks` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
