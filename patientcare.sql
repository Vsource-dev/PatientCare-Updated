-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 28, 2025 at 11:32 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `patientcare`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password`, `name`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin@patientcare.com', '$2y$10$83FylGM1w2RH8mGylCYkLuJwHbn7fR3DoY1OMkvqC2juV0TbC2NBW', 'Admin', NULL, '2025-05-09 13:13:25', '2025-06-15 20:12:40');

-- --------------------------------------------------------

--
-- Table structure for table `admission_details`
--

CREATE TABLE `admission_details` (
  `admission_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `admission_date` datetime DEFAULT NULL,
  `admission_type` varchar(50) NOT NULL,
  `admission_source` varchar(50) NOT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `doctor_id` int(10) UNSIGNED DEFAULT NULL,
  `room_number` varchar(20) NOT NULL,
  `bed_number` varchar(20) NOT NULL,
  `admission_notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admission_details`
--

INSERT INTO `admission_details` (`admission_id`, `patient_id`, `admission_date`, `admission_type`, `admission_source`, `department_id`, `doctor_id`, `room_number`, `bed_number`, `admission_notes`, `created_at`, `updated_at`) VALUES
(69, 33, '2025-08-22 23:07:47', 'Normal', 'ER', 27, 33, '101', '101-B1', 's', '2025-08-22 23:07:47', '2025-08-22 23:07:47'),
(70, 34, '2025-08-27 08:38:05', 'Inpatient', 'ER', 27, 33, '101', '101-B3', 'wAG PAKAININ', '2025-08-27 08:38:05', '2025-08-27 08:38:05'),
(71, 35, '2025-08-27 09:42:58', 'Inpatient', 'ER', 27, 33, '101', '101-B2', NULL, '2025-08-27 09:42:58', '2025-08-27 09:42:58');

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `bill_item_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `message` varchar(1000) DEFAULT NULL,
  `actor` varchar(150) NOT NULL,
  `icon` varchar(50) NOT NULL DEFAULT 'fa-info-circle',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `beds`
--

CREATE TABLE `beds` (
  `bed_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `bed_number` varchar(20) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'available',
  `rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `patient_id` int(11) DEFAULT NULL COMMENT 'Which patient is currently assigned to this bed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beds`
--

