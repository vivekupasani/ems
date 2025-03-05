<?php
// Prevent any output before JSON response
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Ensure we're sending JSON response
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config.php';

$conn = connectDB();

$data = json_decode(file_get_contents('php://input'), true);

header('Content-Type: application/json');

if ($data && isset($data['employees'])) {
    try {
        foreach ($data['employees'] as $row) {
            $emp_code = $row['emp_code'] ?? null;
            $institute_name = $row['institute_name'] ?? null;
            $department = $row['department'] ?? null;
            $designation = $row['designation'] ?? null;
            $location = $row['location'] ?? null;
            $joining_date = $row['joining_date'] ?? null;
            $leaving_date = $row['leaving_date'] ?? null;
            $emp_category = $row['emp_category'] ?? null;
            $full_name = $row['full_name'] ?? null;
            $gender = $row['gender'] ?? null;
            $blood_group = $row['blood_group'] ?? null;
            $nationality = $row['nationality'] ?? null;
            $dob = $row['dob'] ?? null;
            $father_name = $row['father_name'] ?? null;
            $mother_name = $row['mother_name'] ?? null;
            $spouse_name = $row['spouse_name'] ?? null;
            $mobile_number = $row['mobile_number'] ?? null;
            $alt_number = $row['alt_number'] ?? null;
            $email = $row['email'] ?? null;
            $address = $row['address'] ?? null;
            $bank_name = $row['bank_name'] ?? null;
            $branch_name = $row['branch_name'] ?? null;
            $account_number = $row['account_number'] ?? null;
            $ifsc_code = $row['ifsc_code'] ?? null;
            $pan_number = $row['pan_number'] ?? null;
            $aadhar_number = $row['aadhar_number'] ?? null;
            $salary_category = $row['salary_category'] ?? null;
            $duty_hours = $row['duty_hours'] ?? null;
            $total_hours = $row['total_hours'] ?? null;
            $hours_per_day = $row['hours_per_day'] ?? null;
            $salary_pay_band = $row['salary_pay_band'] ?? null;
            $basic_salary = $row['basic_salary'] ?? null;
            $ca = $row['ca'] ?? null;
            $da = $row['da'] ?? null;
            $hra = $row['hra'] ?? null;
            $ta = $row['ta'] ?? null;
            $ma = $row['ma'] ?? null;
            $other_allowance = $row['other_allowance'] ?? null;
            $pf_number = $row['pf_number'] ?? null;
            $pf_join_date = $row['pf_join_date'] ?? null;

            $stmt = $conn->prepare("INSERT INTO employees (
                emp_code, institute_name, department, designation, location,
                joining_date, leaving_date, emp_category, full_name, gender,
                blood_group, nationality, dob, father_name, mother_name,
                spouse_name, mobile_number, alt_number, email, address,
                bank_name, branch_name, account_number, ifsc_code,
                pan_number, aadhar_number, salary_category,
                duty_hours, total_hours, hours_per_day, salary_pay_band,
                basic_salary, ca, da, hra, ta, ma, other_allowance, pf_number, pf_join_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }

            $stmt->bind_param("ssssssssssssssssssssssssssssssssssssssss", 
                $emp_code, $institute_name, $department, $designation, $location,
                $joining_date, $leaving_date, $emp_category, $full_name, $gender,
                $blood_group, $nationality, $dob, $father_name, $mother_name,
                $spouse_name, $mobile_number, $alt_number, $email, $address,
                $bank_name, $branch_name, $account_number, $ifsc_code,
                $pan_number, $aadhar_number, $salary_category,
                $duty_hours, $total_hours, $hours_per_day, $salary_pay_band,
                $basic_salary, $ca, $da, $hra, $ta, $ma, $other_allowance, $pf_number, $pf_join_date
            );

            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }

            $stmt->close();
        }

        $conn->close();
        echo json_encode(['message' => 'Employees imported successfully!']);
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['message' => 'No data received.']);
}
?>