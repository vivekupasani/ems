<?php
// Configure error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php_error.log'); // Adjust this path as needed

// Set response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database configuration
require_once 'config.php';

try {
    // Establish database connection
    $conn = connectDB();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    $conn->set_charset("utf8mb4");

    // Get and decode JSON input
    $input = file_get_contents('php://input');
    if (empty($input)) {
        throw new Exception('No input data received');
    }

    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input: ' . json_last_error_msg());
    }

    if (!isset($data['employees']) || !is_array($data['employees'])) {
        throw new Exception('No employee data received or invalid format');
    }

    // Prepare SQL statement (excludes Actions and Approval Status by design)
    $stmt = $conn->prepare("INSERT INTO employees (
        emp_code, institute_name, department, designation, location,
        joining_date, leaving_date, emp_category, full_name, gender,
        blood_group, nationality, dob, father_name, mother_name,
        spouse_name, mobile_number, alt_number, email, address,
        bank_name, branch_name, account_number, ifsc_code,
        pan_number, aadhar_number, salary_category,
        duty_hours, total_hours, hours_per_day, salary_pay_band,
        basic_salary, ca, da, hra, ta, ma, other_allowance, 
        pf_number, pf_join_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        throw new Exception('Prepare statement failed: ' . $conn->error);
    }

    // Process each employee record
    $successCount = 0;
    $errors = [];

    foreach ($data['employees'] as $index => $row) {
        try {
            // Map data to variables, explicitly ignoring Actions and Approval Status
            $params = [
                $row['Employee Code'] ?? null,
                $row['Institute Name'] ?? null,
                $row['Department'] ?? null,
                $row['Designation'] ?? null,
                $row['location'] ?? null,
                $row['Joining Date'] ?? null,
                $row['leaving Date'] ?? null,
                $row['Category'] ?? null,
                $row['Full Name'] ?? null,
                $row['Gender'] ?? null,
                $row['Blood Group'] ?? null,
                $row['Nationality'] ?? null,
                $row['DOB'] ?? null,
                $row['Father Name'] ?? null,
                $row['Mother Name'] ?? null,
                $row['Spouse Name'] ?? null,
                $row['Mobile'] ?? null,
                $row['Alt Number'] ?? null,
                $row['Email'] ?? null,
                $row['Address'] ?? null,
                $row['Bank Name'] ?? null,
                $row['Branch Name'] ?? null,
                $row['Account Number'] ?? null,
                $row['IFSC Code'] ?? null,
                $row['PAN Number'] ?? null,
                $row['Aadhar Number'] ?? null,
                $row['Salary Category'] ?? null,
                $row['Duty Hours'] ?? null,
                $row['Total Hours'] ?? null,
                $row['Hours per Day'] ?? null,
                $row['Salary Payband'] ?? null,
                $row['Basic Salary'] ?? null,
                $row['Conveyance Allowance'] ?? null,
                $row['DA'] ?? null,
                $row['HRA'] ?? null,
                $row['Travelling Allowance'] ?? null,
                $row['Medical Allowance'] ?? null,
                $row['Other Allowance'] ?? null,
                $row['PF Number'] ?? null,
                $row['PF Join Date'] ?? null
            ];

            // Bind parameters
            $stmt->bind_param("ssssssssssssssssssssssssssssssssssssssss", ...$params);

            // Execute the statement
            if (!$stmt->execute()) {
                throw new Exception("Execute failed for employee at index $index: " . $stmt->error);
            }
            $successCount++;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            error_log("Error processing employee at index $index: " . $e->getMessage());
        }
    }

    // Close resources
    $stmt->close();
    $conn->close();

    // Prepare response
    $response = [
        'success' => empty($errors),
        'message' => empty($errors) 
            ? "All $successCount employees imported successfully!" 
            : "$successCount employees imported with " . count($errors) . " errors",
        'errors' => $errors,
        'redirect' => 'ins_dashboard.php'
    ];
    http_response_code(200);
    echo json_encode($response);

} catch (Exception $e) {
    // Handle top-level errors
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }

    http_response_code(500);
    $response = [
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'errors' => [$e->getMessage()]
    ];
    error_log("Upload error: " . $e->getMessage());
    echo json_encode($response);
}

exit(); // Ensure no stray output
?>