-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 30, 2025 at 01:30 AM
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
-- Database: `gms`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcement_tbl`
--

CREATE TABLE `announcement_tbl` (
  `announcement_jd` int(11) NOT NULL,
  `subject` int(11) NOT NULL,
  `section` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `date` varchar(10) NOT NULL,
  `teacher_id` varchar(20) NOT NULL,
  `date_inserted` date NOT NULL DEFAULT current_timestamp(),
  `quarterly` int(11) NOT NULL,
  `items` int(11) NOT NULL,
  `grade` int(11) NOT NULL,
  `time` time NOT NULL,
  `room` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement_tbl`
--

INSERT INTO `announcement_tbl` (`announcement_jd`, `subject`, `section`, `type`, `date`, `teacher_id`, `date_inserted`, `quarterly`, `items`, `grade`, `time`, `room`) VALUES
(4, 2, 1, 'quiz', '2025-11-28', '301531-002', '2025-11-27', 1, 50, 7, '13:00:00', 1),
(5, 2, 1, 'pt', '2025-11-28', '301531-002', '2025-11-27', 2, 100, 7, '13:00:00', 1),
(6, 2, 1, 'pt', '2025-11-28', '301531-002', '2025-11-27', 1, 100, 7, '12:30:00', 1),
(7, 2, 1, 'exam', '2025-11-28', '301531-002', '2025-11-27', 1, 100, 7, '12:35:00', 1),
(8, 2, 1, 'quiz', '2025-11-28', '301531-002', '2025-11-27', 2, 30, 7, '12:40:00', 1),
(9, 2, 0, 'exam', '2025-11-28', '301531-002', '2025-11-27', 2, 70, 7, '12:45:00', 1),
(10, 2, 1, 'exam', '2025-11-28', '301531-002', '2025-11-27', 2, 50, 7, '12:48:00', 1),
(11, 2, 1, 'quiz', '2025-11-28', '301531-002', '2025-11-27', 3, 80, 7, '12:51:00', 1),
(12, 2, 1, 'pt', '2025-11-28', '301531-002', '2025-11-27', 3, 100, 7, '12:55:00', 1),
(13, 2, 1, 'exam', '2025-11-28', '301531-002', '2025-11-27', 3, 100, 7, '12:56:00', 1),
(14, 2, 1, 'quiz', '2025-11-28', '301531-002', '2025-11-27', 4, 40, 7, '12:58:00', 1),
(15, 2, 1, 'pt', '2025-11-28', '301531-002', '2025-11-27', 4, 100, 7, '12:58:00', 1),
(16, 2, 1, 'exam', '2025-11-28', '301531-002', '2025-11-27', 4, 70, 7, '12:57:00', 1),
(17, 2, 1, 'quiz', '2025-11-27', '301531-002', '2025-11-26', 1, 50, 7, '08:31:00', 1),
(18, 2, 3, 'quiz', '2025-11-26', '301531-002', '2025-11-26', 1, 100, 7, '06:51:00', 1),
(19, 2, 1, 'quiz', '2025-11-26', '301531-002', '2025-11-26', 1, 30, 7, '07:31:00', 1),
(20, 2, 1, 'pt', '2025-11-27', '301531-002', '2025-11-26', 1, 70, 7, '08:42:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `attendance_tbl`
--

CREATE TABLE `attendance_tbl` (
  `attendance_id` int(11) NOT NULL,
  `student_id` varchar(20) DEFAULT NULL,
  `attendance_date` date DEFAULT NULL,
  `am_status` varchar(10) DEFAULT NULL,
  `pm_status` varchar(10) DEFAULT NULL,
  `remarks` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_tbl`
--

INSERT INTO `attendance_tbl` (`attendance_id`, `student_id`, `attendance_date`, `am_status`, `pm_status`, `remarks`) VALUES
(1, '2025-001', '2025-11-29', 'Present', 'Absent', 1),
(2, '2025-002', '2025-11-29', 'Absent', 'Late', 1),
(3, '2025-003', '2025-11-29', 'Excused', 'Present', 1),
(4, '2025-004', '2025-11-29', 'Absent', 'Present', 1),
(5, '2025-005', '2025-11-29', 'Present', 'Present', 1),
(6, '2025-002', '2025-11-26', 'Late', 'Absent', 1),
(7, '2025-004', '2025-11-26', 'Present', 'Absent', 1),
(8, '2025-004', '2025-11-27', 'Present', 'Absent', 1),
(9, '2025-009', '2025-11-28', 'Present', 'Absent', 1);

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_period_tbl`
--

CREATE TABLE `enrollment_period_tbl` (
  `enrollment_period_id` int(11) NOT NULL,
  `description` varchar(200) NOT NULL,
  `start_date` varchar(10) NOT NULL,
  `end_date` varchar(10) NOT NULL,
  `sy` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollment_period_tbl`
--

INSERT INTO `enrollment_period_tbl` (`enrollment_period_id`, `description`, `start_date`, `end_date`, `sy`) VALUES
(1, 'ENROLLMENY PERIOD SY 2025-2026', '2025-11-25', '2025-11-30', 2025);

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_quarters_tbl`
--

CREATE TABLE `enrollment_quarters_tbl` (
  `id` int(11) NOT NULL,
  `period_id` int(11) DEFAULT NULL,
  `quarter_name` varchar(10) DEFAULT NULL,
  `quarter_start` date DEFAULT NULL,
  `quarter_end` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollment_quarters_tbl`
--

INSERT INTO `enrollment_quarters_tbl` (`id`, `period_id`, `quarter_name`, `quarter_start`, `quarter_end`) VALUES
(1, 1, '1st', '2025-12-01', '2026-02-02'),
(2, 1, '2nd', '2026-02-03', '2026-04-03'),
(3, 1, '3rd', '2026-06-04', '2026-08-05'),
(4, 1, '4th', '2026-10-06', '2026-12-07');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_requirement_tbl`
--

CREATE TABLE `enrollment_requirement_tbl` (
  `enrollment_requirement_id` int(11) NOT NULL,
  `enrollment_requirement_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollment_requirement_tbl`
--

INSERT INTO `enrollment_requirement_tbl` (`enrollment_requirement_id`, `enrollment_requirement_name`) VALUES
(1, 'FORM 137'),
(2, 'PSA'),
(3, 'REPORT CARD (GRADE 6)'),
(4, 'DTS');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_tbl`
--

CREATE TABLE `enrollment_tbl` (
  `enrollmentId` int(11) NOT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `extname` varchar(10) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `mothertongue` varchar(50) DEFAULT NULL,
  `birthplace` varchar(100) DEFAULT NULL,
  `ip` varchar(10) DEFAULT NULL,
  `is_4ps` varchar(10) DEFAULT NULL,
  `has_disability` varchar(10) DEFAULT NULL,
  `disability_type` text DEFAULT NULL,
  `disability_specify` varchar(100) DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `mother_lastname` varchar(50) DEFAULT NULL,
  `mother_firstname` varchar(50) DEFAULT NULL,
  `mother_middlename` varchar(50) DEFAULT NULL,
  `mother_contact` varchar(20) DEFAULT NULL,
  `father_lastname` varchar(50) DEFAULT NULL,
  `father_firstname` varchar(50) DEFAULT NULL,
  `father_middlename` varchar(50) DEFAULT NULL,
  `father_contact` varchar(20) DEFAULT NULL,
  `guardian_lastname` varchar(50) DEFAULT NULL,
  `guardian_firstname` varchar(50) DEFAULT NULL,
  `guardian_middlename` varchar(50) DEFAULT NULL,
  `guardian_contact` varchar(20) DEFAULT NULL,
  `form_137` varchar(100) DEFAULT NULL,
  `psa` varchar(100) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `enrollment_status` int(11) NOT NULL DEFAULT 1,
  `user_ids` int(11) NOT NULL,
  `grade` varchar(10) NOT NULL,
  `stud_sy` int(11) NOT NULL,
  `lrn` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollment_tbl`
--

INSERT INTO `enrollment_tbl` (`enrollmentId`, `lastname`, `firstname`, `middlename`, `extname`, `birthdate`, `age`, `sex`, `mothertongue`, `birthplace`, `ip`, `is_4ps`, `has_disability`, `disability_type`, `disability_specify`, `current_address`, `permanent_address`, `mother_lastname`, `mother_firstname`, `mother_middlename`, `mother_contact`, `father_lastname`, `father_firstname`, `father_middlename`, `father_contact`, `guardian_lastname`, `guardian_firstname`, `guardian_middlename`, `guardian_contact`, `form_137`, `psa`, `date_created`, `enrollment_status`, `user_ids`, `grade`, `stud_sy`, `lrn`) VALUES
(1, 'MINGA', 'JENNEX', 'ALGORDO', '', '2003-07-30', 22, 'Female', 'TAGALOG', 'BALANACAN, MOGPOG, MARINDUQUE', 'NO', 'YES', 'No', '', '', 'BALANACAN MOGPOG MARINDUQUE', 'BALANACAN MOGPOG MARINDUQUE', 'MINGA', 'JENNIFER', 'ALGORDO', '09271717469', 'MINGA', 'ALEX', 'M', '09271717469', 'MINGA', 'ALEX', 'M', '09271717469', NULL, NULL, '2025-11-27 19:39:33', 3, 1, '7', 2025, '109939000049'),
(2, 'EMBING', 'KRISTINE NELLY', 'L', '', '2003-10-12', 22, 'Female', 'TAGALOG', 'LIBTANGIN, GASAN, MARINDUQUE', 'NO', 'NO', 'No', '', '', 'LIBTANGIN GASAN MARINDUQUE', 'LIBTANGIN GASAN MARINDUQUE', 'EMBING', 'REGINA', 'LUISAGA', '09271717469', 'EMBING', 'RANDY', 'LLEGADO', '09271717469', 'EMBING', 'REGINA', 'LUISAGA', '09271717469', NULL, NULL, '2025-11-27 19:46:30', 3, 3, '7', 2025, '109924090018'),
(3, 'EMBING', 'KRISTINE NELLY', 'L', '', '2003-10-12', 22, 'Female', 'TAGALOG', 'LIBTANGIN, GASAN, MARINDUQUE', 'NO', 'NO', 'No', '', '', 'LIBTANGIN GASAN MARINDUQUE', 'LIBTANGIN GASAN MARINDUQUE', 'EMBING', 'REGINA', 'LUISAGA', '09271717469', 'EMBING', 'RANDY', 'LLEGADO', '09271717469', 'EMBING', 'REGINA', 'LUISAGA', '09271717469', NULL, NULL, '2025-11-27 19:46:36', 3, 3, '7', 2025, '109924090018'),
(4, 'REJANO', 'AILENE ', 'PERILLA', '', '2003-12-12', 21, 'Female', 'TAGALOG', 'HINADHARAN , MOGPOG, MARINDUQUE', 'NO', 'YES', 'No', '', '', 'HINADHARAN , MOGPOG, MARINDUQUE', 'HINADHARAN , MOGPOG, MARINDUQUE', 'REJANO', 'LENLIE', 'PERILLA', '09353793249', 'REJANO', 'ARSENIO', 'ROGELIO', '09559663511', 'REJANO', 'LENLIE', 'PERILLA', '09353793249', NULL, NULL, '2025-11-27 19:47:46', 3, 2, '7', 2025, '10994500005'),
(5, 'SAPORNA', 'AIRA JANE', 'LOSBANOS', '', '2013-01-15', 12, 'Female', 'TAGALOG', 'BRGY. TRES, GASAN, MARINDUQUE', 'NO', 'NO', 'No', '', '', 'BRGY. TRES, GASAN, MARINDUQUE', 'BRGY. TRES, GASAN, MARINDUQUE', 'SAPORNA', 'ANNA', 'LOSBANOS', '09763445685', 'SAPORNA', 'RUDEN', 'SALES', '09763445685', 'SAPORNA', 'ANNA', 'LOSBANOS', '09763445685', NULL, NULL, '2025-11-27 19:52:57', 3, 4, '7', 2025, '109924090016'),
(6, 'MANDIA', 'KIM', 'L.', '', '2003-12-24', 21, 'Female', 'TAGALOG', 'AGUMAYMAYAN BOAC, MARINDUQUE', 'NO', 'NO', 'No', '', '', 'AGUMAYMAYAN BOAC MARINDUQUE', 'AGUMAYMAYAN BOAC MARINDUQUE', 'MANDIA', 'MARY', 'L.', '09763445685', 'MANDIA', 'JOSEPH', 'M.', '09763445685', 'MANDIA', 'MARY', 'L.', '09763445685', NULL, NULL, '2025-11-27 20:06:37', 3, 5, '8', 2025, '10314509000010'),
(7, 'SUAREZ', 'DHOMALIN', 'MALAYLAY', '', '1999-12-21', 25, 'Female', 'AKSJJKHJAH', 'NASGJASHJJKLQ', 'NO', 'NO', 'No', '', '', 'NAMSNADNNSBD', 'NAMSNADNNSBD', 'NANSMN', 'DNSDD', 'SJDHSJKHEJK', '09887654', 'SAJSKJDKJ', 'DJSDHJSH', 'DSHDJADHWAJ', '0987654321', 'NNNSDNSJD', 'DHSJHDJADHJ', 'DJDKJHJK', '0987654455', NULL, NULL, '2025-11-27 09:42:51', 3, 6, '10', 2025, '101828229929'),
(8, 'DOE', 'JOHN', 'N/A', '', '2007-01-01', 18, 'Male', 'TAGALOG', 'MOGPOG', 'NO', 'YES', 'No', '', '', 'MOGPOG', 'MOGPOG', 'DOE', 'JANE', 'N/A', '912345678', 'DOE', 'JAY', 'NA', '938471345', 'DOE', 'JANE', 'N/A', '912345678', NULL, NULL, '2025-11-26 14:30:46', 3, 7, '7', 2025, '12345678901'),
(9, 'LAYLAY', 'ABEGAIL JOY', 'LIMBO', '', '2000-08-02', 25, 'Female', 'TAGALOG', 'BOAC', 'NO', 'YES', 'No', '', '', 'BOAC', 'BOAC', 'M', 'M', 'M', '09', 'A', 'A', 'A', '09', 'B', 'B', 'B', '09', NULL, NULL, '2025-11-26 15:21:23', 2, 8, '7', 2025, '18B0702'),
(10, 'LAYLAY', 'ABEGAIL JOY', 'LIMBO', '', '2000-08-02', 25, 'Female', 'TAGALOG', 'BOAC', 'NO', 'YES', 'No', '', '', 'BOAC', 'BOAC', 'M', 'M', 'M', '09', 'A', 'A', 'A', '09', 'B', 'B', 'B', '09', NULL, NULL, '2025-11-26 15:21:30', 2, 8, '7', 2025, '18B0702'),
(11, 'CARPIO', 'MELVZ', 'M.', '', '1995-09-10', 30, 'Male', 'TAGALOG', 'BOAC', 'NO', 'YES', 'No', '', '', 'BOAC', 'BOAC', 'CARPIO', 'ELVIE', 'M', '09', 'G', 'R', 'H', '09763445685', 'G', 'H', 'J', '09271717469', NULL, NULL, '2025-11-26 16:15:02', 3, 9, '7', 2025, '3014530900012'),
(12, 'CARPIO', 'MELVZ', 'M.', '', '1995-09-10', 30, 'Male', 'TAGALOG', 'BOAC', 'NO', 'YES', 'No', '', '', 'BOAC', 'BOAC', 'CARPIO', 'ELVIE', 'M', '09', 'G', 'R', 'H', '09763445685', 'G', 'H', 'J', '09271717469', NULL, NULL, '2025-11-26 16:15:07', 1, 9, '7', 2025, '3014530900012'),
(13, 'OLIVAR', 'JILLIAN', 'CAMA', '', '2011-04-09', 14, 'Female', 'FILIPINO', 'MINDORO', 'NO', 'YES', 'No', '', '', 'QUATIS, MASIGA, GASAN, MARINDUQUE', 'QUATIS, MASIGA, GASAN, MARINDUQUE', 'OLIVAR', 'MARY GRACE', 'CAMA', '09271717469', 'OLIVAR', 'SAMSON', 'SALAZAR', '09271717469', 'OLIVAR', 'MARY GRACE', 'CAMA', '09271717469', NULL, NULL, '2025-11-27 04:46:02', 3, 11, '9', 2025, '109929160019'),
(14, 'MACUNAT', 'DANIELLE', 'MENDOZA', '', '2011-01-05', 14, 'Female', 'TAGALOG', 'A.A PEREZ ', 'NO', 'NO', 'No', '', '', 'MAHUNIG, GASAN, MARINDUQUE', 'MAHUNIG, GASAN, MARINDUQUE', 'MENDOZA', 'JOSEPHINE', 'DIMAAMPAO', '485', 'MACUNAT', 'FLORENCIO', 'SAN ANDRES', '27', 'MENDOZA', 'JOSEPHINE', 'DIMAAMPAO', '527335', NULL, NULL, '2025-11-27 05:15:43', 1, 13, '9', 2025, '109924160016'),
(15, 'MACUNAT', 'DANIELLE', 'MENDOZA', '', '2011-01-05', 14, 'Female', 'TAGALOG', 'A.A PEREZ ', 'NO', 'NO', 'No', '', '', 'MAHUNIG, GASAN, MARINDUQUE', 'MAHUNIG, GASAN, MARINDUQUE', 'MENDOZA', 'JOSEPHINE', 'DIMAAMPAO', '485', 'MACUNAT', 'FLORENCIO', 'SAN ANDRES', '27', 'MENDOZA', 'JOSEPHINE', 'DIMAAMPAO', '527335', NULL, NULL, '2025-11-27 05:15:48', 1, 13, '9', 2025, '109924160016'),
(16, 'HERMOSO', 'ZYRUS LANCE', 'ROGAS', '', '2011-01-16', 14, 'Male', 'TAGALOG', 'BRGY UNO GASAN MARINDUQUE', 'NO', 'NO', 'No', '', '', 'BRGY UNO GASAN MARINDUQUE', 'BRGY UNO GASAN MARINDUQUE', 'HERMOSO', 'MARICEL', 'ROGAS', '09763445685', 'HERMOSO', 'ZEUS', 'ZOLETA', '09271717469', 'HERMOSO', 'MARICEL', 'ROGAS', '09763445685', NULL, NULL, '2025-11-27 05:16:59', 1, 12, '9', 2025, '109929160046'),
(17, 'SADJASJD', 'HSJDHJ', 'HJSDHF', 'S', '2000-09-26', 25, 'Male', 'SAMPLE ', 'SLJDASDASKD', 'No', 'No', 'No', '', '', 'JHDJASHDSJAD', 'JHDJASHDSJAD', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '2025-11-29 08:02:14', 1, 2, '7', 2025, '812738123812738'),
(18, 'AJSHASDJ', 'HJASDJASHDJ', 'SDASD', 'S', '2000-09-26', 25, 'Male', 'SAMPLE ', 'ADASDNASDNASD', 'No', 'No', 'No', '', '', 'JHDJASHDJHASJDKAS', 'JHDJASHDJHASJDKAS', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '2025-11-30 00:01:05', 1, 16, '7', 2025, '12931263');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_uploaded_files`
--

CREATE TABLE `enrollment_uploaded_files` (
  `id` int(11) NOT NULL,
  `enrollment_id` int(11) DEFAULT NULL,
  `requirement_name` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollment_uploaded_files`
--

INSERT INTO `enrollment_uploaded_files` (`id`, `enrollment_id`, `requirement_name`, `file_name`, `uploaded_at`) VALUES
(1, 1, 'FORM 137', 'req_6928a8f559d0c5.18428079_roses.png', '2025-11-27 19:39:33'),
(2, 1, 'PSA', 'req_6928a8f55b5b71.28012143_roses.png', '2025-11-27 19:39:33'),
(3, 1, 'REPORT CARD (GRADE 6)', 'req_6928a8f55c6b70.11786189_roses.png', '2025-11-27 19:39:33'),
(4, 2, 'FORM 137', 'req_6928aa96eeaab1.63801156_roses.png', '2025-11-27 19:46:30'),
(5, 2, 'PSA', 'req_6928aa96f05a16.95035408_roses.png', '2025-11-27 19:46:30'),
(6, 2, 'REPORT CARD (GRADE 6)', 'req_6928aa96f19478.56944826_roses.png', '2025-11-27 19:46:30'),
(7, 3, 'FORM 137', 'req_6928aa9c75e8d1.53720832_roses.png', '2025-11-27 19:46:36'),
(8, 3, 'PSA', 'req_6928aa9c7744a2.93538691_roses.png', '2025-11-27 19:46:36'),
(9, 3, 'REPORT CARD (GRADE 6)', 'req_6928aa9c78e999.91818255_roses.png', '2025-11-27 19:46:36'),
(10, 4, 'FORM 137', 'req_6928aae2e0f2a5.06718346_Messenger_creation_BED0F74C-42BF-42C0-8211-8381014E99D1.jpeg', '2025-11-27 19:47:46'),
(11, 4, 'PSA', 'req_6928aae2e27cf5.79842901_birth-certificate.jpg', '2025-11-27 19:47:46'),
(12, 4, 'REPORT CARD (GRADE 6)', 'req_6928aae2e3ad93.26431163_IMG20251126104841.jpg', '2025-11-27 19:47:46'),
(13, 5, 'FORM 137', 'req_6928ac19a4b256.51465600_roses.png', '2025-11-27 19:52:57'),
(14, 5, 'PSA', 'req_6928ac19a6a180.20233920_roses.png', '2025-11-27 19:52:57'),
(15, 5, 'REPORT CARD (GRADE 6)', 'req_6928ac19a80f90.09425897_roses.png', '2025-11-27 19:52:57'),
(16, 6, 'FORM 137', 'req_6928af4d1b3ee7.09979306_form137k-12.webp', '2025-11-27 20:06:37'),
(17, 6, 'PSA', 'req_6928af4d1c8361.85136312_PSA.jpg', '2025-11-27 20:06:37'),
(18, 6, 'REPORT CARD (GRADE 6)', 'req_6928af4d1d55f9.32712096_Report card.jpg', '2025-11-27 20:06:37'),
(19, 7, 'FORM 137', 'req_69281d1bebaf30.92105441_form137k-12.webp', '2025-11-27 09:42:51'),
(20, 7, 'PSA', 'req_69281d1bee3853.97565835_PSA.jpg', '2025-11-27 09:42:51'),
(21, 7, 'REPORT CARD (GRADE 6)', 'req_69281d1beff3f0.30287504_Report card.jpg', '2025-11-27 09:42:51'),
(22, 8, 'FORM 137', 'req_69270f16e7b1e4.53875879_Report card.jpg', '2025-11-26 14:30:46'),
(23, 8, 'PSA', 'req_69270f16e973f2.71455490_PSA.jpg', '2025-11-26 14:30:46'),
(24, 8, 'REPORT CARD (GRADE 6)', 'req_69270f16ea73a5.33918316_form137k-12.webp', '2025-11-26 14:30:46'),
(25, 11, 'FORM 137', 'req_69272786790196.99247919_form137k-12.webp', '2025-11-26 16:15:02'),
(26, 11, 'PSA', 'req_692727867a9461.04740145_PSA.jpg', '2025-11-26 16:15:02'),
(27, 11, 'REPORT CARD (GRADE 6)', 'req_692727867bbb63.50297421_Report card.jpg', '2025-11-26 16:15:02'),
(28, 12, 'FORM 137', 'req_6927278b654717.69529150_form137k-12.webp', '2025-11-26 16:15:07'),
(29, 12, 'PSA', 'req_6927278b68b703.62744339_PSA.jpg', '2025-11-26 16:15:07'),
(30, 12, 'REPORT CARD (GRADE 6)', 'req_6927278b6a9df6.75785624_Report card.jpg', '2025-11-26 16:15:07'),
(31, 16, 'FORM 137', 'req_6927decb496e53.91672212_form137k-12.webp', '2025-11-27 05:16:59'),
(32, 16, 'PSA', 'req_6927decb4b0529.12857265_PSA.jpg', '2025-11-27 05:16:59'),
(33, 16, 'REPORT CARD (GRADE 6)', 'req_6927decb4c3a83.68497222_Report card.jpg', '2025-11-27 05:16:59'),
(34, 17, 'FORM 137', 'req_692aa8864923d3.09161022_Screenshot 2025-09-04 190420.png', '2025-11-29 08:02:14'),
(35, 17, 'PSA', 'req_692aa8864b6f72.23113666_Screenshot 2025-09-07 062426.png', '2025-11-29 08:02:14'),
(36, 17, 'REPORT CARD (GRADE 6)', 'req_692aa8864c9a47.06177071_Screenshot 2025-09-07 062426.png', '2025-11-29 08:02:14'),
(37, 17, 'DTS', 'req_692aa886514d47.11735570_Screenshot 2025-09-06 084125.png', '2025-11-29 08:02:14'),
(38, 18, 'FORM 137', 'req_692b8941aa22f3.99587581_Screenshot 2025-09-06 115707.png', '2025-11-30 00:01:05'),
(39, 18, 'PSA', '1764461165_Screenshot 2025-09-07 062426.png', '2025-11-30 00:06:05');

-- --------------------------------------------------------

--
-- Table structure for table `grade_tbl`
--

CREATE TABLE `grade_tbl` (
  `grade_id` int(11) NOT NULL,
  `grade_name` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade_tbl`
--

INSERT INTO `grade_tbl` (`grade_id`, `grade_name`) VALUES
(1, 7),
(2, 8),
(3, 9),
(4, 10);

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `attempts_id` int(11) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `last_attempt` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `new_users`
--

CREATE TABLE `new_users` (
  `new_users_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `activation_token` varchar(100) NOT NULL,
  `user_status` int(11) DEFAULT 1,
  `profile_pic` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `new_users`
--

INSERT INTO `new_users` (`new_users_id`, `full_name`, `email`, `username`, `password`, `activation_token`, `user_status`, `profile_pic`) VALUES
(1, 'Jennex A. Minga', 'minga.jennex@marsu.edu.ph', 'Jennex', '$2y$10$2pONJaHNMi3KgmWZBRmOhuiKZ0IZJZCVbWddTfKHzq3ZiTC/kZxoa', '', 1, ''),
(2, 'Ailene P. Rejano', 'ailenerejano9@gmail.com', 'Ailene', '$2y$10$ZHHH2uyu47phCTRuxcn/c.CYUGwzXpb7OHzGOhwXZW/Y5eU3keGUi', '', 1, ''),
(4, 'Aira Jane Saporna', 'embingkristinenelly12@gmail.com', 'Aira', '$2y$10$i3SmlW7exUD.NeT1rJWLAu/vVQGTvP8oNXsczvNPPTQB.CCv/jiiG', '', 1, ''),
(5, 'Kim L. Mandia', 'mandia.kim@marsu.edu.ph', 'Kim Mandia', '$2y$10$M0twwLfKEQWxMkXMoRIu7e2FiFl8Cb5oHxq1xkORMu6rJSXTcb6H2', '', 1, ''),
(6, 'Dhomalin Suarez', 'dhomalin.miip@gmail.com', 'Dhoma', '$2y$10$FE0SjPy7plYMnRRRE4iVtuUiF8sY2rtYvfwBvbzC7Cv.lGygsImV2', '', 1, ''),
(7, 'John Doe', 'johndoe@gmail.com', 'johndoe', '$2y$10$SGkLaoWWKshVca9Jq32gdOJJqJRJi3MeSWknbfsKQuUOYPySzFxTm', '', 1, ''),
(8, 'Abegail Jouy L. Laylay', 'laylay.abegail@marsu.edu.ph', 'Laylay', '$2y$10$Cg9XYB1kctLkV.k/2CWUu.ELJO1kRPRzYbc6BKRNSx4Ixu1.06rY6', '', 1, ''),
(9, 'Melvin Carpio', 'melvz@gmail.com', 'melvz', '$2y$10$JXpB6qdys92Wb90LgXiolO4qoIkCB7ftNNJdoSMy.h8xPYyx8K.uK', '', 1, ''),
(10, 'Jillian M. Maming', 'jillianmaming04@gmail.com', 'Jillian', '$2y$10$dmDYkOHzKH319U/CzT5Oo.r7Hcw84ih1AVfX/Jy6utLePZ2rR1m0K', '', 1, ''),
(11, 'Jillian C. Olivar', 'olivarjillian31@gmail.com', 'Jill', '$2y$10$48CeN5xZVYejT7GX6Uny9.904V2LElvnTv763tmffrKqi5NGsH3du', '', 1, ''),
(12, 'Zyrus Lance R. Hermoso', 'kitoytoyhermoso@gmail.com', 'Zyrus Hermoso', '$2y$10$RrJtg1LIxeJJN0bhwX/7oORN8mg4gFp3LJs2vZZbYvs5AaeNAp2sS', '', 1, ''),
(13, 'Danielle Jean M. Macunat', 'daniellejeanmacunat@gmail.com', 'denden', '$2y$10$6JRPNggfSVryvJpcak9VEeE/5knRAP5uWYsA75poHQLH.XhrH2f8G', '', 1, ''),
(14, 'Karl Ashton S. Martin', 'ashtonmartin@gmail.com', 'Karl', '$2y$10$los5eaWCuqUzXnWynJUVDultCEDBtCc/wfUshn98Rwd.Rbq2NUHhW', '', 1, ''),
(15, 'Shalanie Platon', 'shalanie@gmail.com', 'Shalanie', '$2y$10$6DsTe71FivTiUbmnqAJKBuJ8QRcLWvu.CE/HVbDs7sfjeF0Ptp2Jy', '', 1, ''),
(16, 'alfred flores', 'alfredzxc129@gmail.com', 'alfred', '$2y$10$ktfeEbuhxk2PCN8iExdDru1nxR1LKi6CZbYK2tJt7laacYoOUjcSa', '', 1, '1764460817_Screenshot 2025-09-04 193856.png');

-- --------------------------------------------------------

--
-- Table structure for table `room_tbl`
--

CREATE TABLE `room_tbl` (
  `room_id` int(11) NOT NULL,
  `room_name` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_tbl`
--

INSERT INTO `room_tbl` (`room_id`, `room_name`) VALUES
(1, 'ROOM 1 '),
(2, 'ROOM 2'),
(3, 'ROOM 3'),
(4, 'ROOM 4'),
(5, 'ROOM 5');

-- --------------------------------------------------------

--
-- Table structure for table `school_events_tbl`
--

CREATE TABLE `school_events_tbl` (
  `event_id` int(11) NOT NULL,
  `event_title` varchar(255) DEFAULT NULL,
  `event_description` text DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `event_place` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_events_tbl`
--

INSERT INTO `school_events_tbl` (`event_id`, `event_title`, `event_description`, `event_date`, `event_time`, `event_place`) VALUES
(1, 'INDUCTION BALL', 'rgbjkdsbjsd', '2025-11-27', '19:00:00', 'BANGBANG NATIONAL HIGH SCHOOL');

-- --------------------------------------------------------

--
-- Table structure for table `section_tbl`
--

CREATE TABLE `section_tbl` (
  `section_id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `section_grade` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section_tbl`
--

INSERT INTO `section_tbl` (`section_id`, `section_name`, `section_grade`) VALUES
(1, 'EINSTEIN', '7'),
(2, 'DARWIN', '7'),
(3, 'PASCAL', '7'),
(4, 'LIBRA', '8'),
(5, 'CAPRICORN', '8'),
(6, 'PISCES', '8'),
(7, 'RUBY', '9'),
(8, 'EMERALD', '9'),
(9, 'GARNET', '9'),
(10, 'PLATINUM', '10'),
(11, 'SILVER', '10'),
(12, 'GOLD', '10'),
(13, 'SAGITTARIUS', '7');

-- --------------------------------------------------------

--
-- Table structure for table `student_dropout_tbl`
--

CREATE TABLE `student_dropout_tbl` (
  `dropout_id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `dropout_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_dropout_tbl`
--

INSERT INTO `student_dropout_tbl` (`dropout_id`, `student_id`, `dropout_date`, `reason`, `created_at`) VALUES
(1, '2025-003', '2025-11-30', 'FINANCIAL PROBLEMS', '2025-11-29 23:01:51');

-- --------------------------------------------------------

--
-- Table structure for table `student_scores_tbl`
--

CREATE TABLE `student_scores_tbl` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_scores_tbl`
--

INSERT INTO `student_scores_tbl` (`id`, `student_id`, `announcement_id`, `score`, `recorded_at`) VALUES
(1, '2025-001', 4, 45, '2025-11-28 20:34:47'),
(2, '2025-002', 4, 43, '2025-11-28 20:34:55'),
(3, '2025-003', 4, 48, '2025-11-28 20:35:04'),
(4, '2025-004', 4, 45, '2025-11-28 20:35:13'),
(5, '2025-005', 4, 50, '2025-11-28 20:35:24'),
(6, '2025-001', 5, 100, '2025-11-28 20:35:50'),
(7, '2025-002', 5, 100, '2025-11-28 20:35:56'),
(8, '2025-003', 5, 100, '2025-11-28 20:36:05'),
(9, '2025-004', 5, 100, '2025-11-28 20:36:11'),
(10, '2025-005', 5, 100, '2025-11-28 20:36:18'),
(11, '2025-001', 6, 90, '2025-11-28 20:36:38'),
(12, '2025-002', 6, 91, '2025-11-28 20:36:45'),
(13, '2025-003', 6, 80, '2025-11-28 20:36:53'),
(14, '2025-004', 6, 75, '2025-11-28 20:37:00'),
(15, '2025-005', 6, 85, '2025-11-28 20:37:11'),
(16, '2025-001', 7, 90, '2025-11-28 20:37:34'),
(17, '2025-002', 7, 79, '2025-11-28 20:37:44'),
(18, '2025-003', 7, 80, '2025-11-28 20:37:55'),
(19, '2025-004', 7, 80, '2025-11-28 20:38:12'),
(20, '2025-005', 7, 85, '2025-11-28 20:38:21'),
(21, '2025-001', 8, 20, '2025-11-28 20:38:37'),
(22, '2025-002', 8, 25, '2025-11-28 20:38:44'),
(23, '2025-003', 8, 25, '2025-11-28 20:38:52'),
(24, '2025-004', 8, 25, '2025-11-28 20:39:00'),
(25, '2025-005', 8, 15, '2025-11-28 20:39:07'),
(26, '2025-001', 10, 30, '2025-11-28 20:39:24'),
(27, '2025-002', 10, 25, '2025-11-28 20:39:30'),
(28, '2025-003', 10, 30, '2025-11-28 20:39:45'),
(29, '2025-004', 10, 50, '2025-11-28 20:39:52'),
(30, '2025-005', 10, 50, '2025-11-28 20:39:58'),
(31, '2025-001', 11, 79, '2025-11-28 20:40:11'),
(32, '2025-002', 11, 70, '2025-11-28 20:40:19'),
(33, '2025-003', 11, 60, '2025-11-28 20:40:26'),
(34, '2025-004', 11, 79, '2025-11-28 20:40:34'),
(35, '2025-005', 11, 1, '2025-11-28 20:40:40'),
(36, '2025-001', 12, 90, '2025-11-28 20:40:56'),
(37, '2025-002', 12, 85, '2025-11-28 20:41:02'),
(38, '2025-003', 12, 79, '2025-11-28 20:41:11'),
(39, '2025-004', 12, 80, '2025-11-28 20:41:18'),
(40, '2025-005', 12, 5, '2025-11-28 20:41:24'),
(41, '2025-001', 13, 60, '2025-11-28 20:41:57'),
(42, '2025-002', 13, 60, '2025-11-28 20:42:04'),
(43, '2025-003', 13, 80, '2025-11-28 20:42:10'),
(44, '2025-004', 13, 90, '2025-11-28 20:42:16'),
(45, '2025-005', 13, 60, '2025-11-28 20:42:24'),
(46, '2025-001', 14, 20, '2025-11-28 20:42:45'),
(47, '2025-002', 14, 10, '2025-11-28 20:42:51'),
(48, '2025-003', 14, 15, '2025-11-28 20:42:58'),
(49, '2025-004', 14, 25, '2025-11-28 20:43:05'),
(50, '2025-005', 14, 10, '2025-11-28 20:43:13'),
(51, '2025-001', 15, 50, '2025-11-28 20:43:33'),
(52, '2025-002', 15, 80, '2025-11-28 20:43:41'),
(53, '2025-003', 15, 87, '2025-11-28 20:43:47'),
(54, '2025-004', 15, 83, '2025-11-28 20:43:53'),
(55, '2025-005', 15, 30, '2025-11-28 20:43:58'),
(56, '2025-001', 16, 60, '2025-11-28 20:44:10'),
(57, '2025-002', 16, 65, '2025-11-28 20:44:16'),
(58, '2025-003', 16, 50, '2025-11-28 20:44:20'),
(59, '2025-004', 16, 70, '2025-11-28 20:44:26'),
(60, '2025-005', 16, 0, '2025-11-28 20:44:32'),
(61, '2025-002', 17, 40, '2025-11-27 09:36:57'),
(62, '2025-003', 17, 1, '2025-11-27 09:37:15'),
(63, '2025-008', 18, 70, '2025-11-26 14:51:17'),
(64, '2025-004', 19, 25, '2025-11-26 15:31:54'),
(65, '2025-009', 20, 65, '2025-11-27 16:46:01');

-- --------------------------------------------------------

--
-- Table structure for table `student_tbl`
--

CREATE TABLE `student_tbl` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `student_grade` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `year_graduate` varchar(10) DEFAULT NULL,
  `section_id` int(11) NOT NULL,
  `password` varchar(100) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `is_ssc` int(11) NOT NULL,
  `profile_pic` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_tbl`
--

INSERT INTO `student_tbl` (`id`, `student_id`, `enrollment_id`, `student_grade`, `created_at`, `year_graduate`, `section_id`, `password`, `status`, `is_ssc`, `profile_pic`) VALUES
(1, '2025-001', 1, '8', '2025-11-27 19:42:24', NULL, 4, '$2y$10$/BU3H0m9Xtt6xKBjBQJfUOpcESys55XTJaUjeq5L810Ob5T3R0GgK', 0, 0, ''),
(2, '2025-002', 2, '8', '2025-11-27 20:09:26', NULL, 4, '$2y$10$yS4bhikcxOgo/7rlIQXgJ.ONSikWQ2Lt1e94FaA5ER.3g2k4dsYQu', 0, 0, ''),
(3, '2025-003', 3, '8', '2025-11-27 20:10:48', NULL, 4, '$2y$10$x2dlFBTimtK1Vhs0cU/p1Og5GVX.L3aV6iVIECDASXl5rB5Cc9efK', 2, 0, ''),
(4, '2025-004', 4, '7', '2025-11-27 20:11:02', NULL, 1, '$2y$10$m/.90wAaQTV6WWo.f95dAuSjP4ococQ2aJLEGL2JlDWIMn2tdJSzW', 0, 1, ''),
(5, '2025-005', 5, '7', '2025-11-27 20:11:20', NULL, 1, '$2y$10$E5LKyoOZtsgohUlgRWlWEuwBn9lH6lhppFhoiGrgthAXM5U6t4TC.', 1, 0, ''),
(6, '2025-006', 6, '8', '2025-11-27 20:11:39', NULL, 4, '$2y$10$fN8UWcoyTSqesxttiIdgdO5uYnvSYwEeZ93wJ3qSn9FX9EyuCQCgy', 1, 0, '1764460150_Screenshot 2025-09-07 062426.png'),
(7, '2025-007', 7, '10', '2025-11-27 09:45:47', NULL, 10, '$2y$10$DbTXjT2yYHu6t1AEE9WM9Oe.lWNdK0TAdfkbGkBiolGetc1PlwD0K', 1, 0, ''),
(8, '2025-008', 8, '7', '2025-11-26 14:35:17', NULL, 3, '$2y$10$lD3kvStSZLLOoK6kDqMjW.d4IjY2Zb/JDDO6IcHQtWyGlrnJpWPGe', 1, 0, ''),
(9, '2025-009', 11, '7', '2025-11-26 16:18:18', NULL, 1, '$2y$10$e1KyHdIllvnrtweu3b9jkuCa8P94uW/8RImO6d/U65f7E4/DfZ5fm', 1, 0, ''),
(10, '2025-010', 13, '9', '2025-11-27 04:47:47', NULL, 8, '$2y$10$V8SJIW5VbrL9N7y9vFB52u/.3T6yRjI.8sDF9SsLLiuxej1n8V5sC', 1, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `student_transferee_tbl`
--

CREATE TABLE `student_transferee_tbl` (
  `transferee_id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `transfer_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `school_to_transfer` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_transferee_tbl`
--

INSERT INTO `student_transferee_tbl` (`transferee_id`, `student_id`, `transfer_date`, `reason`, `school_to_transfer`, `created_at`) VALUES
(4, '2025-002', '2025-11-30', 'HJSADSA', 'NAJSDNASJD', '2025-11-29 22:59:08');

-- --------------------------------------------------------

--
-- Table structure for table `subject_tbl`
--

CREATE TABLE `subject_tbl` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(50) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `subject_grade` varchar(100) NOT NULL,
  `teacher_assign` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject_tbl`
--

INSERT INTO `subject_tbl` (`subject_id`, `subject_code`, `subject_name`, `subject_grade`, `teacher_assign`) VALUES
(1, 'ENGLISH 7', 'ENGLISH', '7', '1236123'),
(2, 'MATH 7', 'MATHEMATHICS', '7', '301531-002'),
(3, 'SCIENCE 7', 'SCIENCE', '7', '301531-004'),
(4, 'AP 7', 'ARALING PANLIPUNAN', '7', '0'),
(5, 'TLE 7', 'TECHNOLOGY AND LIVELIHOOD EDUCATION (ICT)', '7', '1236123'),
(6, 'ESP 7', 'EDUKASYON SA PAGPAPAKATAO', '7', '301531-003'),
(7, 'FIL 7', 'FILIPINO', '7', '0'),
(8, 'MAPEH 7', 'MAPEH', '7', '0'),
(9, 'MATH 8', 'MATHEMATICS', '8', '0'),
(10, 'AP 8', 'ARALING PANLIPUNAN', '8', '0'),
(11, 'MAPEH 8', 'MAPEH', '8', '1236123'),
(12, 'SCIENCE 8', 'SCIENCE', '8', '0'),
(13, 'ENGLISH 8', 'ENGLISH', '8', '0'),
(14, 'ESP 8', 'EDUKASYON SA PAGPAPAKATAO', '8', '1236123'),
(15, 'TLE 8', 'TECHNOLOGY AND LIVELIHOOD EDUCATION', '8', '0'),
(16, 'FIL 8', 'FILIPINO', '8', '0'),
(17, 'MATH 9', 'MATHEMATICS', '9', '0'),
(18, 'SCIENCE 9', 'SCIENCE', '9', '0'),
(19, 'FIL 9', 'FILIPINO', '9', '1236123'),
(20, 'ENGLISH 9', 'ENGLISH', '9', '0'),
(21, 'MAPEH 9', 'MAPEH', '9', '0'),
(22, 'TLE 9', 'TECHNOLOGY AND LIVELIHOOD EDUCATION', '9', '0'),
(23, 'AP 9', 'ARALING PANLIPUNAN', '9', '0'),
(24, 'ESP 9', 'EDUKASYON SA PAGPAPAKATAO', '9', '0'),
(25, 'MATH 10', 'MATHEMATICS', '10', '0'),
(26, 'SCIENCE 10', 'SCIENCE', '10', '0'),
(27, 'FIL 10', 'FILIPINO', '10', '0'),
(28, 'ENGLISH 10', 'ENGLISH', '10', '0'),
(29, 'MAPEH 10', 'MAPEH', '10', '0'),
(30, 'TLE 10', 'TECHNOLOGY AND LIVELIHOOD EDUCATION', '10', '0'),
(31, 'ESP 10', 'EDUKASYON SA PAGPAPAKATAO', '10', '0'),
(32, 'AP 10', 'ARALING PANLIPUNAN', '10', '0');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_tbl`
--

CREATE TABLE `teacher_tbl` (
  `teacher_id` varchar(20) NOT NULL,
  `teacher_name` varchar(100) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `ext` varchar(50) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `teacher_type` enum('Class Adviser','Subject Teacher') NOT NULL,
  `grade_level` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `profile` varchar(200) NOT NULL,
  `teacher_status` int(11) NOT NULL DEFAULT 1,
  `specialization` varchar(255) DEFAULT NULL,
  `degree` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_tbl`
--

INSERT INTO `teacher_tbl` (`teacher_id`, `teacher_name`, `first_name`, `middle_name`, `last_name`, `ext`, `email`, `teacher_type`, `grade_level`, `section_id`, `password`, `profile`, `teacher_status`, `specialization`, `degree`) VALUES
('1236123', 'SARAH LABATI', 'SARAH', 'HJASHDJASH', 'JHASHDJAS', 'S', 'alfredzxc129@gmail.com', 'Subject Teacher', NULL, NULL, '$2y$10$aO1BYSS/FifTfRe6rvTv6.Lji7.X8ZdKrvjXdV3XQbmmQDf5RQB42', '1764459444_Screenshot 2025-09-07 062426.png', 1, '123127382173821', ''),
('2131273891273', 'ADSJHASJDHSAJDHJAH JHDJASHDJASHJ HDJASHDJASHD D', 'ADSJHASJDHSAJDHJAH', 'JHDJASHDJASHJ', 'HDJASHDJASHD', 'D', 'ashdasjhdjh@gmail.com', 'Class Adviser', 7, 2, '$2y$10$nAgsAZlQoIcHOZUSPnXOQ.85Ftk7F0f/oi/G0Vqeud95CSMBD6Nvq', 'teacher_6923a4c86dc4e6.85103750.png', 1, 'HUASJDHASJDHASJD', ''),
('301531-001', 'DINAH M. SUMAGUI ', 'DINAH', 'M.', 'SUMAGUI', NULL, 'dinah.sumagui@deped.gov.ph', 'Subject Teacher', NULL, NULL, '$2y$10$ks4mAGZyVTpPA1QY9XwnUukNYD00ak8Ism5s.5svtHmPvFd5oiQS6', 'teacher_691774d4f26227.65577153.jpg', 1, NULL, ''),
('301531-002', 'NIMFA S. SERDENA ', 'NIMFA', 'S.', 'SERDENA', NULL, 'nimfa.serdena@deped.gov.ph', 'Class Adviser', 7, 1, '$2y$10$xVXJd1VixcLaulno4cTLiOVA41OmcAMu/tsc/GrEnDFJAcakPWxBy', 'teacher_69177e2cbb7618.28495616.jpg', 1, NULL, ''),
('301531-003', 'FRANKLIN S. LAZO ', 'FRANKLIN', 'S.', 'LAZO', NULL, 'franklin.lazo@deped.gov.ph', 'Class Adviser', 7, 2, '$2y$10$u6Q3SX/nhBEXhsd4FQT/0u1.x4xho0CZmc6ixbUTtPsf5Ca7HOA4S', 'teacher_6916c654e318b7.94054255.jpg', 1, NULL, ''),
('301531-004', 'ETHEL M. MACAYSA ', 'ETHEL', 'M.', 'MACAYSA', NULL, 'ethel.macaysa@deped.gov.ph', 'Class Adviser', 7, 4, '$2y$10$4wE9aJoNYJsGTejoIW1WR.V5njki5nXsFLG0Ir2V3F839sa7KinFu', 'teacher_6916c6550cf376.23315572.jpg', 1, NULL, ''),
('301531-005', 'SHARMAINE M. DELOS REYES ', 'SHARMAINE', 'M.', 'DELOS REYES', NULL, 'sharmaine.delosreyes@deped.gov.ph', 'Class Adviser', 7, 5, '$2y$10$nuD.am297aGUhERYwdgyt.zbS1j/lDy6YibzZt5tZ9J0PrHd7IVsy', 'teacher_6916c754e06bf9.36085215.jpg', 1, NULL, ''),
('301531-006', 'MELANIE M. MANGANTE ', 'MELANIE', 'M.', 'MANGANTE', NULL, 'melanie.mangante@deped.gov.ph', 'Class Adviser', 7, 6, '$2y$10$M4ZsZ2bugQ403t245ibYg.l5rvFLln55N43GNMwATles.JoX1SHKG', 'teacher_6916c7550347a5.95338861.jpg', 1, NULL, ''),
('301531-011', 'LOIDA S. GARCIA ', 'LOIDA', 'S.', 'GARCIA', NULL, 'loida.garcia@deped.gov.ph', 'Class Adviser', 8, 3, '$2y$10$kHy85owoGVOUnSVA0f6.6OsOduZ8WrkBkl8bMrYv8iS3dq1MXosJ6', 'teacher_6916ceba8e8445.22984618.jpg', 1, NULL, ''),
('301531-012', 'NELMA M. MARTINEZ ', 'NELMA', 'M.', 'MARTINEZ', NULL, 'nelma.martinez@deped.gov.ph', 'Class Adviser', 8, 8, '$2y$10$GylOYR8pKeCz.JWfvLUEB.c54XzDaHP0xpqHNVCogg3JUHq5vSqhW', 'teacher_6916cebaaffa04.91414300.jpg', 1, NULL, ''),
('301531-013', 'ANN MARGARET L. PEROLINO ', 'ANN MARGARET', 'L.', 'PEROLINO', NULL, 'annmargaret.perolino@deped.gov.ph', 'Class Adviser', 8, 9, '$2y$10$d4gPbrE8VrWXq49Sud11rue9MKpvz8ecjdvs5CA6lKaP0Fbg7zS2y', 'teacher_6916cebacf7474.12477840.jpg', 1, NULL, ''),
('301531-014', 'CHONA M. TUMULAC ', 'CHONA', 'M.', 'TUMULAC', NULL, 'chona.tumulac@deped.gov.ph', 'Class Adviser', 8, 10, '$2y$10$atcJ8w5BOzGuFmnc/Pru0e.e8vepE9rFU9oxUTaoCM7nYb5XrbDC.', 'teacher_6916cebaee3535.71681184.jpg', 1, NULL, ''),
('301531-018', 'LINA M. OCTAVIO ', 'LINA', 'M.', 'OCTAVIO', NULL, 'lina.octavio@deped.gov.ph', 'Class Adviser', 9, 11, '$2y$10$pBAzjSf5S7O8HiOUw15L0edXQ4p2VUlfDlenzM1bcdIXPKd1nrYZO', 'teacher_6916d18bc44297.47667993.jpg', 1, NULL, ''),
('301531-019', 'JELLIE ANN L. JALAC ', 'JELLIE ANN', 'L.', 'JALAC', NULL, 'jellieann.jalac@deped.gov.ph', 'Class Adviser', 9, 12, '$2y$10$Qk7M88MZoe90IVnJeDxjPOb/DRVRS8ciL1krm6osAp2OpMFSF0kHm', 'teacher_6916d18be4a785.17469317.jpg', 1, NULL, ''),
('301531-020', 'DREXLER R. ROYO ', 'DREXLER', 'R.', 'ROYO', NULL, 'drexler.royo@deped.gov.ph', 'Class Adviser', 9, 13, '$2y$10$iXsjFoJMdK9eGvdohD.qwud7DE2BBv4JxmX53F4zHHp5OPbI.uLTW', 'teacher_6916d18c0fefe1.58636873.jpg', 1, NULL, ''),
('301531-021', 'LEA NESSA M. ORNEDO ', 'LEA NESSA', 'M.', 'ORNEDO', NULL, 'leanessa.ornedo@deped.gov.ph', 'Class Adviser', 9, 14, '$2y$10$/Z6CL0iBqZVluSAmm4Xy/.CvPFnUKadnZV3e9fSgZEk1lsNOkpotC', 'teacher_6916d18c2eeaa6.83864653.jpg', 1, NULL, ''),
('301531-022', 'RICHELLE Q. MOGOL ', 'RICHELLE', 'Q.', 'MOGOL', NULL, 'richelle.mogol@deped.gov.ph', 'Class Adviser', 9, 15, '$2y$10$cnlR/g6w.dZau6TP3ZEKBe.MdSXjvvEotwifGGpp6LMGu97czuX0S', 'teacher_6916d18c4e7381.04713334.jpg', 1, NULL, ''),
('301531-026', 'REGGIE P. LINASA ', 'REGGIE', 'P.', 'LINASA', NULL, 'reggie.linasa@deped.gov.ph', 'Class Adviser', 10, 16, '$2y$10$J0.uSooDtTVGJmTo44RIbO1.6hH03UqGXpe0uNAMjoINrdGwPtgRu', 'teacher_6916d2d955da34.06163263.jpg', 1, NULL, ''),
('301531-027', 'JORGETTE S. HIZOLE ', 'JORGETTE', 'S.', 'HIZOLE', NULL, 'jorgette.hizole@deped.gov.ph', 'Class Adviser', 10, 17, '$2y$10$/YyNmJB1gmL1YB6JeQq.O.Ctz1S50OlhM9hFUSpdGreqmJ1nMN4d.', 'teacher_6916d2d97568f9.93899129.jpg', 1, NULL, ''),
('301531-028', 'RIMALYN L. SABIDA ', 'RIMALYN', 'L.', 'SABIDA', NULL, 'rimalyn.sabida@deped.gov.ph', 'Class Adviser', 10, 18, '$2y$10$dgq2Z2H7ut658RM.t5NXP.LFcl5znIxJvNTBuXi/CZgSHXxK8j82K', 'teacher_6916d2d9944499.94096027.jpg', 1, NULL, ''),
('301531-029', 'ROGER S. SAGUID ', 'ROGER', 'S.', 'SAGUID', NULL, 'roger.saguid@deped.gov.ph', 'Class Adviser', 10, 19, '$2y$10$4R/3ttQr71nZbO7M1wRq7.E1pnCkKf0yopdRvYSVdtBlMF9P0obpa', 'teacher_6916d347dff739.61995457.jpg', 1, NULL, ''),
('301531-030', 'JOSEPHINE M. MACUNAT ', 'JOSEPHINE', 'M.', 'MACUNAT', NULL, 'josephine.macunat@deped.gov.ph', 'Class Adviser', 10, 20, '$2y$10$aJwmFHDcuVaiW2sxoJAddu/L5vQp9a7LdYM.h4tpKwtlkWPaLWPuC', 'teacher_6916d3480b39b9.03709500.jpg', 1, NULL, ''),
('301531-031', 'RONJIE MAR LEAL MALINAO ', 'RONJIE MAR', 'LEAL', 'MALINAO', NULL, 'alfredzxc129@gmail.com', 'Class Adviser', 7, 7, '$2y$10$epv.1nvdc/PLVb7b8ZLZ5eKx6s6I1no/nzdPNs2HyzlYw/yQoZHAC', 'teacher_6917a2b6505771.14181485.jpg', 1, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `userlogs_tbl`
--

CREATE TABLE `userlogs_tbl` (
  `log_id` int(11) NOT NULL,
  `userid` int(2) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `login_time` timestamp NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userlogs_tbl`
--

INSERT INTO `userlogs_tbl` (`log_id`, `userid`, `username`, `login_time`, `ip_address`) VALUES
(1, 1, 'admin', '2025-11-23 23:48:42', '::1'),
(2, 1, 'admin', '2025-11-23 23:50:01', '::1'),
(3, 0, 'juan', '2025-11-23 23:50:16', '::1'),
(4, 1, 'admin', '2025-11-24 00:02:40', '::1'),
(5, 1, '12345', '2025-11-24 00:27:18', '::1'),
(6, 1, 'admin', '2025-11-24 00:28:10', '::1'),
(7, 301531, '301531-001', '2025-11-24 00:28:59', '::1'),
(8, 1, 'admin', '2025-11-24 00:29:24', '::1'),
(9, 1, 'admin', '2025-11-24 00:35:59', '::1'),
(10, 2147483647, '2131273891273', '2025-11-24 00:44:21', '::1'),
(11, 1, 'admin', '2025-11-24 01:06:00', '::1'),
(12, 1, 'admin', '2025-11-27 11:12:33', '::1'),
(13, 0, 'Jennex', '2025-11-28 11:35:43', '::1'),
(14, 1, '12345', '2025-11-28 11:41:54', '::1'),
(15, 0, 'Ailene', '2025-11-28 11:42:11', '10.5.50.1'),
(16, 0, 'Kaye', '2025-11-28 11:43:32', '::1'),
(17, 0, 'Aira', '2025-11-28 11:48:43', '::1'),
(18, 0, 'Kim Mandia', '2025-11-28 12:03:41', '::1'),
(19, 1, '12345', '2025-11-28 12:08:03', '::1'),
(20, 1, 'admin', '2025-11-28 12:14:01', '::1'),
(21, 301531, '301531-002', '2025-11-28 12:17:13', '::1'),
(22, 301531, '301531-002', '2025-11-28 12:18:15', '::1'),
(23, 1, 'admin', '2025-11-28 12:22:17', '::1'),
(24, 301531, '301531-002', '2025-11-28 12:24:04', '::1'),
(25, 1, 'admin', '2025-11-29 12:48:06', '::1'),
(26, 2025, '2025-002', '2025-11-29 12:53:47', '::1'),
(27, 1, 'admin', '2025-11-29 12:55:23', '::1'),
(28, 2025, '2025-001', '2025-11-29 12:57:24', '::1'),
(29, 1, 'admin', '2025-11-29 12:57:56', '::1'),
(30, 1, 'admin', '2025-11-27 01:19:52', '::1'),
(31, 301531, '301531-002', '2025-11-27 01:27:41', '::1'),
(32, 2025, '2025-002', '2025-11-27 01:32:31', '::1'),
(33, 1, 'admin', '2025-11-27 01:34:13', '::1'),
(34, 301531, '301531-002', '2025-11-27 01:34:48', '::1'),
(35, 0, 'Dhoma', '2025-11-28 01:39:37', '::1'),
(36, 1, 'admin', '2025-11-28 01:44:23', '::1'),
(37, 1, '12345', '2025-11-28 01:45:01', '::1'),
(38, 1, 'admin', '2025-11-28 01:46:47', '::1'),
(39, 301531, '301531-001', '2025-11-28 01:47:23', '::1'),
(40, 1, 'admin', '2025-11-27 06:19:58', '::1'),
(41, 0, 'johndoe', '2025-11-27 06:26:15', '::1'),
(42, 1, '12345', '2025-11-27 06:32:37', '::1'),
(43, 301531, '301531-002', '2025-11-27 06:39:19', '::1'),
(44, 1, 'admin', '2025-11-27 06:55:14', '::1'),
(45, 1, 'admin', '2025-11-27 07:02:38', '::1'),
(46, 0, 'Laylay', '2025-11-27 07:16:09', '::1'),
(47, 1, '12345', '2025-11-27 07:23:10', '::1'),
(48, 301531, '301531-002', '2025-11-27 07:26:12', '::1'),
(49, 2025, '2025-004', '2025-11-27 07:36:42', '::1'),
(50, 0, 'Ailene', '2025-11-27 07:44:15', '10.131.39.224'),
(51, 2025, '2025-004', '2025-11-27 07:47:31', '::1'),
(52, 2025, '2025-004', '2025-11-27 07:50:02', '::1'),
(53, 1, 'admin', '2025-11-27 07:52:35', '::1'),
(54, 1, 'admin', '2025-11-27 07:53:16', '::1'),
(55, 0, 'melvz', '2025-11-27 08:09:01', '::1'),
(56, 1, '12345', '2025-11-27 08:16:10', '::1'),
(57, 1, 'admin ', '2025-11-27 08:26:56', '::1'),
(58, 301531, '301531-002', '2025-11-27 08:38:05', '::1'),
(59, 1, 'admin', '2025-11-27 20:19:40', '::1'),
(60, 0, 'Jillian', '2025-11-27 20:30:11', '::1'),
(61, 1, '12345', '2025-11-27 20:41:39', '::1'),
(62, 0, 'Jill', '2025-11-27 20:42:14', '::1'),
(63, 1, 'admin', '2025-11-27 20:48:28', '::1'),
(64, 2025, '2025-010', '2025-11-27 20:49:00', '::1'),
(65, 1, 'admin', '2025-11-27 20:56:55', '10.131.39.250'),
(66, 1, '12345', '2025-11-27 20:57:44', '10.131.39.250'),
(67, 1, 'admin', '2025-11-27 21:08:53', '10.131.39.59'),
(68, 0, 'Zyrus Hermoso', '2025-11-27 21:10:03', '::1'),
(69, 0, 'denden', '2025-11-27 21:10:49', '10.131.39.140'),
(70, 0, 'Karl', '2025-11-27 21:12:51', '10.131.39.59'),
(71, 0, 'Shalanie', '2025-11-27 21:15:48', '10.131.39.250'),
(72, 1, 'admin', '2025-11-27 21:49:16', '::1'),
(73, 1, '12345', '2025-11-27 21:49:56', '::1'),
(74, 1, 'admin', '2025-11-27 21:53:55', '::1'),
(75, 301531, '301531-011', '2025-11-29 04:57:47', '::1'),
(76, 1, 'admin', '2025-11-29 05:27:05', '::1'),
(77, 1, 'admin', '2025-11-29 05:28:35', '::1'),
(78, 1236123, '1236123', '2025-11-29 05:30:03', '::1'),
(79, 1, 'admin', '2025-11-29 05:35:10', '::1'),
(80, 1, 'admin ', '2025-11-29 05:36:09', '::1'),
(81, 1, '12345', '2025-11-29 05:37:15', '::1'),
(82, 1, 'admin', '2025-11-29 05:56:08', '::1'),
(83, 2025, '2025-001', '2025-11-29 05:57:29', '::1'),
(84, 1, 'admin', '2025-11-29 06:02:31', '::1'),
(85, 1, '212312', '2025-11-29 06:03:14', '::1'),
(86, 1, 'admin', '2025-11-29 06:07:03', '::1'),
(87, 1, '12345', '2025-11-29 06:29:14', '::1'),
(88, 301531, '301531-002', '2025-11-29 06:44:13', '::1'),
(89, 301531, '301531-002', '2025-11-29 07:00:33', '::1'),
(90, 1, 'admin', '2025-11-29 07:27:21', '::1'),
(91, 1, 'admin', '2025-11-29 07:49:59', '::1'),
(92, 1, 'admin', '2025-11-29 07:51:49', '::1'),
(93, 0, 'Ailene', '2025-11-29 07:57:43', '::1'),
(94, 1, 'admin', '2025-11-29 08:03:45', '::1'),
(95, 2025, '2025-001', '2025-11-29 08:06:05', '::1'),
(96, 2025, '2025-004', '2025-11-29 08:06:40', '::1'),
(97, 2025, '2025-004', '2025-11-29 08:09:16', '::1'),
(98, 1, 'admin', '2025-11-29 08:12:58', '::1'),
(99, 1, 'admin', '2025-11-29 22:41:04', '::1'),
(100, 1, 'admin', '2025-11-29 23:16:40', '::1'),
(101, 1236123, '1236123', '2025-11-29 23:34:17', '::1'),
(102, 1, 'admin', '2025-11-29 23:38:30', '::1'),
(103, 2025, '2025-006', '2025-11-29 23:38:50', '::1'),
(104, 2025, '2025-006', '2025-11-29 23:49:38', '::1'),
(105, 0, 'alfred', '2025-11-29 23:53:06', '::1'),
(106, 2147483647, '2131273891273', '2025-11-30 00:18:20', '::1'),
(107, 301531, '301531-002', '2025-11-30 00:19:17', '::1'),
(108, 301531, '301531-001', '2025-11-30 00:28:05', '::1'),
(109, 301531, '301531-002', '2025-11-30 00:28:23', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `user_tbl`
--

CREATE TABLE `user_tbl` (
  `userid` int(11) NOT NULL,
  `fullname` varchar(30) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(20) DEFAULT NULL,
  `password` varchar(300) DEFAULT NULL,
  `usertype` int(1) DEFAULT 1,
  `useractive` int(1) DEFAULT 1,
  `dateCreated` timestamp NULL DEFAULT current_timestamp(),
  `dateUpdated` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `contact_number` varchar(20) NOT NULL,
  `position` varchar(255) NOT NULL,
  `code` varchar(200) NOT NULL,
  `profile_pic` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_tbl`
--

INSERT INTO `user_tbl` (`userid`, `fullname`, `email`, `username`, `password`, `usertype`, `useractive`, `dateCreated`, `dateUpdated`, `contact_number`, `position`, `code`, `profile_pic`) VALUES
(1, 'juan dela cruz', 'ailen@gmail.com', 'admin', '$2y$10$My/YeQuIVDMfBOZW0fIMReho1xRyEanSCaAYzAV2tBPMUbUAFXasK', 1, 1, '2024-02-28 01:18:05', '2025-11-29 23:24:24', '09215813119', 'ADMIN', '', '1764458149_588585944_1495821338197432_97498539505536190_n.jpg'),
(50, 'NORMINDA S. MABAO', 'principal@gmail.com', '212312', '$2y$10$17bZ65/00.fpg/VX51L94uvhY29PT2LgKX4s8XVJ.gApCJN0o3MfO', 1, 1, '2025-07-12 00:00:16', '2025-11-13 19:18:37', '09385713056', 'PRINCIPAL', '', '0'),
(52, 'JUAN DELA CRUZ', 'mandia.kim@marsu.edu.ph', '12345', '$2y$10$pjUR8iLEr817jHnTIxivXOnKSFlgnd/FV3eXpgqtWEwIzcyoA3eXG', 1, 1, '2025-11-13 19:22:25', '2025-11-13 19:22:25', '09271717469', 'ENROLLMENT ADVISER', '', '0');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcement_tbl`
--
ALTER TABLE `announcement_tbl`
  ADD PRIMARY KEY (`announcement_jd`);

--
-- Indexes for table `attendance_tbl`
--
ALTER TABLE `attendance_tbl`
  ADD PRIMARY KEY (`attendance_id`);

--
-- Indexes for table `enrollment_period_tbl`
--
ALTER TABLE `enrollment_period_tbl`
  ADD PRIMARY KEY (`enrollment_period_id`);

--
-- Indexes for table `enrollment_quarters_tbl`
--
ALTER TABLE `enrollment_quarters_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollment_requirement_tbl`
--
ALTER TABLE `enrollment_requirement_tbl`
  ADD PRIMARY KEY (`enrollment_requirement_id`);

--
-- Indexes for table `enrollment_tbl`
--
ALTER TABLE `enrollment_tbl`
  ADD PRIMARY KEY (`enrollmentId`);

--
-- Indexes for table `enrollment_uploaded_files`
--
ALTER TABLE `enrollment_uploaded_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grade_tbl`
--
ALTER TABLE `grade_tbl`
  ADD PRIMARY KEY (`grade_id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`attempts_id`);

--
-- Indexes for table `new_users`
--
ALTER TABLE `new_users`
  ADD PRIMARY KEY (`new_users_id`);

--
-- Indexes for table `room_tbl`
--
ALTER TABLE `room_tbl`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `school_events_tbl`
--
ALTER TABLE `school_events_tbl`
  ADD PRIMARY KEY (`event_id`),
  ADD UNIQUE KEY `event_title` (`event_title`);

--
-- Indexes for table `section_tbl`
--
ALTER TABLE `section_tbl`
  ADD PRIMARY KEY (`section_id`);

--
-- Indexes for table `student_dropout_tbl`
--
ALTER TABLE `student_dropout_tbl`
  ADD PRIMARY KEY (`dropout_id`);

--
-- Indexes for table `student_scores_tbl`
--
ALTER TABLE `student_scores_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_tbl`
--
ALTER TABLE `student_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_transferee_tbl`
--
ALTER TABLE `student_transferee_tbl`
  ADD PRIMARY KEY (`transferee_id`);

--
-- Indexes for table `subject_tbl`
--
ALTER TABLE `subject_tbl`
  ADD PRIMARY KEY (`subject_id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`);

--
-- Indexes for table `teacher_tbl`
--
ALTER TABLE `teacher_tbl`
  ADD PRIMARY KEY (`teacher_id`),
  ADD KEY `fk_section` (`section_id`);

--
-- Indexes for table `userlogs_tbl`
--
ALTER TABLE `userlogs_tbl`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `user_tbl`
--
ALTER TABLE `user_tbl`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcement_tbl`
--
ALTER TABLE `announcement_tbl`
  MODIFY `announcement_jd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `attendance_tbl`
--
ALTER TABLE `attendance_tbl`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `enrollment_period_tbl`
--
ALTER TABLE `enrollment_period_tbl`
  MODIFY `enrollment_period_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enrollment_quarters_tbl`
--
ALTER TABLE `enrollment_quarters_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `enrollment_requirement_tbl`
--
ALTER TABLE `enrollment_requirement_tbl`
  MODIFY `enrollment_requirement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `enrollment_tbl`
--
ALTER TABLE `enrollment_tbl`
  MODIFY `enrollmentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `enrollment_uploaded_files`
--
ALTER TABLE `enrollment_uploaded_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `grade_tbl`
--
ALTER TABLE `grade_tbl`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `attempts_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `new_users`
--
ALTER TABLE `new_users`
  MODIFY `new_users_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `room_tbl`
--
ALTER TABLE `room_tbl`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `school_events_tbl`
--
ALTER TABLE `school_events_tbl`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `section_tbl`
--
ALTER TABLE `section_tbl`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `student_dropout_tbl`
--
ALTER TABLE `student_dropout_tbl`
  MODIFY `dropout_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_scores_tbl`
--
ALTER TABLE `student_scores_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `student_tbl`
--
ALTER TABLE `student_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `student_transferee_tbl`
--
ALTER TABLE `student_transferee_tbl`
  MODIFY `transferee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subject_tbl`
--
ALTER TABLE `subject_tbl`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `userlogs_tbl`
--
ALTER TABLE `userlogs_tbl`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `user_tbl`
--
ALTER TABLE `user_tbl`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
