<?php
// Include the database connection file
include 'config.php';

// Initialize response array for AJAX requests
$response = array('success' => false, 'message' => '');

// Function to validate and sanitize input data
function sanitizeInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to format dates
function formatDate($date)
{
    if (empty($date))
        return null;
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d ? $d->format('Y-m-d') : null;
}

// Fetch employee data if ID is provided
$employee = null;
if (isset($_GET['id'])) {
    $conn = connectDB();
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
}
// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn = connectDB();

        // Validate and sanitize all inputs
        $employeeData = array();
        foreach ($_POST as $key => $value) {
            if ($key !== 'submit') { // Skip the submit button
                // Handle date fields
                if (in_array($key, ['joining_date', 'leaving_date', 'dob', 'pf_join_date'])) {
                    $employeeData[$key] = formatDate($value);
                } else {
                    $employeeData[$key] = sanitizeInput($value);
                }
            }
        }

        // Prepare the UPDATE query
        $query = "UPDATE employees SET 
            emp_code = ?, institute_name = ?, department = ?, designation = ?,
            location = ?, joining_date = ?, leaving_date = ?, emp_category = ?,
            full_name = ?, gender = ?, blood_group = ?, nationality = ?,
            dob = ?, father_name = ?, mother_name = ?, spouse_name = ?,
            mobile_number = ?, alt_number = ?, email = ?, address = ?,
            bank_name = ?, branch_name = ?, account_number = ?, ifsc_code = ?,
            pan_number = ?, aadhar_number = ?, salary_category = ?,
            duty_hours = ?, total_hours = ?, hours_per_day = ?,
            salary_pay_band = ?, basic_salary = ?, pf_number = ?,
            pf_join_date = ?, ca = ?, da = ?, hra = ?, ta = ?, ma = ?,
            other_allowance = ?
            WHERE id = ?";

        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param(
            "ssssssssssssssssssssssssssssssssssssssssi",
            $employeeData['emp_code'],
            $employeeData['institute_name'],
            $employeeData['department'],
            $employeeData['designation'],
            $employeeData['location'],
            $employeeData['joining_date'],
            $employeeData['leaving_date'],
            $employeeData['emp_category'],
            $employeeData['full_name'],
            $employeeData['gender'],
            $employeeData['blood_group'],
            $employeeData['nationality'],
            $employeeData['dob'],
            $employeeData['father_name'],
            $employeeData['mother_name'],
            $employeeData['spouse_name'],
            $employeeData['mobile_number'],
            $employeeData['alt_number'],
            $employeeData['email'],
            $employeeData['address'],
            $employeeData['bank_name'],
            $employeeData['branch_name'],
            $employeeData['account_number'],
            $employeeData['ifsc_code'],
            $employeeData['pan_number'],
            $employeeData['aadhar_number'],
            $employeeData['salary_category'],
            $employeeData['duty_hours'],
            $employeeData['total_hours'],
            $employeeData['hours_per_day'],
            $employeeData['salary_pay_band'],
            $employeeData['basic_salary'],
            $employeeData['pf_number'],
            $employeeData['pf_join_date'],
            $employeeData['ca'],
            $employeeData['da'],
            $employeeData['hra'],
            $employeeData['ta'],
            $employeeData['ma'],
            $employeeData['other_allowance'],
            $employeeData['id']
        );

        // Execute the statement
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Employee details updated successfully';
        } else {
            throw new Exception("Error executing statement: " . $stmt->error);
        }

        $stmt->close();

        // If it's an AJAX request, return JSON response
        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        // For regular form submission, redirect to viewemp.php
        header("Location: viewemp.php");
        exit;

    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = 'Error: ' . $e->getMessage();

        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        // For regular form submission, show error and link to go back
        echo "Error: " . $e->getMessage();
        echo "<br><a href='javascript:history.back()'>Go Back</a>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    <script src="static/script.js">
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="static/style.css">
</head>

