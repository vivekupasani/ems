-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2025 at 12:00 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+05:30"; -- Indian Standard Time (IST)

--
-- Database: `employee_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--
INSERT INTO `users` (`id`, `email`, `password`, `name`, `created_at`) VALUES
(1, 'admin.rahul.sharma@gmail.com', 'hashed_pass123', 'Rahul Sharma', '2025-03-05 04:36:21'),
(2, 'arun.kumar@example.com', 'hashed_pass456', 'Arun Kumar', '2023-01-01 04:30:00'),
(3, 'priya.verma@example.com', 'hashed_pass789', 'Priya Verma', '2022-06-15 03:30:00'),
(4, 'institute.vikram@example.com', 'hashed_pass101', 'Vikram Singh', '2025-03-05 05:00:00'),
(5, 'school.neha@example.com', 'hashed_pass102', 'Neha Patel', '2025-03-05 05:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--
INSERT INTO `admin_users` (`id`, `user_id`, `password`, `name`, `email`, `created_at`, `remember_token`, `token_expiry`) VALUES
(1, 'admin001', '0192023a7bbd73250516f069df18b500', 'Rahul Sharma', 'admin.rahul.sharma@gmail.com', '2025-03-05 04:36:21', 'random_token_india', '2025-12-31 23:59:59'),
(2, 'admin002', 'hashed_pass_admin2', 'Suman Gupta', 'suman.gupta@example.com', '2025-03-05 05:00:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `institute_users`
--
CREATE TABLE `institute_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `institute_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `institute_users`
--
INSERT INTO `institute_users` (`id`, `user_id`, `password`, `name`, `email`, `institute_name`, `created_at`, `remember_token`, `token_expiry`) VALUES
(1, 'inst001', '5LAwI7DoCbgJ7iJawkTnc/SzgMfifKXfrzykAMdW9sM=', 'Vikram Singh', 'institute.vikram@example.com', 'IIT Delhi', '2025-03-05 05:00:00', NULL, NULL),
(2, 'inst002', 'hashed_pass103', 'Anjali Nair', 'institute.anjali@example.com', 'NIT Trichy', '2025-03-05 05:10:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `school_users`
--
CREATE TABLE `school_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `school_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_users`
--
INSERT INTO `school_users` (`id`, `user_id`, `password`, `name`, `email`, `school_name`, `created_at`, `remember_token`, `token_expiry`) VALUES
(1, 'school001', 'hashed_pass102', 'Neha Patel', 'school.neha@example.com', 'DPS Mumbai', '2025-03-05 05:00:00', NULL, NULL),
(2, 'school002', 'hashed_pass104', 'Rakesh Yadav', 'school.rakesh@example.com', 'KV Bangalore', '2025-03-05 05:15:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--
CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `approval_status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--
INSERT INTO `employees` (`id`, `emp_code`, `institute_name`, `department`, `designation`, `location`, `joining_date`, `leaving_date`, `emp_category`, `full_name`, `gender`, `blood_group`, `nationality`, `dob`, `father_name`, `mother_name`, `spouse_name`, `mobile_number`, `alt_number`, `email`, `address`, `bank_name`, `branch_name`, `account_number`, `ifsc_code`, `pan_number`, `aadhar_number`, `salary_category`, `other_salary_category`, `duty_hours`, `total_hours`, `hours_per_day`, `salary_pay_band`, `basic_salary`, `pf_number`, `pf_join_date`, `ca`, `da`, `hra`, `ta`, `ma`, `other_allowance`, `profile_photo`, `aadhar_copy`, `pan_copy`, `bank_copy`, `created_at`, `updated_at`, `approval_status`) VALUES
(1, 'EMP001', 'IIT Delhi', 'IT', 'Software Engineer', 'Delhi', '2023-01-01', '2024-12-19', 'Adhoc', 'Arun Kumar', 'Male', 'O+', 'Indian', '1990-05-15', 'Ramesh Kumar', 'Sita Devi', 'Pooja Kumar', '9876543210', '9123456780', 'arun.kumar@example.com', 'Flat 101, Rohini, Delhi', 'State Bank of India', 'Rohini Branch', '123456789012', 'SBIN0001234', 'ABCDE1234F', '123456789012', 'Adhoc With PF', NULL, 8.00, 160.00, 8.00, 'Level-6', 50000.00, 'PF123456', '2023-01-15', 2000.00, 3000.00, 1500.00, 1000.00, 700.00, 2000.00, 'arun_profile.jpg', 'arun_aadhar.pdf', 'arun_pan.pdf', 'arun_bank.pdf', '2023-01-01 04:30:00', '2025-03-05 05:00:06', 'Approved'),
(2, 'EMP002', 'NIT Trichy', 'HR', 'HR Manager', 'Tiruchirappalli', '2022-06-15', NULL, 'Contract', 'Priya Verma', 'Female', 'A+', 'Indian', '1985-07-20', 'Mohan Verma', 'Geeta Verma', NULL, '8765432109', '9213456789', 'priya.verma@example.com', '12, Anna Nagar, Trichy', 'HDFC Bank', 'Trichy Branch', '987654321098', 'HDFC0005678', 'FGHIJ5678K', '987654321098', 'Contract', NULL, 7.50, 150.00, 7.50, 'Level-7', 60000.00, 'PF654321', '2022-07-01', 2500.00, 3500.00, 1800.00, 1200.00, 600.00, 2500.00, 'priya_profile.jpg', 'priya_aadhar.pdf', 'priya_pan.pdf', 'priya_bank.pdf', '2022-06-15 03:30:00', '2025-03-05 04:47:44', 'Pending'),
(3, 'EMP003', 'IIT Delhi', 'Finance', 'Financial Analyst', 'Delhi', '2021-03-10', NULL, 'Permanent', 'Ravi Patel', 'Male', 'B+', 'Indian', '1992-11-12', 'Suresh Patel', 'Laxmi Patel', 'Meena Patel', '7654321098', '9321456789', 'ravi.patel@example.com', 'C-45, Vasant Kunj, Delhi', 'ICICI Bank', 'Vasant Kunj Branch', '112233445566', 'ICIC0004321', 'KLMNO9876P', '456789123456', 'Permanent', NULL, 8.50, 170.00, 8.50, 'Level-6', 55000.00, 'PF789123', '2021-03-20', 2200.00, 3200.00, 1600.00, 1100.00, 550.00, 2200.00, 'ravi_profile.jpg', 'ravi_aadhar.pdf', 'ravi_pan.pdf', 'ravi_bank.pdf', '2021-03-10 03:00:00', '2025-03-05 04:47:52', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `history_emp`
--
CREATE TABLE `history_emp` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `emp_code` VARCHAR(50),
  `institute_name` VARCHAR(100),
  `department` VARCHAR(100),
  `designation` VARCHAR(100),
  `location` VARCHAR(100),
  `joining_date` DATE,
  `leaving_date` DATE,
  `emp_category` VARCHAR(50),
  `full_name` VARCHAR(100),
  `gender` VARCHAR(10),
  `blood_group` VARCHAR(10),
  `nationality` VARCHAR(50),
  `dob` DATE,
  `father_name` VARCHAR(100),
  `mother_name` VARCHAR(100),
  `spouse_name` VARCHAR(100),
  `mobile_number` VARCHAR(15),
  `alt_number` VARCHAR(15),
  `email` VARCHAR(100),
  `address` TEXT,
  `bank_name` VARCHAR(100),
  `branch_name` VARCHAR(100),
  `account_number` VARCHAR(50),
  `ifsc_code` VARCHAR(20),
  `pan_number` VARCHAR(10),
  `aadhar_number` VARCHAR(12),
  `salary_category` VARCHAR(50),
  `other_salary_category` VARCHAR(50),
  `duty_hours` DECIMAL(5,2),
  `total_hours` DECIMAL(5,2),
  `hours_per_day` DECIMAL(5,2),
  `salary_pay_band` VARCHAR(50),
  `basic_salary` DECIMAL(15,2),
  `pf_number` VARCHAR(50),
  `pf_join_date` DATE,
  `ca` DECIMAL(15,2),
  `da` DECIMAL(15,2),
  `hra` DECIMAL(15,2),
  `ta` DECIMAL(15,2),
  `ma` DECIMAL(15,2),
  `other_allowance` DECIMAL(15,2),
  `profile_photo` VARCHAR(255),
  `aadhar_copy` VARCHAR(255),
  `pan_copy` VARCHAR(255),
  `bank_copy` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `approval_status` VARCHAR(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history_emp`
--
INSERT INTO `history_emp` (`id`, `emp_code`, `institute_name`, `department`, `designation`, `location`, `joining_date`, `leaving_date`, `emp_category`, `full_name`, `gender`, `blood_group`, `nationality`, `dob`, `father_name`, `mother_name`, `spouse_name`, `mobile_number`, `alt_number`, `email`, `address`, `bank_name`, `branch_name`, `account_number`, `ifsc_code`, `pan_number`, `aadhar_number`, `salary_category`, `other_salary_category`, `duty_hours`, `total_hours`, `hours_per_day`, `salary_pay_band`, `basic_salary`, `pf_number`, `pf_join_date`, `ca`, `da`, `hra`, `ta`, `ma`, `other_allowance`, `profile_photo`, `aadhar_copy`, `pan_copy`, `bank_copy`, `created_at`, `updated_at`, `approval_status`) VALUES
(1, 'EMP001', 'IIT Delhi', 'IT', 'Software Engineer', 'Delhi', '2023-01-01', '2024-12-19', 'Adhoc', 'Arun Kumar', 'Male', 'O+', 'Indian', '1990-05-15', 'Ramesh Kumar', 'Sita Devi', 'Pooja Kumar', '9876543210', '9123456780', 'arun.kumar@example.com', 'Flat 101, Rohini, Delhi', 'State Bank of India', 'Rohini Branch', '123456789012', 'SBIN0001234', 'ABCDE1234F', '123456789012', 'Adhoc With PF', NULL, 8.00, 160.00, 8.00, 'Level-6', 50000.00, 'PF123456', '2023-01-15', 2000.00, 3000.00, 1500.00, 1000.00, 700.00, 2000.00, 'arun_profile.jpg', 'arun_aadhar.pdf', 'arun_pan.pdf', 'arun_bank.pdf', '2023-01-01 04:30:00', '2025-03-05 05:00:06', 'Approved'),
(2, 'EMP002', 'NIT Trichy', 'HR', 'HR Manager', 'Tiruchirappalli', '2022-06-15', '2025-01-01', 'Contract', 'Priya Verma', 'Female', 'A+', 'Indian', '1985-07-20', 'Mohan Verma', 'Geeta Verma', NULL, '8765432109', '9213456789', 'priya.verma@example.com', '12, Anna Nagar, Trichy', 'HDFC Bank', 'Trichy Branch', '987654321098', 'HDFC0005678', 'FGHIJ5678K', '987654321098', 'Contract', NULL, 7.50, 150.00, 7.50, 'Level-7', 60000.00, 'PF654321', '2022-07-01', 2500.00, 3500.00, 1800.00, 1200.00, 600.00, 2500.00, 'priya_profile.jpg', 'priya_aadhar.pdf', 'priya_pan.pdf', 'priya_bank.pdf', '2022-06-15 03:30:00', '2025-03-05 04:47:44', 'Pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `institute_users`
--
ALTER TABLE `institute_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `school_users`
--
ALTER TABLE `school_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `history_emp`
--
ALTER TABLE `history_emp`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `institute_users`
--
ALTER TABLE `institute_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `school_users`
--
ALTER TABLE `school_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `history_emp`
--
ALTER TABLE `history_emp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

COMMIT;