INSERT INTO `beds` (`bed_id`, `room_id`, `bed_number`, `status`, `rate`, `created_at`, `updated_at`, `patient_id`) VALUES
(128, 40, '101-B1', 'occupied', 0.00, '2025-08-20 12:41:12', '2025-08-22 15:07:47', 33),
(129, 40, '101-B2', 'occupied', 0.00, '2025-08-20 12:41:12', '2025-08-27 01:42:58', 35),
(130, 40, '101-B3', 'occupied', 0.00, '2025-08-20 12:41:12', '2025-08-27 00:38:05', 34),
(131, 41, '143-B1', 'available', 200.00, '2025-08-27 01:56:27', '2025-08-27 01:56:27', NULL),
(132, 41, '143-B2', 'available', 200.00, '2025-08-27 01:56:27', '2025-08-27 01:56:27', NULL),
(133, 41, '143-B3', 'available', 200.00, '2025-08-27 01:56:27', '2025-08-27 01:56:27', NULL),
(134, 41, '143-B4', 'available', 200.00, '2025-08-27 01:56:27', '2025-08-27 01:56:27', NULL),
(135, 41, '143-B5', 'available', 200.00, '2025-08-27 01:56:27', '2025-08-27 01:56:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `billing_information`
--

CREATE TABLE `billing_information` (
  `billing_info_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `guarantor_name` varchar(100) NOT NULL,
  `guarantor_relationship` varchar(50) NOT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing_information`
--

INSERT INTO `billing_information` (`billing_info_id`, `patient_id`, `guarantor_name`, `guarantor_relationship`, `payment_status`, `created_at`, `updated_at`) VALUES
(48, 33, 'Kyrie', 'Father', 'pending', '2025-08-22 23:07:47', '2025-08-22 23:07:47'),
(49, 34, 'Sam Gonzales', 'Diko alam saknya', 'pending', '2025-08-27 08:38:05', '2025-08-27 08:38:05'),
(50, 35, 'Kyrie', 'Irving', 'pending', '2025-08-27 09:42:58', '2025-08-27 09:42:58');

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `billing_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `admission_id` int(11) DEFAULT NULL,
  `billing_date` date NOT NULL,
  `payment_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`billing_id`, `patient_id`, `admission_id`, `billing_date`, `payment_status`) VALUES
(53, 33, 69, '2025-08-24', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `bill_items`
--

CREATE TABLE `bill_items` (
  `billing_item_id` int(11) NOT NULL,
  `billing_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `billing_date` date NOT NULL,
  `prescription_item_id` int(11) DEFAULT NULL,
  `assignment_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bill_items`
--

INSERT INTO `bill_items` (`billing_item_id`, `billing_id`, `amount`, `billing_date`, `prescription_item_id`, `assignment_id`, `service_id`, `quantity`, `discount_amount`, `status`) VALUES
(35, 53, 1400.00, '2025-08-24', NULL, NULL, 106, 1, 0.00, 'pending'),
(36, 53, 150.00, '2025-08-24', NULL, NULL, 109, 1, 0.00, 'pending'),
(41, 53, 15.00, '2025-08-24', NULL, NULL, 44, 1, 0.00, 'pending'),
(42, 53, 10.00, '2025-08-24', NULL, NULL, 111, 1, 0.00, 'pending'),
(43, 53, 180.00, '2025-08-24', NULL, NULL, 112, 1, 0.00, 'pending'),
(44, 53, 5.00, '2025-08-24', NULL, NULL, 43, 1, 0.00, 'pending'),
(45, 53, 6.00, '2025-08-24', NULL, NULL, 110, 1, 0.00, 'pending'),
(46, 53, 8.00, '2025-08-24', NULL, NULL, 105, 1, 0.00, 'pending'),
(47, 53, 5.00, '2025-08-24', NULL, NULL, 111, 1, 0.00, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `description`) VALUES
(22, 'Neurology', NULL),
(23, 'Orthopedics', NULL),
(24, 'Cardiology', NULL),
(25, 'Gynecology', NULL),
(26, 'Pediatrics ', NULL),
(27, 'General', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE `deposits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `deposited_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deposits`
--

INSERT INTO `deposits` (`id`, `patient_id`, `amount`, `deposited_at`, `created_at`, `updated_at`) VALUES
(1, 46, 233.00, '2025-07-23 00:00:00', '2025-07-22 23:36:48', '2025-07-22 23:36:48'),
(2, 33, 1000.00, '2025-07-23 00:00:00', '2025-07-22 23:37:08', '2025-07-22 23:37:08'),
(3, 6, 123.00, '2025-08-02 00:00:00', '2025-08-02 13:19:33', '2025-08-02 13:19:33'),
(4, 6, 22.00, '2025-08-02 00:00:00', '2025-08-02 13:22:30', '2025-08-02 13:22:30'),
(5, 6, 22.00, '2025-08-02 00:00:00', '2025-08-02 13:25:11', '2025-08-02 13:25:11');

-- --------------------------------------------------------

--
-- Table structure for table `disputes`
--

CREATE TABLE `disputes` (
  `dispute_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `disputable_id` bigint(20) UNSIGNED NOT NULL,
  `disputable_type` varchar(255) NOT NULL,
  `datetime` datetime NOT NULL,
  `reason` text NOT NULL,
  `status` varchar(50) NOT NULL,
  `approved_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `disputes`
--

INSERT INTO `disputes` (`dispute_id`, `patient_id`, `disputable_id`, `disputable_type`, `datetime`, `reason`, `status`, `approved_by`) VALUES
(1, 46, 0, '', '2025-07-13 12:04:09', 'dasdasd', 'pending', NULL),
(2, 46, 0, '', '2025-07-13 12:04:44', 'dasdasd', 'pending', NULL),
(3, 46, 0, '', '2025-07-13 12:06:40', 'dasdasd', 'pending', NULL),
(4, 46, 0, '', '2025-07-18 08:05:12', 'eqweqwe', 'pending', NULL),
(5, 6, 0, '', '2025-07-30 22:27:54', 'asdasd', 'pending', NULL),
(6, 9, 1, 'App\\Models\\ServiceAssignment', '2025-07-31 20:50:26', 'asdas', 'pending', NULL),
(7, 9, 5, 'App\\Models\\ServiceAssignment', '2025-07-31 20:52:23', 'asdas', 'pending', NULL),
(8, 9, 1, 'App\\Models\\ServiceAssignment', '2025-07-31 20:55:18', 'asdas', 'pending', NULL),
(9, 9, 1, 'App\\Models\\ServiceAssignment', '2025-07-31 20:57:29', 'asdas', 'pending', NULL),
(10, 9, 1, 'App\\Models\\ServiceAssignment', '2025-07-31 21:07:02', 'asdas', 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `doctor_name` varchar(100) NOT NULL,
  `doctor_specialization` varchar(100) DEFAULT NULL,
  `rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `department_id` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `user_id`, `doctor_name`, `doctor_specialization`, `rate`, `department_id`, `created_at`, `updated_at`) VALUES
(31, 48, 'Stan', 'IDK', 0.00, 27, '2025-08-22 12:53:24', '2025-08-22 13:03:45'),
(33, NULL, 'same', NULL, 2000.00, 27, '2025-08-22 22:16:39', '2025-08-22 22:16:39'),
(34, NULL, 'gamma', NULL, 1000.00, 23, '2025-08-22 22:24:21', '2025-08-22 22:24:21'),
(35, NULL, 'js', NULL, 0.00, 25, '2025-08-22 22:41:35', '2025-08-22 22:41:35'),
(36, NULL, 'sammyG', NULL, 200.00, 24, '2025-08-27 09:57:34', '2025-08-27 09:57:34');

-- --------------------------------------------------------

--
-- Table structure for table `hospital_services`
--

CREATE TABLE `hospital_services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `service_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospital_services`
--

INSERT INTO `hospital_services` (`service_id`, `service_name`, `price`, `quantity`, `description`, `service_type`) VALUES
(43, 'Paracetamol 500 mg Tablet', 5.00, 2000, 'Analgesic / antipyretic', 'medication'),
(44, 'Amoxicillin 500 mg Cap', 15.00, 1500, 'Broad-spectrum antibiotic', 'medication'),
(45, 'Ceftriaxone 1 g Vial', 180.00, 300, 'IV antibiotic', 'medication'),
(46, 'Complete Blood Count (CBC)', 250.00, 0, 'Hematology panel', 'lab'),
(47, 'Basic Metabolic Panel', 600.00, 0, 'Serum chemistry panel', 'lab'),
(48, 'Urinalysis', 180.00, 0, 'Routine UA', 'lab'),
(49, 'Comprehensive Metabolic Panel (CMP)', 800.00, 0, 'Full metabolic panel', 'lab'),
(50, 'Thyroid Function Tests', 950.00, 0, 'Thyroid hormone panel', 'lab'),
(51, 'Coagulation Panel', 600.00, 0, 'Coagulation studies', 'lab'),
(52, 'Basic Metabolic Panel (BMP)', 600.00, 0, 'Basic metabolic panel', 'lab'),
(53, 'Lipid Panel', 750.00, 0, 'Lipid profile', 'lab'),
(54, 'Hemoglobin A1C', 650.00, 0, 'Glycated hemoglobin test', 'lab'),
(55, 'Liver Function Tests', 700.00, 0, 'LFTs (AST, ALT, etc.)', 'lab'),
(56, 'C-Reactive Protein', 500.00, 0, 'Inflammation marker', 'lab'),
(57, 'Chest X-Ray (PA/LAT)', 1200.00, 0, 'Two-view chest X-ray', 'lab'),
(58, 'Abdominal Ultrasound', 1800.00, 0, 'Whole-abdomen ultrasound', 'lab'),
(59, 'CT Head (Non-contrast)', 4500.00, 0, 'Emergency CT scan', 'lab'),
(60, 'CT Scan – Head (Non-contrast)', 4500.00, 0, 'Non-contrast CT of head', 'lab'),
(61, 'CT Scan – Chest', 4000.00, 0, 'CT of chest', 'lab'),
(62, 'MRI – Brain', 12000.00, 0, 'MRI of brain', 'lab'),
(63, 'MRI – Spine', 12000.00, 0, 'MRI of spine', 'lab'),
(64, 'Ultrasound – Abdomen', 1800.00, 0, 'Abdominal ultrasound exam', 'lab'),
(97, 'Appendectomy (Open)', 30000.00, 0, 'Open appendectomy surgical removal of appendix', 'operation'),
(98, 'Appendectomy (Laparoscopic)', 60000.00, 0, 'Minimally invasive laparoscopic appendectomy', 'operation'),
(99, 'Cholecystectomy (Laparoscopic)', 80000.00, 0, 'Gallbladder removal via laparoscopy', 'operation'),
(100, 'Hernia Repair (Open)', 50000.00, 0, 'Surgical repair of hernia, open technique', 'operation'),
(101, 'Hysterectomy (Total Abdominal)', 60000.00, 0, 'Removal of uterus via open abdomen', 'operation'),
(102, 'Thyroidectomy (Partial)', 100000.00, 0, 'Partial thyroid removal surgery', 'operation'),
(103, 'Minor Procedure (General OR)', 16000.00, 0, 'General minor operation in operating room', 'operation'),
(104, 'Metformin 500 mg Tablet (Generic)', 5.00, 0, 'Oral antidiabetic; first-line therapy for type 2 diabetes', 'medication'),
(105, 'Glipizide 5 mg Tablet (Sulfonylurea)', 8.00, 0, 'Oral antidiabetic; sulfonylurea class', 'medication'),
(106, 'Insulin (Human), 10 ml vial', 350.00, 0, 'Injectable insulin for diabetes management', 'medication'),
(107, 'Atenolol 50 mg Tablet (Beta-blocker)', 12.00, 0, 'Cardiovascular – hypertension treatment', 'medication'),
(108, 'Atorvastatin 20 mg Tablet (Statin)', 30.00, 0, 'Lipid-lowering agent for hypercholesterolemia', 'medication'),
(109, 'Salbutamol Inhaler (100 mcg)', 150.00, 0, 'Bronchodilator for asthma/COPD', 'medication'),
(110, 'Cetirizine 10 mg Tablet (Antihistamine)', 6.00, 0, 'Allergy & rhinitis relief', 'medication'),
(111, 'Biogesic (Paracetamol) Tablet', 5.00, 0, 'Common OTC analgesic & antipyretic by Unilab', 'medication'),
(112, 'Tempra Suspension (Paracetamol) – Children', 45.00, 0, 'Kids OTC fever/pain solution', 'medication');

-- --------------------------------------------------------

--
-- Table structure for table `insurance_providers`
--

CREATE TABLE `insurance_providers` (
  `insurance_provider_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `insurance_providers`
--

INSERT INTO `insurance_providers` (`insurance_provider_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Blue Cross', '2025-05-12 06:28:33', '2025-06-10 01:17:14'),
(2, 'Medicare', '2025-05-12 06:28:33', '2025-06-10 01:17:14'),
(3, 'Medicaid', '2025-05-12 06:28:33', '2025-06-10 01:17:14'),
(4, 'Aetna', '2025-05-12 06:28:33', '2025-06-10 01:17:14'),
(5, 'Cigna', '2025-05-12 06:28:33', '2025-06-10 01:17:14'),
(6, 'Blue Cross', '2025-05-12 06:37:55', '2025-06-10 01:17:14'),
(7, 'Medicare', '2025-05-12 06:37:55', '2025-06-10 01:17:14'),
(8, 'Medicaid', '2025-05-12 06:37:55', '2025-06-10 01:17:14'),
(9, 'Aetna', '2025-05-12 06:37:55', '2025-06-10 01:17:14'),
(10, 'Cigna', '2025-05-12 06:37:55', '2025-06-10 01:17:14'),
(11, 'Blue Cross', '2025-05-12 06:38:19', '2025-06-10 01:17:14'),
(12, 'Medicare', '2025-05-12 06:38:19', '2025-06-10 01:17:14'),
(13, 'Medicaid', '2025-05-12 06:38:19', '2025-06-10 01:17:14'),
(14, 'Aetna', '2025-05-12 06:38:19', '2025-06-10 01:17:14'),
(15, 'Cigna', '2025-05-12 06:38:19', '2025-06-10 01:17:14'),
(16, 'Blue Cross', '2025-05-12 06:38:44', '2025-06-10 01:17:14'),
(17, 'Medicare', '2025-05-12 06:38:44', '2025-06-10 01:17:14'),
(18, 'Medicaid', '2025-05-12 06:38:44', '2025-06-10 01:17:14'),
(19, 'Aetna', '2025-05-12 06:38:44', '2025-06-10 01:17:14'),
(20, 'Cigna', '2025-05-12 06:38:44', '2025-06-10 01:17:14'),
(21, 'Blue Cross', '2025-05-12 06:40:15', '2025-06-10 01:17:14'),
(22, 'Medicare', '2025-05-12 06:40:15', '2025-06-10 01:17:14'),
(23, 'Medicaid', '2025-05-12 06:40:15', '2025-06-10 01:17:14'),
(24, 'Aetna', '2025-05-12 06:40:15', '2025-06-10 01:17:14'),
(25, 'Cigna', '2025-05-12 06:40:15', '2025-06-10 01:17:14'),
(27, 'DSD', '2025-06-10 01:19:00', '2025-06-09 17:19:00'),
(28, 'eqwe', '2025-06-10 01:35:47', '2025-06-09 17:35:47'),
(29, 'asdasd', '2025-06-15 11:53:22', '2025-06-15 03:53:22'),
(30, 'qwe', '2025-06-18 13:37:50', '2025-06-18 05:37:50'),
(31, 'dasdas', '2025-06-21 07:55:28', '2025-06-20 23:55:28'),
(32, 'qwewqe', '2025-06-23 20:49:09', '2025-06-23 12:49:09'),
(33, 'Sam', '2025-06-23 23:45:12', '2025-06-23 15:45:12'),
(34, 'bro', '2025-06-25 13:40:57', '2025-06-25 05:40:57'),
(35, 'qweqwe', '2025-06-25 14:03:21', '2025-06-25 06:03:21'),
(38, 'eqweqwe', '2025-07-06 11:49:29', '2025-07-06 03:49:29'),
(39, 'None', '2025-07-15 20:33:29', '2025-07-15 12:33:29');

-- --------------------------------------------------------

--
-- Table structure for table `medical_details`
--

CREATE TABLE `medical_details` (
  `medical_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `primary_reason` text NOT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `blood_pressure` varchar(20) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `heart_rate` int(11) DEFAULT NULL,
  `medical_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`medical_history`)),
  `allergies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allergies`)),
  `other_medical_history` text DEFAULT NULL,
  `other_allergies` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_details`
--

INSERT INTO `medical_details` (`medical_id`, `patient_id`, `primary_reason`, `temperature`, `blood_pressure`, `weight`, `height`, `heart_rate`, `medical_history`, `allergies`, `other_medical_history`, `other_allergies`, `created_at`, `updated_at`) VALUES
(66, 33, 'Walang sakit', 2.0, '123', 233.00, 199.00, 21, '\"{\\\"hypertension\\\":false,\\\"heart_disease\\\":false,\\\"copd\\\":false,\\\"diabetes\\\":false,\\\"asthma\\\":true,\\\"kidney_disease\\\":false,\\\"others\\\":null}\"', '{\"penicillin\":false,\"nsaids\":false,\"contrast_dye\":false,\"sulfa\":true,\"latex\":true,\"none\":false,\"others\":null}', NULL, NULL, '2025-08-22 23:07:47', '2025-08-22 23:07:47'),
(67, 34, 'Walang Pera Pangamot', 123.0, '20/120', 123.00, 123.00, 123, '\"{\\\"hypertension\\\":true,\\\"heart_disease\\\":true,\\\"copd\\\":true,\\\"diabetes\\\":false,\\\"asthma\\\":false,\\\"kidney_disease\\\":false,\\\"others\\\":null}\"', '{\"penicillin\":false,\"nsaids\":false,\"contrast_dye\":false,\"sulfa\":true,\"latex\":true,\"none\":false,\"others\":null}', NULL, NULL, '2025-08-27 08:38:05', '2025-08-27 08:38:05'),
(68, 35, 'Walang sakit', 100.0, '100', 100.00, 100.00, 100, '\"{\\\"hypertension\\\":true,\\\"heart_disease\\\":true,\\\"copd\\\":true,\\\"diabetes\\\":true,\\\"asthma\\\":true,\\\"kidney_disease\\\":true,\\\"others\\\":null}\"', '{\"penicillin\":true,\"nsaids\":true,\"contrast_dye\":true,\"sulfa\":true,\"latex\":true,\"none\":true,\"others\":null}', NULL, NULL, '2025-08-27 09:42:58', '2025-08-27 09:42:58');

-- --------------------------------------------------------

--
-- Table structure for table `medicine_stock_movements`
--

CREATE TABLE `medicine_stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` int(11) NOT NULL,
  `delta` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `miscellaneous_charges`
--

CREATE TABLE `miscellaneous_charges` (
  `id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','completed') NOT NULL DEFAULT 'pending',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `completed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `bill_item_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `miscellaneous_charges`
--

INSERT INTO `miscellaneous_charges` (`id`, `patient_id`, `service_id`, `quantity`, `unit_price`, `total`, `notes`, `status`, `created_by`, `completed_by`, `bill_item_id`, `created_at`, `updated_at`) VALUES
(4, 6, 73, 1, 600.00, 600.00, NULL, 'completed', 13, 13, NULL, '2025-08-02 00:06:31', '2025-08-02 00:06:37'),
(5, 8, 71, 2, 1000.00, 2000.00, NULL, 'completed', 13, 13, NULL, '2025-08-02 00:11:55', '2025-08-02 00:12:01'),
(6, 6, 72, 1, 850.00, 850.00, NULL, 'completed', 13, 13, NULL, '2025-08-02 00:20:28', '2025-08-02 00:20:44'),
(7, 8, 72, 2, 850.00, 1700.00, 'qweqwe', 'completed', 13, 13, NULL, '2025-08-02 00:21:03', '2025-08-02 00:21:28');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` varchar(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` longtext NOT NULL,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('0a5eb146-f94d-43c3-a38b-d34371a075cb', 'App\\Notifications\\LabChargeCreated', 'App\\Models\\Patient', 20, '{\"type\":\"Laboratory\",\"event\":\"Order Placed\",\"assignment_id\":17,\"service\":\"Urinalysis\",\"doctor\":\"Carol Taylor\",\"ordered_at\":\"2025-08-19 15:20:22\",\"message\":\"Your lab order for \\u201cUrinalysis\\u201d has been placed.\"}', NULL, '2025-08-19 15:20:22', '2025-08-19 15:20:22'),
('23cb0f48-6d33-4ae1-823a-8d23635ac6c4', 'App\\Notifications\\AdmissionCharged', 'App\\Models\\User', 37, '{\"type\":\"Billing\",\"title\":\"\\u20b11,500.00 in admission charges\",\"bed_rate\":500,\"room_rate\":1000,\"doctor_rate\":0,\"billing_item_id\":26}', NULL, '2025-07-20 22:37:15', '2025-07-20 22:37:15'),
('37431cc1-b693-4b69-aaad-05329b6cb28a', 'App\\Notifications\\AdmissionCharged', 'App\\Models\\User', 36, '{\"type\":\"Billing\",\"title\":\"Admission Charges Applied\",\"message\":\"You\'ve been charged \\u20b13,200.00 (Bed: \\u20b1500.00, Room: \\u20b11,200.00, Doctor: \\u20b11,500.00)\",\"bed_rate\":500,\"room_rate\":1200,\"doctor_rate\":1500}', '2025-07-20 22:25:49', '2025-07-20 22:19:31', '2025-07-20 22:25:49'),
('451a5cc8-d82a-4d76-8e49-1ccc5cce2672', 'App\\Notifications\\DepositReceived', 'App\\Models\\Patient', 6, '{\"type\":\"deposit\",\"deposit_id\":null,\"amount\":\"22\",\"deposited_at\":\"2025-08-02 00:00:00\",\"message\":\"A payment of \\u20b122.00 has been posted to your account.\"}', NULL, '2025-08-02 21:25:11', '2025-08-02 21:25:11'),
('46384d52-c6aa-4729-ad82-3c933a5c9c0c', 'App\\Notifications\\ORChargeCreated', 'App\\Models\\Patient', 6, '{\"type\":\"or_charge\",\"assignment_id\":15,\"service_name\":\"Physical Therapy Consultation\",\"amount\":\"700.00\",\"assigned_at\":\"2025-08-02 21:00:22\",\"message\":\"An OR charge for \\u201cPhysical Therapy Consultation\\u201d (\\u20b1700.00) was added.\"}', NULL, '2025-08-02 21:00:24', '2025-08-02 21:00:24'),
('5c6e2d0b-7943-465f-80a2-33594c2e50d1', 'App\\Notifications\\AdmissionCharged', 'App\\Models\\User', 36, '{\"type\":\"Billing\",\"title\":\"Admission Charges Applied\",\"message\":\"You\'ve been charged \\u20b10.00 (Bed: \\u20b10.00, Room: \\u20b10.00, Doctor: \\u20b10.00)\",\"bed_rate\":0,\"room_rate\":0,\"doctor_rate\":0}', NULL, '2025-07-20 22:36:04', '2025-07-20 22:36:04'),
('60889e9f-7c64-4db4-afba-8647bb849486', 'App\\Notifications\\DisputeFiled', 'App\\Models\\User', 5, '{\"message\":\"A new billing dispute was filed.\",\"dispute_id\":6}', NULL, '2025-07-31 20:50:28', '2025-07-31 20:50:28'),
('74cc5189-7dfb-4f30-9ad4-870eda3a05d8', 'App\\Notifications\\DisputeFiled', 'App\\Models\\User', 5, '{\"message\":\"A new billing dispute was filed.\",\"dispute_id\":3}', NULL, '2025-07-13 12:06:43', '2025-07-13 12:06:43'),
('798edb3d-ef94-4f9f-91b6-2deb6855c873', 'App\\Notifications\\AdmissionCharged', 'App\\Models\\User', 40, '{\"type\":\"Billing\",\"title\":\"\\u20b10.00 in admission charges\",\"bed_rate\":0,\"room_rate\":0,\"doctor_rate\":0,\"billing_item_id\":18}', NULL, '2025-08-01 16:48:28', '2025-08-01 16:48:28'),
('8d59d0cf-cb3c-4698-915c-4a259316d476', 'App\\Notifications\\LabChargeCreated', 'App\\Models\\Patient', 20, '{\"type\":\"Laboratory\",\"event\":\"Order Placed\",\"assignment_id\":16,\"service\":\"Thyroid Function Tests\",\"doctor\":\"Carol Taylor\",\"ordered_at\":\"2025-08-19 15:20:21\",\"message\":\"Your lab order for \\u201cThyroid Function Tests\\u201d has been placed.\"}', NULL, '2025-08-19 15:20:22', '2025-08-19 15:20:22'),
('8fc5ddef-32ec-45aa-8a43-a4e03c0c9c92', 'App\\Notifications\\DisputeFiled', 'App\\Models\\User', 5, '{\"message\":\"A new billing dispute was filed.\",\"dispute_id\":4}', NULL, '2025-07-18 08:05:14', '2025-07-18 08:05:14'),
('a75ea6ca-b1c8-4ca9-8342-1d9734073c7b', 'App\\Notifications\\DisputeFiled', 'App\\Models\\User', 5, '{\"message\":\"A new billing dispute was filed.\",\"dispute_id\":7}', NULL, '2025-07-31 20:52:23', '2025-07-31 20:52:23'),
('c3a9a858-b318-44e4-b6ad-e3b15a1dce58', 'App\\Notifications\\DisputeFiled', 'App\\Models\\User', 5, '{\"message\":\"A new billing dispute was filed.\",\"dispute_id\":8}', NULL, '2025-07-31 20:55:18', '2025-07-31 20:55:18'),
('c6acf063-2f8c-4bb6-8039-f57d920c4f64', 'App\\Notifications\\LabChargeCreated', 'App\\Models\\Patient', 20, '{\"type\":\"Laboratory\",\"event\":\"Order Placed\",\"assignment_id\":18,\"service\":\"Complete Blood Count (CBC)\",\"doctor\":\"Carol Taylor\",\"ordered_at\":\"2025-08-19 15:20:22\",\"message\":\"Your lab order for \\u201cComplete Blood Count (CBC)\\u201d has been placed.\"}', NULL, '2025-08-19 15:20:22', '2025-08-19 15:20:22'),
('e33854f9-51ae-4d4f-9134-b965f3f09fdc', 'App\\Notifications\\DisputeFiled', 'App\\Models\\User', 5, '{\"message\":\"A new billing dispute was filed.\",\"dispute_id\":5}', NULL, '2025-07-30 22:27:56', '2025-07-30 22:27:56'),
('ea125738-d451-4954-9f7c-21b48fb35a0c', 'App\\Notifications\\DepositReceived', 'App\\Models\\Patient', 6, '{\"type\":\"deposit\",\"deposit_id\":null,\"amount\":\"22\",\"deposited_at\":\"2025-08-02 00:00:00\",\"message\":\"A payment of \\u20b122.00 has been posted to your account.\"}', NULL, '2025-08-02 21:22:32', '2025-08-02 21:22:32'),
('eb565081-108f-4d29-a34c-958645471ac0', 'App\\Notifications\\DisputeFiled', 'App\\Models\\User', 5, '{\"message\":\"A new billing dispute was filed.\",\"dispute_id\":9}', NULL, '2025-07-31 20:57:29', '2025-07-31 20:57:29'),
('fc384afe-7458-4c69-87a6-433cbe2977ef', 'App\\Notifications\\DisputeFiled', 'App\\Models\\User', 5, '{\"message\":\"A new billing dispute was filed.\",\"dispute_id\":10}', NULL, '2025-07-31 21:07:02', '2025-07-31 21:07:02');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `patient_first_name` varchar(100) NOT NULL,
  `patient_last_name` varchar(100) NOT NULL,
  `middle_initial` char(1) DEFAULT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `patient_birthday` date DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `billing_status` enum('open','finished') DEFAULT 'open',
  `billing_closed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `patient_first_name`, `patient_last_name`, `middle_initial`, `sex`, `patient_birthday`, `email`, `phone_number`, `profile_picture`, `status`, `created_at`, `updated_at`, `civil_status`, `address`, `birthday`, `password`, `profile_photo`, `billing_status`, `billing_closed_at`) VALUES
(33, 'Jin', 'Arzaga', NULL, 'Female', '2025-08-22', 'ja.001@patientcare.com', '99999999999', NULL, 'active', '2025-08-22 15:07:47', '2025-08-22 15:07:47', 'Single', '#001, Don Bosco St., Brgy. Sto Cristo', NULL, '$2y$10$42YTzZKoRuBZKglgEIp6iuwObIvEy5dbuOG82iZqCTvuIk7FGnyqq', NULL, 'open', NULL),
(34, 'Kyrie', 'Irving', NULL, 'Male', '2025-08-27', 'ki.001@patientcare.com', '743244324324', NULL, 'active', '2025-08-27 00:38:05', '2025-08-27 00:38:05', 'Single', '#Maligaya', NULL, '$2y$10$eBGbbrpKNFYZurnrvHEcnuhLHfJdRHvYd2URGoq4q0WTrIUc8a5ka', NULL, 'open', NULL),
(35, 'Gab', 'Gab', NULL, 'Male', '2025-08-27', 'gg.001@patientcare.com', '99999999999', NULL, 'active', '2025-08-27 01:42:58', '2025-08-27 01:42:58', 'Single', '#001, Don Bosco St., Brgy. Sto Cristo', NULL, '$2y$10$vIG1fTj/Sm33RCGSmgUxTuf79wDDYgWyD55l8jYd9K/nzUZ1RkSIS', NULL, 'open', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `payment_method_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`payment_method_id`, `name`, `created_at`) VALUES
(1, 'Cash', '2025-05-12 06:28:33'),
(2, 'Insurance', '2025-05-12 06:28:33'),
(3, 'Credit Card', '2025-05-12 06:28:33'),
(4, 'Debit Card', '2025-05-12 06:28:33'),
(5, 'Bank Transfer', '2025-05-12 06:28:33'),
(6, 'Cash', '2025-05-12 06:37:44'),
(7, 'Insurance', '2025-05-12 06:37:44'),
(8, 'Credit Card', '2025-05-12 06:37:44'),
(9, 'Debit Card', '2025-05-12 06:37:44'),
(10, 'Bank Transfer', '2025-05-12 06:37:44'),
(11, 'Cash', '2025-05-12 06:37:55'),
(12, 'Insurance', '2025-05-12 06:37:55'),
(13, 'Credit Card', '2025-05-12 06:37:55'),
(14, 'Debit Card', '2025-05-12 06:37:55'),
(15, 'Bank Transfer', '2025-05-12 06:37:55'),
(16, 'Cash', '2025-05-12 06:38:19'),
(17, 'Insurance', '2025-05-12 06:38:19'),
(18, 'Credit Card', '2025-05-12 06:38:19'),
(19, 'Debit Card', '2025-05-12 06:38:19'),
(20, 'Bank Transfer', '2025-05-12 06:38:19'),
(21, 'Cash', '2025-05-12 06:38:44'),
(22, 'Insurance', '2025-05-12 06:38:44'),
(23, 'Credit Card', '2025-05-12 06:38:44'),
(24, 'Debit Card', '2025-05-12 06:38:44'),
(25, 'Bank Transfer', '2025-05-12 06:38:44'),
(26, 'Cash', '2025-05-12 06:40:15'),
(27, 'Insurance', '2025-05-12 06:40:15'),
(28, 'Credit Card', '2025-05-12 06:40:15'),
(29, 'Debit Card', '2025-05-12 06:40:15'),
(30, 'Bank Transfer', '2025-05-12 06:40:15');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_charges`
--

CREATE TABLE `pharmacy_charges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` int(11) NOT NULL,
  `prescribing_doctor` varchar(255) NOT NULL,
  `rx_number` varchar(100) NOT NULL,
  `notes` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','completed') NOT NULL DEFAULT 'pending',
  `dispensed_at` timestamp NULL DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pharmacy_charges`
--

INSERT INTO `pharmacy_charges` (`id`, `patient_id`, `prescribing_doctor`, `rx_number`, `notes`, `total_amount`, `status`, `dispensed_at`, `date`, `created_at`, `updated_at`) VALUES
(1, 33, 'same', 'RX2025082314551471T', NULL, 5.00, 'completed', '2025-08-24 14:01:08', '2025-08-23 06:55:14', '2025-08-23 06:55:14', '2025-08-24 14:01:08'),
(2, 33, 'same', 'RX20250823150005OZ8', NULL, 15.00, 'completed', '2025-08-24 14:01:05', '2025-08-23 07:00:05', '2025-08-23 07:00:05', '2025-08-24 14:01:05'),
(3, 33, 'same', 'RX20250823150018BAV', '3', 5.00, 'completed', '2025-08-24 14:00:28', '2025-08-23 07:00:18', '2025-08-23 07:00:18', '2025-08-24 14:00:28'),
(4, 33, 'same', 'RX20250824142736EGW', NULL, 1550.00, 'completed', '2025-08-24 14:00:11', '2025-08-24 06:27:36', '2025-08-24 06:27:36', '2025-08-24 14:00:11'),
(9, 33, 'same', 'RX20250824221158HOT', NULL, 205.00, 'completed', '2025-08-24 14:36:59', '2025-08-24 14:11:58', '2025-08-24 14:11:58', '2025-08-24 14:36:59'),
(10, 33, 'same', 'RX202508242252206WO', NULL, 24.00, 'pending', NULL, '2025-08-24 14:52:20', '2025-08-24 14:52:20', '2025-08-24 14:52:20');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_charge_items`
--

CREATE TABLE `pharmacy_charge_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `charge_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` int(11) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pharmacy_charge_items`
--

INSERT INTO `pharmacy_charge_items` (`id`, `charge_id`, `service_id`, `quantity`, `unit_price`, `total`, `status`) VALUES
(1, 1, 43, 1, 5.00, 5.00, 'dispensed'),
(2, 2, 44, 1, 15.00, 15.00, 'dispensed'),
(3, 3, 43, 1, 5.00, 5.00, 'dispensed'),
(4, 4, 106, 4, 350.00, 1400.00, 'dispensed'),
(5, 4, 109, 1, 150.00, 150.00, 'dispensed'),
(6, 9, 44, 1, 15.00, 15.00, 'dispensed'),
(7, 9, 111, 2, 5.00, 10.00, 'dispensed'),
(8, 9, 112, 4, 45.00, 180.00, 'dispensed'),
(9, 10, 43, 1, 5.00, 5.00, 'dispensed'),
(10, 10, 110, 1, 6.00, 6.00, 'dispensed'),
(11, 10, 105, 1, 8.00, 8.00, 'pending'),
(12, 10, 111, 1, 5.00, 5.00, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `prescription_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`prescription_id`, `patient_id`, `doctor_id`) VALUES
(21, 33, 33),
(22, 33, 33),
(23, 33, 33),
(24, 33, 33),
(29, 33, 33),
(30, 33, 33);

-- --------------------------------------------------------

--
-- Table structure for table `prescription_items`
--

CREATE TABLE `prescription_items` (
  `prescription_item_id` int(11) NOT NULL,
  `prescription_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `datetime` datetime NOT NULL,
  `service_id` int(11) NOT NULL,
  `quantity_given` int(11) NOT NULL,
  `quantity_asked` int(11) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `dosage` varchar(255) DEFAULT NULL,
  `frequency` varchar(255) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `duration_unit` varchar(255) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `refills` int(11) DEFAULT 0,
  `routing` enum('internal','external') DEFAULT NULL,
  `priority` enum('routine','urgent','stat') DEFAULT NULL,
  `daw` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescription_items`
--

INSERT INTO `prescription_items` (`prescription_item_id`, `prescription_id`, `name`, `datetime`, `service_id`, `quantity_given`, `quantity_asked`, `status`, `dosage`, `frequency`, `route`, `duration`, `duration_unit`, `instructions`, `refills`, `routing`, `priority`, `daw`) VALUES
(13, 21, 'Paracetamol 500 mg Tablet', '2025-08-23 14:55:14', 43, 0, 1, 'pending', NULL, NULL, NULL, 1, 'days', '', 0, NULL, NULL, 0),
(14, 22, 'Amoxicillin 500 mg Cap', '2025-08-23 15:00:05', 44, 0, 1, 'pending', NULL, NULL, NULL, 1, 'days', '', 0, NULL, NULL, 0),
(15, 23, 'Paracetamol 500 mg Tablet', '2025-08-23 15:00:18', 43, 0, 1, 'pending', NULL, NULL, NULL, 1, 'days', '3', 0, NULL, NULL, 0),
(16, 24, 'Insulin (Human), 10 ml vial', '2025-08-24 14:27:36', 106, 0, 4, 'pending', NULL, NULL, NULL, 2, 'days', '', 0, NULL, NULL, 0),
(17, 24, 'Salbutamol Inhaler (100 mcg)', '2025-08-24 14:27:36', 109, 0, 1, 'pending', NULL, NULL, NULL, 1, 'days', '', 0, NULL, NULL, 0),
(22, 29, 'Amoxicillin 500 mg Cap', '2025-08-24 22:11:58', 44, 0, 1, 'pending', NULL, NULL, NULL, 1, 'days', '', 0, NULL, NULL, 0),
(23, 29, 'Biogesic (Paracetamol) Tablet', '2025-08-24 22:11:58', 111, 0, 2, 'pending', NULL, NULL, NULL, 1, 'days', '', 0, NULL, NULL, 0),
(24, 29, 'Tempra Suspension (Paracetamol) – Children', '2025-08-24 22:11:58', 112, 0, 4, 'pending', NULL, NULL, NULL, 1, 'days', '', 0, NULL, NULL, 0),
(25, 30, 'Paracetamol 500 mg Tablet', '2025-08-24 22:52:20', 43, 0, 1, 'pending', NULL, NULL, NULL, 1, 'days', '', 0, NULL, NULL, 0),
(26, 30, 'Cetirizine 10 mg Tablet (Antihistamine)', '2025-08-24 22:52:20', 110, 0, 1, 'pending', NULL, NULL, NULL, 1, 'days', '', 0, NULL, NULL, 0),
(27, 30, 'Glipizide 5 mg Tablet (Sulfonylurea)', '2025-08-24 22:52:20', 105, 0, 1, 'pending', NULL, NULL, NULL, 1, 'days', '', 0, NULL, NULL, 0),
(28, 30, 'Biogesic (Paracetamol) Tablet', '2025-08-24 22:52:20', 111, 0, 1, 'pending', NULL, NULL, NULL, 1, 'days', '', 0, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `capacity` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Maximum number of beds this room should hold',
  `rate` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `department_id`, `room_number`, `status`, `created_at`, `updated_at`, `capacity`, `rate`) VALUES
(40, 27, '101', 'available', '2025-08-20 12:41:12', '2025-08-20 12:41:12', 3, 0),
(41, 24, '143', 'available', '2025-08-27 01:56:27', '2025-08-27 01:56:27', 5, 200);

-- --------------------------------------------------------

--
-- Table structure for table `service_assignments`
--

CREATE TABLE `service_assignments` (
  `assignment_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `bill_item_id` int(10) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `room` int(11) DEFAULT NULL,
  `doctor_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `datetime` datetime DEFAULT NULL,
  `service_status` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `bed_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `patient_id`, `doctor_id`, `username`, `email`, `password`, `role`, `department_id`, `room_id`, `bed_id`) VALUES
(1, NULL, NULL, 'admin_user', 'admin@patientcare.local', '$2y$10$Gcho8W7w0sQdcVUunqFw6eYci1uuLDX0NvYCf2lU6VmTN5NYlSrLK', 'admin', NULL, NULL, NULL),
(2, NULL, NULL, 'doctor_user', 'doctor@patientcare.local', '$2y$10$Gcho8W7w0sQdcVUunqFw6eYci1uuLDX0NvYCf2lU6VmTN5NYlSrLK', 'doctor', NULL, NULL, NULL),
(4, NULL, NULL, 'admission_user', 'admission@patientcare.local', '$2y$10$Gcho8W7w0sQdcVUunqFw6eYci1uuLDX0NvYCf2lU6VmTN5NYlSrLK', 'admission', NULL, NULL, NULL),
(5, NULL, NULL, 'billing_user', 'billing@patientcare.local', '$2y$10$Gcho8W7w0sQdcVUunqFw6eYci1uuLDX0NvYCf2lU6VmTN5NYlSrLK', 'billing', NULL, NULL, NULL),
(6, NULL, NULL, 'pharmacy_user', 'pharmacy@patientcare.local', '$2y$10$Gcho8W7w0sQdcVUunqFw6eYci1uuLDX0NvYCf2lU6VmTN5NYlSrLK', 'pharmacy', NULL, NULL, NULL),
(13, NULL, NULL, 'supplies_user', 'supplies@example.com', '$2y$10$OySPNocqIa3957nYQETuW.iWdv/SA0BfFgUFO3sEpsM085iTX23ya', 'supplies', NULL, NULL, NULL),
(14, NULL, NULL, 'or_user', 'operating@example.com', '$2y$10$KEzDhJxbbRHNjUJiEaDQGeyKkeFP8fANF2U5GwKWX4oH7enbH2N7.', 'operating_room', NULL, NULL, NULL),
(15, NULL, NULL, 'lab_user', 'laboratory@example.com', '$2y$10$V.Rl8YzGIQ0F7HlYNLyrQ.ppiwvXvqNtn2Z.5CYnyMY7U5F4o2Sr.', 'laboratory', NULL, NULL, NULL),
(40, 27, 2, 'vs.001', 'vs.001@patientcare.com', '$2y$10$9cHfLWk6zIcgr6fa51K/I./0puzI2hDdDLGoOe6njZIw5MxbLYXRC', 'patient', NULL, NULL, NULL),
(42, NULL, NULL, 'sam', 'toshibasam08@gmail.com', '$2y$10$UALZ4y.XWh/y7hWENOGXU.NcCnV3tc97cEuWKXFQFmEX76oZK5F.u', 'admin', NULL, NULL, NULL),
(45, NULL, 26, 'lolza', 'user@example.com', '$2y$10$4xNTB1uM7VJ1lneImYy/tOjJ2aJOJmLA87IPae5x1XYHX8syiNGly', 'doctor', NULL, NULL, NULL),
(46, NULL, 27, 'sammmm', 'stan@gmail.com', '$2y$10$jv3dMPdVIyyUyRTTR1sQO.2Q60Qvg3PB94hFP5aYbDt1y8LqIZCSW', 'doctor', NULL, NULL, NULL),
(48, NULL, 31, 'stan', 'stans@gmail.com', 'password', 'doctor', 6, NULL, NULL),
(49, NULL, 33, 'same', 'doctorme@gmail.com', '$2y$10$CR9d84NshXjx1bswMWrVBuQI35lTqWLfvOhD6/ScO1jxXZ2hqZNsq', 'doctor', NULL, NULL, NULL),
(50, NULL, 34, 'gamma', 'doc@gmail.com', '$2y$10$UVmQL1KrJd48maAuqGif8.4eMObmAAEgH8LL8r1E66AYK.VkpVKUi', 'doctor', NULL, NULL, NULL),
(51, NULL, 35, 'js', 'dok@patientcare.local', '$2y$10$WdafsnwTnWLVAp8k7dKPLucJX2L/4m.sCzOSq6X4//hTdgSEoNpRu', 'doctor', NULL, NULL, NULL),
(54, 33, 33, 'ja.001', 'ja.001@patientcare.com', '$2y$10$1dyb6Rc1Ucrh2ni8rgn5seQ8/nyGW5hvHavOXos/orH9OLlv8G6QS', 'patient', 27, 40, 128),
(66, NULL, NULL, 'Joshua', 'joshua@local.com', '$2y$10$zRysHKgOVVx4OfSykocnYOkQvqFlJDk3otynD0OQw/H7XKXdZHx3K', 'pharmacy', NULL, NULL, NULL),
(67, NULL, NULL, 'Joshue', 'joshko@umay.com', '$2y$10$2M0IXbEFHE9hX9p5AG6e8.tj4UtWGozCbx8eUeoWmOcwiZ3Pw6.86', 'billing', NULL, NULL, NULL),
(68, 34, 33, 'ki.001', 'ki.001@patientcare.com', '$2y$10$omJtcWVYi1VmEhC1zlqSGuw6czpnXJmyV45mnl.uYom52EUgUIIIW', 'patient', 27, 40, 130),
(69, 35, 33, 'gg.001', 'gg.001@patientcare.com', '$2y$10$QXtRrw/EHMavKZR1j/Sg/eaWiqRIJAZy3pe57rfsOYJeSO2YKLhEK', 'patient', 27, 40, 129),
(70, NULL, 36, 'sammyG', 'doctorsamg@gmail.com', '$2y$10$/.ekBpFjPSPQ4.nqpsN31O/dejpKdfBw7IOegtRjRHuTZsDfWPF1y', 'doctor', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admission_details`
--
ALTER TABLE `admission_details`
  ADD PRIMARY KEY (`admission_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_log_bill_item_id_foreign` (`bill_item_id`);

--
-- Indexes for table `beds`
--
ALTER TABLE `beds`
  ADD PRIMARY KEY (`bed_id`),
  ADD KEY `beds_room_id_foreign` (`room_id`),
  ADD KEY `beds_patient_id_foreign` (`patient_id`);

--
-- Indexes for table `billing_information`
--
ALTER TABLE `billing_information`
  ADD PRIMARY KEY (`billing_info_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`billing_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `bills_ibfk_2` (`admission_id`);

--
-- Indexes for table `bill_items`
--
ALTER TABLE `bill_items`
  ADD PRIMARY KEY (`billing_item_id`),
  ADD KEY `billing_id` (`billing_id`),
  ADD KEY `prescription_item_id` (`prescription_item_id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `bill_items_ibfk_service` (`service_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deposits_patient_id_foreign` (`patient_id`);

--
-- Indexes for table `disputes`
--
ALTER TABLE `disputes`
  ADD PRIMARY KEY (`dispute_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `disputes_disputable_index` (`disputable_id`,`disputable_type`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `hospital_services`
--
ALTER TABLE `hospital_services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `insurance_providers`
--
ALTER TABLE `insurance_providers`
  ADD PRIMARY KEY (`insurance_provider_id`);

--
-- Indexes for table `medical_details`
--
ALTER TABLE `medical_details`
  ADD PRIMARY KEY (`medical_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `medicine_stock_movements`
--
ALTER TABLE `medicine_stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_msm_service` (`service_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `miscellaneous_charges`
--
ALTER TABLE `miscellaneous_charges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mc_patient_idx` (`patient_id`),
  ADD KEY `mc_service_idx` (`service_id`),
  ADD KEY `mc_createdby_idx` (`created_by`),
  ADD KEY `mc_completedby_idx` (`completed_by`),
  ADD KEY `bill_item_id` (`bill_item_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `patients_email_unique` (`email`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`payment_method_id`);

--
-- Indexes for table `pharmacy_charges`
--
ALTER TABLE `pharmacy_charges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pharmacy_charges_patient` (`patient_id`);

--
-- Indexes for table `pharmacy_charge_items`
--
ALTER TABLE `pharmacy_charge_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pharmacy_charge_items_charge` (`charge_id`),
  ADD KEY `fk_pharmacy_charge_items_service` (`service_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`prescription_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `prescriptions_ibfk_2` (`doctor_id`);

--
-- Indexes for table `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD PRIMARY KEY (`prescription_item_id`),
  ADD KEY `prescription_id` (`prescription_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `rooms_department_id_foreign` (`department_id`);

--
-- Indexes for table `service_assignments`
--
ALTER TABLE `service_assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_department_id_foreign` (`department_id`),
  ADD KEY `users_room_id_foreign` (`room_id`),
  ADD KEY `users_bed_id_foreign` (`bed_id`),
  ADD KEY `users_patient_id_index` (`patient_id`),
  ADD KEY `users_doctor_id_foreign` (`doctor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admission_details`
--
ALTER TABLE `admission_details`
  MODIFY `admission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `beds`
--
ALTER TABLE `beds`
  MODIFY `bed_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `billing_information`
--
ALTER TABLE `billing_information`
  MODIFY `billing_info_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `billing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `bill_items`
--
ALTER TABLE `bill_items`
  MODIFY `billing_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `disputes`
--
ALTER TABLE `disputes`
  MODIFY `dispute_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `hospital_services`
--
ALTER TABLE `hospital_services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `insurance_providers`
--
ALTER TABLE `insurance_providers`
  MODIFY `insurance_provider_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `medical_details`
--
ALTER TABLE `medical_details`
  MODIFY `medical_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `medicine_stock_movements`
--
ALTER TABLE `medicine_stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `miscellaneous_charges`
--
ALTER TABLE `miscellaneous_charges`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `payment_method_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `pharmacy_charges`
--
ALTER TABLE `pharmacy_charges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pharmacy_charge_items`
--
ALTER TABLE `pharmacy_charge_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `prescription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `prescription_items`
--
ALTER TABLE `prescription_items`
  MODIFY `prescription_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `service_assignments`
--
ALTER TABLE `service_assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_bill_item_id_foreign` FOREIGN KEY (`bill_item_id`) REFERENCES `bill_items` (`billing_item_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