<body class="min-h-screen flex flex-col bg-gray-50">
    <!-- update form -->
    <div id="update-form" class="manage-section mt-6" class="hidden">
        <form id="updateEmployeeForm" class="space-y-8" method="POST" action="update.php">
            <!-- Employer Details Section -->

            <div class="bg-gray-50 p-6 rounded-lg">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Employee Details Update Form</h2>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Employer Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employee id</label>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($employee['id']); ?>"
                            class="mt-1 block p-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employee Code</label>
                        <input type="text" name="emp_code"
                            value="<?php echo htmlspecialchars($employee['emp_code']); ?>" required
                            class="mt-1 block p-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Institute Name</label>
                        <input type="text" name="institute_name"
                            value="<?php echo htmlspecialchars($employee['institute_name']); ?>" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Department</label>
                        <input type="text" name="department"
                            value="<?php echo htmlspecialchars($employee['department']); ?>" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Designation</label>
                        <input type="text" name="designation"
                            value="<?php echo htmlspecialchars($employee['designation']); ?>" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Location</label>
                        <select name="location" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Location</option>
                            <option value="dn" <?php echo ($employee['location'] == 'DN Campus') ? 'selected' : ''; ?>>DN
                                Campus</option>
                            <option value="mogri" <?php echo ($employee['location'] == 'Mogri Campus') ? 'selected' : ''; ?>>Mogri Campus</option>
                            <option value="khetiwadi" <?php echo ($employee['location'] == 'Khetiwadi Campus') ? 'selected' : ''; ?>>Khetiwadi Campus</option>
                            <option value="mbpatel" <?php echo ($employee['location'] == 'MB Patel Science College Campus') ? 'selected' : ''; ?>>MB Patel Science College Campus</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date of Joining</label>
                        <input type="date" name="joining_date"
                            value="<?php echo formatDate($employee['joining_date']); ?>"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date of Leaving</label>
                        <input type="date" name="leaving_date"
                            value="<?php echo formatDate($employee['leaving_date']); ?>"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employee Category</label>
                        <select name="emp_category" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Category</option>
                            <option value="adhoc" <?php echo $employee['emp_category'] == 'Adhoc' ? 'selected' : ''; ?>>
                                Adhoc</option>
                            <option value="permanent" <?php echo $employee['emp_category'] == 'Permanent' ? 'selected' : ''; ?>>Permanent</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Employee Personal Details Section -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Employee Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="full_name"
                            value="<?php echo htmlspecialchars($employee['full_name']); ?>" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Gender</label>
                        <select name="gender" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Gender</option>
                            <option value="male" <?php echo strtolower($employee['gender']) == 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo strtolower($employee['gender']) == 'female' ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo strtolower($employee['gender']) == 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Blood Group</label>
                        <select name="blood_group" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Blood Group</option>
                            <?php
                            $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                            foreach ($bloodGroups as $bg) {
                                echo "<option value=\"$bg\"" . ($employee['blood_group'] == $bg ? ' selected' : '') . ">$bg</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nationality</label>
                        <select name="nationality" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Nationality</option>
                            <option value="indian" <?php echo $employee['nationality'] == 'indian' ? 'selected' : ''; ?>>
                                Indian</option>
                            <option value="other" <?php echo $employee['nationality'] == 'other' ? 'selected' : ''; ?>>
                                Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <input type="date" name="dob" value="<?php echo formatDate($employee['dob']); ?>"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Father Name</label>
                        <input type="text" name="father_name"
                            value="<?php echo htmlspecialchars($employee['father_name']); ?>" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mother Name</label>
                        <input type="text" name="mother_name"
                            value="<?php echo htmlspecialchars($employee['mother_name']); ?>" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Spouse Name</label>
                        <input type="text" name="spouse_name"
                            value="<?php echo htmlspecialchars($employee['spouse_name']); ?>"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mobile Number</label>
                        <input type="tel" name="mobile_number"
                            value="<?php echo htmlspecialchars($employee['mobile_number']); ?>" required
                            pattern="[0-9]{10}"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alternative Number</label>
                        <input type="tel" name="alt_number"
                            value="<?php echo htmlspecialchars($employee['alt_number']); ?>" pattern="[0-9]{10}"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email ID</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>"
                            required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Complete Residential Address</label>
                        <textarea name="address" required rows="3"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($employee['address']); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Bank Details Section -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Bank Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bank Name</label>
                        <select name="bank_name" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Bank</option>
                            <option value="sbi" <?php echo $employee['bank_name'] == 'SBI' ? 'selected' : ''; ?>>SBI
                            </option>
                            <option value="pnb" <?php echo $employee['bank_name'] == 'PNB' ? 'selected' : ''; ?>>PNB
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Branch Name</label>
                        <input type="text" name="branch_name"
                            value="<?php echo htmlspecialchars($employee['branch_name']); ?>" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Account Number</label>
                        <input type="text" name="account_number"
                            value="<?php echo htmlspecialchars($employee['account_number']); ?>" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">IFSC Code</label>
                        <input type="text" name="ifsc_code"
                            value="<?php echo htmlspecialchars($employee['ifsc_code']); ?>"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                </div>
            </div>

            <!-- Statutory Details Section -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statutory Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">PAN Number</label>
                        <input type="text" name="pan_number"
                            value="<?php echo htmlspecialchars($employee['pan_number']); ?>" required
                            pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Aadhar Number</label>
                        <input type="text" name="aadhar_number"
                            value="<?php echo htmlspecialchars($employee['aadhar_number']); ?>" required
                            pattern="[0-9]{12}"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                </div>
            </div>

            <!-- Salary Details Section -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Salary Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Salary Category</label>
                        <select name="salary_category" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Category</option>
                            <option value="adhoc_with_pf" <?php echo $employee['salary_category'] == 'Adhoc with PF' ? 'selected' : ''; ?>>Adhoc with PF</option>
                            <option value="adhoc_without_pf" <?php echo $employee['salary_category'] == 'Adhoc without PF' ? 'selected' : ''; ?>>Adhoc without PF</option>
                            <option value="5th_pay" <?php echo $employee['salary_category'] == '5th Pay' ? 'selected' : ''; ?>>5th Pay</option>
                            <option value="6th_pay" <?php echo $employee['salary_category'] == '6th Pay' ? 'selected' : ''; ?>>6th Pay</option>
                            <option value="other" <?php echo $employee['salary_category'] == 'Any Other' ? 'selected' : ''; ?>>Any Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Duty Hours</label>
                        <input type="number" name="duty_hours"
                            value="<?php echo htmlspecialchars($employee['duty_hours']); ?>" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Hours</label>
                        <input type="number" name="total_hours"
                            value="<?php echo htmlspecialchars($employee['total_hours']); ?>" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hours Per Day(if hourly basis)</label>
                        <input type="number" name="hours_per_day"
                            value="<?php echo htmlspecialchars($employee['hours_per_day']); ?>" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Salary Pay Band</label>
                        <input type="text" name="salary_pay_band"
                            value="<?php echo htmlspecialchars($employee['salary_pay_band']); ?>" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Basic Salary</label>
                        <input type="number" name="basic_salary"
                            value="<?php echo htmlspecialchars($employee['basic_salary']); ?>" required
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">PF Account Number (UAN)</label>
                        <input type="text" name="pf_number"
                            value="<?php echo htmlspecialchars($employee['pf_number']); ?>"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">PF Join Date</label>
                        <input type="date" name="pf_join_date"
                            value="<?php echo formatDate($employee['pf_join_date']); ?>"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                </div>
            </div>

            <!-- Salary Additions Section -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Salary Additions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">City Allowance (CA)</label>
                        <input type="number" name="ca" value="<?php echo htmlspecialchars($employee['ca']); ?>"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Dearness Allowance (DA)</label>
                        <input type="number" name="da" value="<?php echo htmlspecialchars($employee['da']); ?>"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">House Rent Allowance (HRA)</label>
                        <input type="number" name="hra" value="<?php echo htmlspecialchars($employee['hra']); ?>"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Travelling Allowance (TA)</label>
                        <input type="number" name="ta" value="<?php echo htmlspecialchars($employee['ta']); ?>"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Medical Allowance (MA)</label>
                        <input type="number" name="ma" value="<?php echo htmlspecialchars($employee['ma']); ?>"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Other Allowance</label>
                        <input type="number" name="other_allowance"
                            value="<?php echo htmlspecialchars($employee['other_allowance']); ?>"
                            class="mt-1 block w-full p-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Update Employee Details
                </button>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const updateForm = document.getElementById('updateEmployeeForm');

            if (updateForm) {
                updateForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const formData = new FormData(this);

                    fetch('update.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => {
                            // Check if the response is JSON
                            const contentType = response.headers.get('content-type');
                            if (contentType && contentType.includes('application/json')) {
                                return response.json();
                            }
                            throw new Error('Server returned non-JSON response');
                        })
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                window.location.href = 'viewemp.php';
                            } else {
                                alert(data.message || 'Error updating employee details');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while updating employee details. Please try again.');
                        });
                });
            }
        });
    </script>
</body>

</html>