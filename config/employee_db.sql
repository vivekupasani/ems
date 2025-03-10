-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 09, 2025 at 06:23 AM
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
-- Database: `employee_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `user_id`, `password`, `name`, `email`, `created_at`, `remember_token`, `token_expiry`) VALUES
(1, 'admin1', 'U2FsdGVkX19FEQWMfX2kJftsLJf+V9vA56+uOsEIldg=', 'meet', 'meet@gmail.com', '2025-03-05 04:36:21', 'random_token', '2025-12-31 23:59:59'),
(2, 'admin123', '5LAwI7DoCbgJ7iJawkTnc/SzgMfifKXfrzykAMdW9sM=', 'Vivek Upasani', 'v@v.com', '2025-03-09 05:10:14', 'caaa36fb3d8f0e652bfadb7d9b18a8c611acab6835fced79f48525f0d437b665', '2025-04-08 07:14:49');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `emp_code` varchar(50) DEFAULT NULL,
  `institute_name` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `leaving_date` date DEFAULT NULL,
  `emp_category` varchar(50) DEFAULT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `father_name` varchar(150) DEFAULT NULL,
  `mother_name` varchar(150) DEFAULT NULL,
  `spouse_name` varchar(150) DEFAULT NULL,
  `mobile_number` varchar(15) DEFAULT NULL,
  `alt_number` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(20) DEFAULT NULL,
  `ifsc_code` varchar(15) DEFAULT NULL,
  `pan_number` varchar(15) DEFAULT NULL,
  `aadhar_number` varchar(20) DEFAULT NULL,
  `salary_category` varchar(50) DEFAULT NULL,
  `other_salary_category` varchar(100) DEFAULT NULL,
  `duty_hours` decimal(5,2) DEFAULT NULL,
  `total_hours` decimal(5,2) DEFAULT NULL,
  `hours_per_day` decimal(5,2) DEFAULT NULL,
  `salary_pay_band` varchar(50) DEFAULT NULL,
  `basic_salary` decimal(10,2) DEFAULT NULL,
  `pf_number` varchar(50) DEFAULT NULL,
  `pf_join_date` date DEFAULT NULL,
  `ca` decimal(10,2) DEFAULT NULL,
  `da` decimal(10,2) DEFAULT NULL,
  `hra` decimal(10,2) DEFAULT NULL,
  `ta` decimal(10,2) DEFAULT NULL,
  `ma` decimal(10,2) DEFAULT NULL,
  `other_allowance` decimal(10,2) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `aadhar_copy` varchar(255) DEFAULT NULL,
  `pan_copy` varchar(255) DEFAULT NULL,
  `bank_copy` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `approval_status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `emp_code`, `institute_name`, `department`, `designation`, `location`, `joining_date`, `leaving_date`, `emp_category`, `full_name`, `gender`, `blood_group`, `nationality`, `dob`, `father_name`, `mother_name`, `spouse_name`, `mobile_number`, `alt_number`, `email`, `address`, `bank_name`, `branch_name`, `account_number`, `ifsc_code`, `pan_number`, `aadhar_number`, `salary_category`, `other_salary_category`, `duty_hours`, `total_hours`, `hours_per_day`, `salary_pay_band`, `basic_salary`, `pf_number`, `pf_join_date`, `ca`, `da`, `hra`, `ta`, `ma`, `other_allowance`, `profile_photo`, `aadhar_copy`, `pan_copy`, `bank_copy`, `created_at`, `updated_at`, `approval_status`) VALUES
