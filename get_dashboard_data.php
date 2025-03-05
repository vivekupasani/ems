<?php
session_start();
// Set Headers
header('Content-Type: application/json');

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employee_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Total Employees
$totalEmployeesQuery = "SELECT COUNT(*) as total FROM employees";
$totalEmployees = $conn->query($totalEmployeesQuery)->fetch_assoc()['total'];

// Active Employees (without leaving date)
$activeEmployeesQuery = "SELECT COUNT(*) as active FROM employees WHERE leaving_date IS NULL";
$activeEmployees = $conn->query($activeEmployeesQuery)->fetch_assoc()['active'];

// Department Distribution
$departmentsQuery = "SELECT department, COUNT(*) as count 
                     FROM employees 
                     WHERE department IS NOT NULL AND department != ''
                     GROUP BY department";
$departmentsResult = $conn->query($departmentsQuery);
$departments = [];
while ($row = $departmentsResult->fetch_assoc()) {
    $departments[] = $row;
}

// Total Salary
$totalSalaryQuery = "SELECT COALESCE(SUM(basic_salary), 0) as total_salary FROM employees";
$totalSalary = $conn->query($totalSalaryQuery)->fetch_assoc()['total_salary'];

// Salary Ranges
$salaryRangesQuery = "SELECT 
    SUM(CASE WHEN basic_salary BETWEEN 0 AND 25000 THEN 1 ELSE 0 END) as '0-25K',
    SUM(CASE WHEN basic_salary BETWEEN 25001 AND 50000 THEN 1 ELSE 0 END) as '25K-50K',
    SUM(CASE WHEN basic_salary BETWEEN 50001 AND 75000 THEN 1 ELSE 0 END) as '50K-75K',
    SUM(CASE WHEN basic_salary BETWEEN 75001 AND 100000 THEN 1 ELSE 0 END) as '75K-100K',
    SUM(CASE WHEN basic_salary > 100000 THEN 1 ELSE 0 END) as '100K+'
FROM employees";
$salaryRangesResult = $conn->query($salaryRangesQuery);
$salaryRanges = $salaryRangesResult->fetch_row();

// Prepare Response
$response = [
    'totalEmployees' => $totalEmployees,
    'activeEmployees' => $activeEmployees,
    'departments' => $departments,
    'totalSalary' => $totalSalary,
    'salaryRanges' => $salaryRanges
];

echo json_encode($response);

$conn->close();
?>