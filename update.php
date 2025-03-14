<?php
session_start(); // Start session to store status message
// Include the database connection file
include 'config.php';

$conn = connectDB();
// Initialize the $employee array to store fetched data
$employee = [];

// Check if an ID is provided in the URL to fetch the employee data
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the employee data from the database
    $sql = "SELECT * FROM employees WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
    } else {
        echo "Employee not found.";
        exit();
    }

    $stmt->close();
}

// Handle the form submission for updating employee data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $id = $_POST['id'];
    $emp_code = $_POST['emp_code'];
    $institute_name = $_POST['institute_name'];
    $department = $_POST['department'];
    $designation = $_POST['designation'];
    $location = $_POST['location'];
    $joining_date = $_POST['joining_date'];
    $leaving_date = $_POST['leaving_date'];
    $emp_category = $_POST['emp_category'];
    $full_name = $_POST['full_name'];
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];
    $nationality = $_POST['nationality'];
    $dob = $_POST['dob'];
    $father_name = $_POST['father_name'];
    $mother_name = $_POST['mother_name'];
    $spouse_name = $_POST['spouse_name'];
    $mobile_number = $_POST['mobile_number'];
    $alt_number = $_POST['alt_number'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $bank_name = $_POST['bank_name'];
    $branch_name = $_POST['branch_name'];
    $account_number = $_POST['account_number'];
    $ifsc_code = $_POST['ifsc_code'];
    $pan_number = $_POST['pan_number'];
    $aadhar_number = $_POST['aadhar_number'];
    $salary_category = $_POST['salary_category'];
    $duty_hours = $_POST['duty_hours'];
    $total_hours = $_POST['total_hours'];
    $hours_per_day = $_POST['hours_per_day'];
    $salary_pay_band = $_POST['salary_pay_band'];
    $basic_salary = $_POST['basic_salary'];
    $pf_number = $_POST['pf_number'];
    $pf_join_date = $_POST['pf_join_date'];
    $ca = $_POST['ca'];
    $da = $_POST['da'];
    $hra = $_POST['hra'];
    $ma = $_POST['ma'];
    $ta = $_POST['ta'];
    $other_allowance = $_POST['other_allowance'];

    // Prepare the SQL query to update the employee data
    $sql = "UPDATE employees SET
            emp_code = ?,
            institute_name = ?,
            department = ?,
            designation = ?,
            location = ?,
            joining_date = ?,
            leaving_date = ?,
            emp_category = ?,
            full_name = ?,
            gender = ?,
            blood_group = ?,
            nationality = ?,
            dob = ?,
            father_name = ?,
            mother_name = ?,
            spouse_name = ?,
            mobile_number = ?,
            alt_number = ?,
            email = ?,
            address = ?,
            bank_name = ?,
            branch_name = ?,
            account_number = ?,
            ifsc_code = ?,
            pan_number = ?,
            aadhar_number = ?,
            salary_category = ?,
            duty_hours = ?,
            total_hours = ?,
            hours_per_day = ?,
            salary_pay_band = ?,
            basic_salary = ?,
            pf_number = ?,
            pf_join_date = ?,
            ca = ?,
            da = ?,
            hra = ?,
            ma = ?,
            ta = ?,
            other_allowance = ?
            WHERE id = ?";

    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssssssssssssssssssssssssssssss",
        $emp_code, $institute_name, $department, $designation, $location, $joining_date, $leaving_date, $emp_category, $full_name, $gender, $blood_group, $nationality, $dob, $father_name, $mother_name, $spouse_name, $mobile_number, $alt_number, $email, $address, $bank_name, $branch_name, $account_number, $ifsc_code, $pan_number, $aadhar_number, $salary_category, $duty_hours, $total_hours, $hours_per_day, $salary_pay_band, $basic_salary, $pf_number, $pf_join_date, $ca, $da, $hra, $ma, $ta, $other_allowance, $id);

    if ($stmt->execute()) {
        $_SESSION['update_status'] = 'success';
    } else {
        $_SESSION['update_status'] = 'error';
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Update Employee</title>
    <style>
        /* Toaster styles */
        .toaster {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 2rem;
            border-radius: 8px;
            color: white;
            z-index: 1000;
            transition: opacity 0.3s ease-in-out;
        }
        .toaster.success {
            background-color: #10B981;
        }
        .toaster.error {
            background-color: #EF4444;
        }
        .toaster.hidden {
            opacity: 0;
            visibility: hidden;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-gray-50">
    <!-- Toaster Notification -->
    <div id="toaster" class="toaster hidden">
        <span id="toaster-message"></span>
    </div>

    <div id="update-form" class="manage-section mt-6 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <form id="updateEmployeeForm" class="space-y-8" method="POST" action="update.php">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Employee Details Update Form</h2>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Employer Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($employee['id'] ?? ''); ?>" />
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employee Code</label>
                        <input type="text" name="emp_code" value="<?php echo htmlspecialchars($employee['emp_code'] ?? ''); ?>" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Institute Name</label>
                        <input type="text" name="institute_name" value="<?php echo htmlspecialchars($employee['institute_name'] ?? ''); ?>" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Department</label>
                        <input type="text" name="department" value="<?php echo htmlspecialchars($employee['department'] ?? ''); ?>" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Designation</label>
                        <input type="text" name="designation" value="<?php echo htmlspecialchars($employee['designation'] ?? ''); ?>" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Location</label>
                        <select name="location" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Location</option>
                            <option value="dn" <?php echo ($employee['location'] ?? '') === 'dn' ? 'selected' : ''; ?>>DN Campus</option>
                            <option value="mogri" <?php echo ($employee['location'] ?? '') === 'mogri' ? 'selected' : ''; ?>>Mogri Campus</option>
                            <option value="khetiwadi" <?php echo ($employee['location'] ?? '') === 'khetiwadi' ? 'selected' : ''; ?>>Khetiwadi Campus</option>
                            <option value="mbpatel" <?php echo ($employee['location'] ?? '') === 'mbpatel' ? 'selected' : ''; ?>>MB Patel Science College Campus</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date of Joining</label>
                        <input type="date" name="joining_date" value="<?php echo htmlspecialchars($employee['joining_date'] ?? ''); ?>" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date of Leaving</label>
                        <input type="date" name="leaving_date" value="<?php echo htmlspecialchars($employee['leaving_date'] ?? ''); ?>" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employee Category</label>
                        <select name="emp_category" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Category</option>
                            <option value="adhoc" <?php echo ($employee['emp_category'] ?? '') === 'adhoc' ? 'selected' : ''; ?>>Adhoc</option>
                            <option value="permanent" <?php echo ($employee['emp_category'] ?? '') === 'permanent' ? 'selected' : ''; ?>>Permanent</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Employee Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($employee['full_name'] ?? ''); ?>" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Gender</label>
                        <select name="gender" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Gender</option>
                            <option value="male" <?php echo ($employee['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo ($employee['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo ($employee['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Blood Group</label>
                        <select name="blood_group" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Blood Group</option>
                            <?php
                            $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                            foreach ($bloodGroups as $bg) {
                                echo "<option value='$bg'" . (($employee['blood_group'] ?? '') === $bg ? ' selected' : '') . ">$bg</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nationality</label>
                        <select name="nationality" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Nationality</option>
                            <option value="indian" <?php echo ($employee['nationality'] ?? '') === 'indian' ? 'selected' : ''; ?>>Indian</option>
                            <option value="other" <?php echo ($employee['nationality'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <input type="date" name="dob" value="<?php echo htmlspecialchars($employee['dob'] ?? ''); ?>" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Father Name</label>
                        <input type="text" name="father_name" value="<?php echo htmlspecialchars($employee['father_name'] ?? ''); ?>" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mother Name</label>
                        <input type="text" name="mother_name" value="<?php echo htmlspecialchars($employee['mother_name'] ?? ''); ?>" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Spouse Name</label>
                        <input type="text" name="spouse_name" value="<?php echo htmlspecialchars($employee['spouse_name'] ?? ''); ?>" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mobile Number</label>
                        <input type="tel" name="mobile_number" value="<?php echo htmlspecialchars($employee['mobile_number'] ?? ''); ?>" required pattern="[0-9]{10}" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alternative Number</label>
                        <input type="tel" name="alt_number" value="<?php echo htmlspecialchars($employee['alt_number'] ?? ''); ?>" pattern="[0-9]{10}" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email ID</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($employee['email'] ?? ''); ?>" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Complete Residential Address</label>
                        <textarea name="address" required rows="3" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($employee['address'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Bank Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bank Name</label>
                        <select name="bank_name" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Bank</option>
                            <option value="sbi" <?php echo ($employee['bank_name'] ?? '') === 'sbi' ? 'selected' : ''; ?>>SBI</option>
                            <option value="pnb" <?php echo ($employee['bank_name'] ?? '') === 'pnb' ? 'selected' : ''; ?>>PNB</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Branch Name</label>
                        <input type="text" name="branch_name" value="<?php echo htmlspecialchars($employee['branch_name'] ?? ''); ?>" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Account Number</label>
                        <input type="text" name="account_number" value="<?php echo htmlspecialchars($employee['account_number'] ?? ''); ?>" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">IFSC Code</label>
                        <input type="text" name="ifsc_code" value="<?php echo htmlspecialchars($employee['ifsc_code'] ?? ''); ?>" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statutory Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">PAN Number</label>
                        <input type="text" name="pan_number" value="<?php echo htmlspecialchars($employee['pan_number'] ?? ''); ?>" required pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Aadhar Number</label>
                        <input type="text" name="aadhar_number" value="<?php echo htmlspecialchars($employee['aadhar_number'] ?? ''); ?>" required pattern="[0-9]{12}" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Salary Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Salary Category</label>
                        <select name="salary_category" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Category</option>
                            <option value="adhoc_with_pf" <?php echo ($employee['salary_category'] ?? '') === 'adhoc_with_pf' ? 'selected' : ''; ?>>Adhoc with PF</option>
                            <option value="adhoc_without_pf" <?php echo ($employee['salary_category'] ?? '') === 'adhoc_without_pf' ? 'selected' : ''; ?>>Adhoc without PF</option>
                            <option value="5th_pay" <?php echo ($employee['salary_category'] ?? '') === '5th_pay' ? 'selected' : ''; ?>>5th Pay</option>
                            <option value="6th_pay" <?php echo ($employee['salary_category'] ?? '') === '6th_pay' ? 'selected' : ''; ?>>6th Pay</option>
                            <option value="other" <?php echo ($employee['salary_category'] ?? '') === 'other' ? 'selected' : ''; ?>>Any Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Duty Hours</label>
                        <input type="number" name="duty_hours" value="<?php echo htmlspecialchars($employee['duty_hours'] ?? ''); ?>" required step="0.01" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Hours</label>
                        <input type="number" name="total_hours" value="<?php echo htmlspecialchars($employee['total_hours'] ?? ''); ?>" required step="0.01" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hours Per Day</label>
                        <input type="number" name="hours_per_day" value="<?php echo htmlspecialchars($employee['hours_per_day'] ?? ''); ?>" required step="0.01" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Salary Pay Band</label>
                        <input type="text" name="salary_pay_band" value="<?php echo htmlspecialchars($employee['salary_pay_band'] ?? ''); ?>" required class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Basic Salary</label>
                        <input type="number" name="basic_salary" value="<?php echo htmlspecialchars($employee['basic_salary'] ?? ''); ?>" required step="0.01" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">PF Account Number (UAN)</label>
                        <input type="text" name="pf_number" value="<?php echo htmlspecialchars($employee['pf_number'] ?? ''); ?>" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">PF Join Date</label>
                        <input type="date" name="pf_join_date" value="<?php echo htmlspecialchars($employee['pf_join_date'] ?? ''); ?>" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Salary Additions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">City Allowance (CA)</label>
                        <input type="number" name="ca" value="<?php echo htmlspecialchars($employee['ca'] ?? ''); ?>" step="0.01" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Dearness Allowance (DA)</label>
                        <input type="number" name="da" value="<?php echo htmlspecialchars($employee['da'] ?? ''); ?>" step="0.01" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">House Rent Allowance (HRA)</label>
                        <input type="number" name="hra" value="<?php echo htmlspecialchars($employee['hra'] ?? ''); ?>" step="0.01" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Travelling Allowance (TA)</label>
                        <input type="number" name="ta" value="<?php echo htmlspecialchars($employee['ta'] ?? ''); ?>" step="0.01" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Medical Allowance (MA)</label>
                        <input type="number" name="ma" value="<?php echo htmlspecialchars($employee['ma'] ?? ''); ?>" step="0.01" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Other Allowance</label>
                        <input type="number" name="other_allowance" value="<?php echo htmlspecialchars($employee['other_allowance'] ?? ''); ?>" step="0.01" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Update Employee Details
                </button>
            </div>
        </form>
    </div>

    <script>
        function showToaster(message, type, redirect = false) {
            const toaster = document.getElementById('toaster');
            const toasterMessage = document.getElementById('toaster-message');
            
            toasterMessage.textContent = message;
            toaster.className = `toaster ${type}`;
            toaster.classList.remove('hidden');
            
            setTimeout(() => {
                toaster.classList.add('hidden');
                if (redirect) {
                    window.location.href = 'index.php#view-employees';
                }
            }, 3000);
        }

        <?php
        if (isset($_SESSION['update_status'])) {
            if ($_SESSION['update_status'] === 'success') {
                echo "showToaster('Employee details updated successfully!', 'success', true);";
            } else if ($_SESSION['update_status'] === 'error') {
                echo "showToaster('Error updating employee details.', 'error', true);";
            }
            unset($_SESSION['update_status']); // Clear the session variable after use
        }
        ?>
    </script>
</body>
</html>