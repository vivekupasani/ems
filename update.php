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
    $emp_code = $_POST['emp_code'] ?? null;
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
            emp_code = NULLIF(?, ''),
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
    $stmt->bind_param(
        "sssssssssssssssssssssssssssssssssssssssss",
        $emp_code,
        $institute_name,
        $department,
        $designation,
        $location,
        $joining_date,
        $leaving_date,
        $emp_category,
        $full_name,
        $gender,
        $blood_group,
        $nationality,
        $dob,
        $father_name,
        $mother_name,
        $spouse_name,
        $mobile_number,
        $alt_number,
        $email,
        $address,
        $bank_name,
        $branch_name,
        $account_number,
        $ifsc_code,
        $pan_number,
        $aadhar_number,
        $salary_category,
        $duty_hours,
        $total_hours,
        $hours_per_day,
        $salary_pay_band,
        $basic_salary,
        $pf_number,
        $pf_join_date,
        $ca,
        $da,
        $hra,
        $ma,
        $ta,
        $other_allowance,
        $id
    );

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
    <script src="js/validation.js"></script>
    <script src="js/toast.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

<body class="bg-gray-100 min-h-screen font-sans antialiased">
    <div class="container mx-auto px-4 py-12 max-w-7xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">Update Employee Details</h1>
            <p class="mt-2 text-base text-gray-600">Modify employee information for your institute. Required fields are
                marked with <span class="text-red-500">*</span>.</p>
        </div>

        <!-- Form -->
        <form id="updateEmployeeForm" class="space-y-10" method="POST" action="update.php">
            <!-- Hidden ID field -->
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($employee['id'] ?? ''); ?>" />

            <!-- Employer Details -->
            <div
                class="bg-white p-8 rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2 border-gray-200">Employer Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Institute Name / School Name<span
                                class="text-red-500">*</span></label>
                        <input type="text" name="institute_name"
                            value="<?php echo htmlspecialchars($employee['institute_name'] ?? ''); ?>"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 <?php echo $user_type === 'institute' ? 'bg-gray-100 cursor-not-allowed' : ''; ?>" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Employee Code <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="emp_code"
                            value="<?php echo htmlspecialchars($employee['emp_code'] ?? ''); ?>" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Department <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="department"
                            value="<?php echo htmlspecialchars($employee['department'] ?? ''); ?>" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Designation <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="designation"
                            value="<?php echo htmlspecialchars($employee['designation'] ?? ''); ?>" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Location <span
                                class="text-red-500">*</span></label>
                        <select name="location" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400">
                            <option value="">Select Location</option>
                            <option value="dn" <?php echo ($employee['location'] ?? '') === 'dn' ? 'selected' : ''; ?>>DN
                                Campus</option>
                            <option value="mogri" <?php echo ($employee['location'] ?? '') === 'mogri' ? 'selected' : ''; ?>>Mogri Campus</option>
                            <option value="khetiwadi" <?php echo ($employee['location'] ?? '') === 'khetiwadi' ? 'selected' : ''; ?>>Khetiwadi Campus</option>
                            <option value="mbpatel" <?php echo ($employee['location'] ?? '') === 'mbpatel' ? 'selected' : ''; ?>>MB Patel Science College</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Joining <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="joining_date"
                            value="<?php echo htmlspecialchars($employee['joining_date'] ?? ''); ?>" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Leaving</label>
                        <input type="date" name="leaving_date"
                            value="<?php echo htmlspecialchars($employee['leaving_date'] ?? ''); ?>"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Employee Category <span
                                class="text-red-500">*</span></label>
                        <select name="emp_category" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400">
                            <option value="">Select Category</option>
                            <option value="adhoc" <?php echo ($employee['emp_category'] ?? '') === 'adhoc' ? 'selected' : ''; ?>>Adhoc</option>
                            <option value="permanent" <?php echo ($employee['emp_category'] ?? '') === 'permanent' ? 'selected' : ''; ?>>Permanent</option>
                        </select>
                    </div>

                </div>
            </div>

            <!-- Personal Information -->
            <div
                class="bg-white p-8 rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2 border-gray-200">Personal Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="full_name"
                            value="<?php echo htmlspecialchars($employee['full_name'] ?? ''); ?>" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Gender <span
                                class="text-red-500">*</span></label>
                        <select name="gender" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400">
                            <option value="">Select Gender</option>
                            <option value="male" <?php echo ($employee['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>
                                Male</option>
                            <option value="female" <?php echo ($employee['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo ($employee['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Blood Group <span
                                class="text-red-500">*</span></label>
                        <select name="blood_group" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400">
                            <option value="">Select Blood Group</option>
                            <?php
                            $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                            foreach ($bloodGroups as $bg) {
                                $selected = ($employee['blood_group'] ?? '') === $bg ? 'selected' : '';
                                echo "<option value='$bg' $selected>$bg</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nationality <span
                                class="text-red-500">*</span></label>
                        <select name="nationality" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400">
                            <option value="">Select Nationality</option>
                            <option value="indian" <?php echo ($employee['nationality'] ?? '') === 'indian' ? 'selected' : ''; ?>>Indian</option>
                            <option value="other" <?php echo ($employee['nationality'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Birth <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="dob" value="<?php echo htmlspecialchars($employee['dob'] ?? ''); ?>"
                            required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Father's Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="father_name"
                            value="<?php echo htmlspecialchars($employee['father_name'] ?? ''); ?>" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mother's Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="mother_name"
                            value="<?php echo htmlspecialchars($employee['mother_name'] ?? ''); ?>" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Spouse Name</label>
                        <input type="text" name="spouse_name"
                            value="<?php echo htmlspecialchars($employee['spouse_name'] ?? ''); ?>"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mobile Number <span
                                class="text-red-500">*</span></label>
                        <input type="tel" name="mobile_number"
                            value="<?php echo htmlspecialchars($employee['mobile_number'] ?? ''); ?>" required
                            pattern="[0-9]{10}"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Alternative Number</label>
                        <input type="tel" name="alt_number"
                            value="<?php echo htmlspecialchars($employee['alt_number'] ?? ''); ?>" pattern="[0-9]{10}"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email ID <span
                                class="text-red-500">*</span></label>
                        <input type="email" name="email"
                            value="<?php echo htmlspecialchars($employee['email'] ?? ''); ?>" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Complete Residential Address <span
                                class="text-red-500">*</span></label>
                        <textarea name="address" required rows="4"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400"><?php echo htmlspecialchars($employee['address'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Bank Details -->
            <div
                class="bg-white p-8 rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2 border-gray-200">Bank Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Bank Name <span
                                class="text-red-500">*</span></label>
                        <select name="bank_name" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400">
                            <option value="">Select Bank</option>
                            <option value="sbi" <?php echo ($employee['bank_name'] ?? '') === 'sbi' ? 'selected' : ''; ?>>
                                State Bank of India</option>
                            <option value="pnb" <?php echo ($employee['bank_name'] ?? '') === 'pnb' ? 'selected' : ''; ?>>
                                Punjab National Bank</option>
                            <option value="bob" <?php echo ($employee['bank_name'] ?? '') === 'bob' ? 'selected' : ''; ?>>
                                Bank of Baroda</option>
                            <option value="hdfc" <?php echo ($employee['bank_name'] ?? '') === 'hdfc' ? 'selected' : ''; ?>>HDFC Bank</option>
                            <option value="icici" <?php echo ($employee['bank_name'] ?? '') === 'icici' ? 'selected' : ''; ?>>ICICI Bank</option>
                            <option value="axis" <?php echo ($employee['bank_name'] ?? '') === 'axis' ? 'selected' : ''; ?>>Axis Bank</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Branch Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="branch_name"
                            value="<?php echo htmlspecialchars($employee['branch_name'] ?? ''); ?>" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Account Number <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="account_number"
                            value="<?php echo htmlspecialchars($employee['account_number'] ?? ''); ?>" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">IFSC Code <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="ifsc_code"
                            value="<?php echo htmlspecialchars($employee['ifsc_code'] ?? ''); ?>" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                </div>
            </div>

            <!-- Statutory Details -->
            <div
                class="bg-white p-8 rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2 border-gray-200">Statutory Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">PAN Number <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="pan_number"
                            value="<?php echo htmlspecialchars($employee['pan_number'] ?? ''); ?>" required
                            pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                        <p class="mt-1 text-xs text-gray-500">Format: ABCDE1234F</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Aadhar Number <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="aadhar_number"
                            value="<?php echo htmlspecialchars($employee['aadhar_number'] ?? ''); ?>" required
                            pattern="[0-9]{12}"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                        <p class="mt-1 text-xs text-gray-500">12-digit number</p>
                    </div>
                </div>
            </div>

            <!-- Salary Details -->
            <div
                class="bg-white p-8 rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2 border-gray-200">Salary Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Salary Category <span
                                class="text-red-500">*</span></label>
                        <select name="salary_category" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400">
                            <option value="">Select Category</option>
                            <option value="ADHOC With PF" <?php echo ($employee['salary_category'] ?? '') === 'ADHOC With PF' ? 'selected' : ''; ?>>Adhoc with PF</option>
                            <option value="ADHOC Without PF" <?php echo ($employee['salary_category'] ?? '') === 'ADHOC Without PF' ? 'selected' : ''; ?>>Adhoc without PF</option>
                            <option value="5th Pay" <?php echo ($employee['salary_category'] ?? '') === '5th Pay' ? 'selected' : ''; ?>>5th Pay</option>
                            <option value="6th Pay" <?php echo ($employee['salary_category'] ?? '') === '6th Pay' ? 'selected' : ''; ?>>6th Pay</option>
                            <option value="Any Other" <?php echo ($employee['salary_category'] ?? '') === 'Any Other' ? 'selected' : ''; ?>>Any Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Duty Hours <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="duty_hours"
                            value="<?php echo htmlspecialchars($employee['duty_hours'] ?? ''); ?>" required step="0.01"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Total Hours <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="total_hours"
                            value="<?php echo htmlspecialchars($employee['total_hours'] ?? ''); ?>" required step="0.01"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Hours Per Day <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="hours_per_day"
                            value="<?php echo htmlspecialchars($employee['hours_per_day'] ?? ''); ?>" required
                            step="0.01"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Salary Pay Band <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="salary_pay_band"
                            value="<?php echo htmlspecialchars($employee['salary_pay_band'] ?? ''); ?>" required
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Basic Salary <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="basic_salary"
                            value="<?php echo htmlspecialchars($employee['basic_salary'] ?? ''); ?>" required
                            step="0.01"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">PF Account Number (UAN)</label>
                        <input type="text" name="pf_number"
                            value="<?php echo htmlspecialchars($employee['pf_number'] ?? ''); ?>"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">PF Join Date</label>
                        <input type="date" name="pf_join_date"
                            value="<?php echo htmlspecialchars($employee['pf_join_date'] ?? ''); ?>"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                </div>
            </div>

            <!-- Salary Additions -->
            <div
                class="bg-white p-8 rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2 border-gray-200">Salary Additions</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">City Allowance (CA)</label>
                        <input type="number" name="ca" value="<?php echo htmlspecialchars($employee['ca'] ?? ''); ?>"
                            step="0.01"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Dearness Allowance (DA)</label>
                        <input type="number" name="da" value="<?php echo htmlspecialchars($employee['da'] ?? ''); ?>"
                            step="0.01"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">House Rent Allowance (HRA)</label>
                        <input type="number" name="hra" value="<?php echo htmlspecialchars($employee['hra'] ?? ''); ?>"
                            step="0.01"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Travelling Allowance (TA)</label>
                        <input type="number" name="ta" value="<?php echo htmlspecialchars($employee['ta'] ?? ''); ?>"
                            step="0.01"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Medical Allowance (MA)</label>
                        <input type="number" name="ma" value="<?php echo htmlspecialchars($employee['ma'] ?? ''); ?>"
                            step="0.01"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Other Allowance</label>
                        <input type="number" name="other_allowance"
                            value="<?php echo htmlspecialchars($employee['other_allowance'] ?? ''); ?>" step="0.01"
                            class="mt-1 block w-full p-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition duration-200 bg-gray-50 text-gray-700 hover:border-gray-400" />
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4 mt-8">
                <a href="index.php#"
                    class="px-6 py-3 bg-gray-600 text-white rounded-lg font-medium hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200 shadow-md hover:shadow-lg">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-200 shadow-md hover:shadow-lg">
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