(1, 'emp005', 'Institute A', 'IT', 'Developer', 'dn', '2023-01-01', '2024-12-19', 'adhoc', 'John Doe', 'Male', 'O+', 'indian', '1990-05-15', 'Father A', 'Mother A', 'Spouse A', '9876543210', '9123456780', 'john.doe@example.com', 'Address 1', 'pnb', 'Branch A', '1234567890', 'IFSC001', 'BAJPC4350M', '518053869918', 'ADHOC With PF', NULL, 8.00, 160.00, 8.00, 'PB-1', 50000.00, 'PF001', '2023-01-15', 2000.00, 3000.00, 1500.00, 1000.00, 700.00, 2000.00, '\"C:\\Users\\user\\Pictures\\Wallpapers\\range_rover_sport_park_city_edition_2024_5k-3840x2160.jpg\"', 'aadhar1.pdf', 'pan1.pdf', 'bank1.pdf', '2023-01-01 04:30:00', '2025-03-05 05:00:06', 'Approved'),
(2, 'EMP002', 'Institute B', 'HR', 'Manager', 'City B', '2022-06-15', NULL, 'Contract', 'Jane Smith', 'Female', 'A+', 'Indian', '1985-07-20', 'Father B', 'Mother B', NULL, '8765432109', '9213456789', 'jane.smith@example.com', 'Address 2', 'Bank B', 'Branch B', '0987654321', 'IFSC002', 'PAN002', 'AADHAR002', 'B', NULL, 7.50, 150.00, 7.50, 'PB-2', 60000.00, 'PF002', '2022-07-01', 2500.00, 3500.00, 1800.00, 1200.00, 600.00, 2500.00, 'photo2.jpg', 'aadhar2.pdf', 'pan2.pdf', 'bank2.pdf', '2022-06-15 03:30:00', '2025-03-05 04:47:44', 'Pending'),
(3, 'EMP003', 'Institute C', 'Finance', 'Analyst', 'City C', '2021-03-10', NULL, 'Permanent', 'Mike Johnson', 'Male', 'B+', 'Indian', '1992-11-12', 'Father C', 'Mother C', 'Spouse C', '7654321098', '9321456789', 'mike.johnson@example.com', 'Address 3', 'Bank C', 'Branch C', '1122334455', 'IFSC003', 'PAN003', 'AADHAR003', 'A', 'Special', 8.50, 170.00, 8.50, 'PB-1', 55000.00, 'PF003', '2021-03-20', 2200.00, 3200.00, 1600.00, 1100.00, 550.00, 2200.00, 'photo3.jpg', 'aadhar3.pdf', 'pan3.pdf', 'bank3.pdf', '2021-03-10 03:00:00', '2025-03-05 04:47:52', 'Pending'),
(4, 'EMP004', 'Institute D', 'Engineering', 'Technician', 'City D', '2020-08-05', NULL, 'Permanent', 'Alice Brown', 'Female', 'AB-', 'Indian', '1988-09-18', 'Father D', 'Mother D', NULL, '6543210987', '9432156789', 'alice.brown@example.com', 'Address 4', 'Bank D', 'Branch D', '5566778899', 'IFSC004', 'PAN004', 'AADHAR004', 'B', NULL, 9.00, 180.00, 9.00, 'PB-2', 58000.00, 'PF004', '2020-08-15', 2300.00, 3400.00, 1700.00, 1150.00, 575.00, 2300.00, 'photo4.jpg', 'aadhar4.pdf', 'pan4.pdf', 'bank4.pdf', '2020-08-05 02:30:00', '2020-08-05 02:30:00', ''),
(5, 'EMP005', 'Institute E', 'Marketing', 'Executive', 'City E', '2019-12-01', NULL, 'Contract', 'Tom Wilson', 'Male', 'O-', 'Indian', '1991-03-25', 'Father E', 'Mother E', 'Spouse E', '5432109876', '9543215678', 'tom.wilson@example.com', 'Address 5', 'Bank E', 'Branch E', '9988776655', 'IFSC005', 'PAN005', 'AADHAR005', 'A', NULL, 8.00, 160.00, 8.00, 'PB-3', 52000.00, 'PF005', '2019-12-15', 2100.00, 3100.00, 1550.00, 1050.00, 525.00, 2100.00, 'photo5.jpg', 'aadhar5.pdf', 'pan5.pdf', 'bank5.pdf', '2019-12-01 02:00:00', '2019-12-01 02:00:00', ''),
(6, 'EMP006', 'Institute F', 'Sales', 'Coordinator', 'City F', '2023-02-15', NULL, 'Permanent', 'Emma Davis', 'Female', 'A-', 'Indian', '1990-12-22', 'Father F', 'Mother F', NULL, '4321098765', '9654321567', 'emma.davis@example.com', 'Address 6', 'Bank F', 'Branch F', '6677889900', 'IFSC006', 'PAN006', 'AADHAR006', 'B', NULL, 8.25, 165.00, 8.25, 'PB-2', 56000.00, 'PF006', '2023-02-25', 2400.00, 3300.00, 1650.00, 1125.00, 562.50, 2400.00, 'photo6.jpg', 'aadhar6.pdf', 'pan6.pdf', 'bank6.pdf', '2023-02-15 05:00:00', '2023-02-15 05:00:00', ''),
(7, 'EMP007', 'Institute G', 'IT', 'Support', 'City G', '2022-04-20', NULL, 'Permanent', 'Robert Moore', 'Male', 'B-', 'Indian', '1993-07-08', 'Father G', 'Mother G', 'Spouse G', '3210987654', '9765432156', 'robert.moore@example.com', 'Address 7', 'Bank G', 'Branch G', '7788990011', 'IFSC007', 'PAN007', 'AADHAR007', 'A', 'Custom', 9.50, 190.00, 9.50, 'PB-3', 60000.00, 'PF007', '2022-05-01', 2600.00, 3700.00, 1850.00, 1225.00, 612.50, 2600.00, 'photo7.jpg', 'aadhar7.pdf', 'pan7.pdf', 'bank7.pdf', '2022-04-20 05:30:00', '2022-04-20 05:30:00', ''),
(8, 'EMP008', 'Institute H', 'Operations', 'Supervisor', 'City H', '2021-11-05', NULL, 'Contract', 'Laura White', 'Female', 'AB+', 'Indian', '1987-06-14', 'Father H', 'Mother H', NULL, '2109876543', '9876543212', 'laura.white@example.com', 'Address 8', 'Bank H', 'Branch H', '8899001122', 'IFSC008', 'PAN008', 'AADHAR008', 'B', NULL, 7.75, 155.00, 7.75, 'PB-1', 53000.00, 'PF008', '2021-11-15', 2150.00, 3150.00, 1575.00, 1075.00, 537.50, 2150.00, 'photo8.jpg', 'aadhar8.pdf', 'pan8.pdf', 'bank8.pdf', '2021-11-05 06:30:00', '2021-11-05 06:30:00', ''),
(9, 'EMP009', 'Institute I', 'HR', 'Recruiter', 'City I', '2020-10-12', NULL, 'Permanent', 'Oliver Martin', 'Male', 'O+', 'Indian', '1994-02-10', 'Father I', 'Mother I', NULL, '1098765432', '9987654321', 'oliver.martin@example.com', 'Address 9', 'Bank I', 'Branch I', '1234432112', 'IFSC009', 'PAN009', 'AADHAR009', 'A', NULL, 8.50, 170.00, 8.50, 'PB-2', 54000.00, 'PF009', '2020-10-22', 2200.00, 3200.00, 1600.00, 1100.00, 550.00, 2200.00, 'photo9.jpg', 'aadhar9.pdf', 'pan9.pdf', 'bank9.pdf', '2020-10-12 03:15:00', '2020-10-12 03:15:00', ''),
(11, 'EMP011', 'Institute A', 'IT', 'Developer', 'City A', '2023-01-01', NULL, 'Permanent', 'John Doe', 'Male', 'O+', 'Indian', '1990-05-15', 'Father A', 'Mother A', 'Spouse A', '9876543210', '9123456780', 'john.doe@example.com', 'Address 1', 'Bank A', 'Branch A', '1234567890', 'IFSC001', 'PAN001', 'AADHAR001', 'A', NULL, 8.00, 160.00, 8.00, 'PB-1', 50000.00, 'PF001', '2023-01-15', 2000.00, 3000.00, 1500.00, 1000.00, 500.00, 2000.00, NULL, NULL, NULL, NULL, '2024-12-31 04:59:47', '2024-12-31 04:59:47', '');

-- --------------------------------------------------------

--
-- Table structure for table `institute_users`
--

CREATE TABLE `institute_users` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `institute_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `school_users`
--

CREATE TABLE `school_users` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `school_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--


--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `institute_users`
--
ALTER TABLE `institute_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `school_users`
--
ALTER TABLE `school_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `institute_users`
--
ALTER TABLE `institute_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `school_users`
--
ALTER TABLE `school_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



CREATE TABLE history_emp (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_code VARCHAR(50),
    institute_name VARCHAR(100),
    department VARCHAR(100),
    designation VARCHAR(100),
    location VARCHAR(100),
    joining_date DATE,
    leaving_date DATE,
    emp_category VARCHAR(50),
    full_name VARCHAR(100),
    gender VARCHAR(10),
    blood_group VARCHAR(10),
    nationality VARCHAR(50),
    dob DATE,
    father_name VARCHAR(100),
    mother_name VARCHAR(100),
    spouse_name VARCHAR(100),
    mobile_number VARCHAR(15),
    alt_number VARCHAR(15),
    email VARCHAR(100),
    address TEXT,
    bank_name VARCHAR(100),
    branch_name VARCHAR(100),
    account_number VARCHAR(50),
    ifsc_code VARCHAR(20),
    pan_number VARCHAR(10),
    aadhar_number VARCHAR(12),
    salary_category VARCHAR(50),
    other_salary_category VARCHAR(50),
    duty_hours DECIMAL(5,2),
    total_hours DECIMAL(5,2),
    hours_per_day DECIMAL(5,2),
    salary_pay_band VARCHAR(50),
    basic_salary DECIMAL(15,2),
    pf_number VARCHAR(50),
    pf_join_date DATE,
    ca DECIMAL(15,2),
    da DECIMAL(15,2),
    hra DECIMAL(15,2),
    ta DECIMAL(15,2),
    ma DECIMAL(15,2),
    other_allowance DECIMAL(15,2),
    profile_photo VARCHAR(255),
    aadhar_copy VARCHAR(255),
    pan_copy VARCHAR(255),
    bank_copy VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    approval_status VARCHAR(20) DEFAULT 'pending'
);