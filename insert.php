<?php
// process.php
session_start();
require_once 'config.php';

$conn = connectDB();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $errors = [];

  // Validate required fields
  $required_fields = [
    'institute_name',
    'department',
    'designation',
    'location',
    'joining_date',
    'emp_category',
    'full_name',
    'gender',
    'blood_group',
    'nationality',
    'dob',
    'father_name',
    'mother_name',
    'mobile_number',
    'alt_number',
    'email',
    'address',
    'bank_name',
    'branch_name',
    'account_number',
    'ifsc_code',
    'pan_number',
    'aadhar_number',
    'salary_category',
    'duty_hours',
    'total_hours',
    'hours_per_day',
    'salary_pay_band',
    'basic_salary',
    'ca',
    'da',
    'hra',
    'ta',
    'ma',
    'other_allowance',
    'pf_number',
    'pf_join_date'
  ];

  foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
      $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required";
    }
  }

  // Validate email format
  if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
  }

  // Validate mobile number
  if (!empty($_POST['mobile_number']) && !preg_match("/^[0-9]{10}$/", $_POST['mobile_number'])) {
    $errors[] = "Invalid mobile number";
  }

  // Validate PAN number
  if (!empty($_POST['pan_number']) && !preg_match("/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/", $_POST['pan_number'])) {
    $errors[] = "Invalid PAN number";
  }

  // Validate Aadhar number
  if (!empty($_POST['aadhar_number']) && !preg_match("/^[0-9]{12}$/", $_POST['aadhar_number'])) {
    $errors[] = "Invalid Aadhar number";
  }

  
// Assuming you are fetching the employee ID dynamically from a database or form submission
// Replace this with your logic to get the employee's ID (e.g., from a session or form input)
$employee_id = $_POST['id'] ?? null; // Example: get employee_id from POST request

// If employee ID is not found, show an error
if (!$employee_id) {
    die('Employee ID is required.');
}

// Directory for uploads
$upload_dir = "uploads/";

// Create folder for the employee using the employee ID (e.g., uploads/{id}/)
$employee_dir = $upload_dir . $id . "/";
if (!file_exists($employee_dir)) {
    mkdir($employee_dir, 0777, true); // Create the directory if it doesn't exist
}

// Required files to upload (with the desired renamed file names)
$required_files = [
    'profile_photo' => "{$id}.jpg",   // Profile photo will be renamed to {employee_id}.jpg
    'aadhar_copy' => 'aadharcard.jpg',         // Aadhar card renamed to aadharcard.jpg
    'pan_copy' => 'pancard.jpg',               // PAN card renamed to pancard.jpg
    'bank_copy' => 'bankcopy.jpg'              // Bank copy renamed to bankcopy.jpg
];

$uploaded_files = [];
$errors = [];

// Iterate over required files and process the upload
foreach ($required_files as $input_name => $new_filename) {
    if (!isset($_FILES[$input_name]) || $_FILES[$input_name]['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = ucfirst(str_replace('_', ' ', $input_name)) . " is required";
    } elseif ($_FILES[$input_name]['error'] === UPLOAD_ERR_OK) {
        $file_info = pathinfo($_FILES[$input_name]['name']);
        $file_ext = strtolower($file_info['extension']);

        // Validate file type (you can modify this to include other formats if needed)
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array($file_ext, $allowed_extensions)) {
            $errors[] = "Invalid file type for " . str_replace('_', ' ', $input_name);
            continue;
        }

        // Generate the full file path inside the employee's directory
        $upload_path = $employee_dir . $new_filename;

        // Move the uploaded file to the employee's folder
        if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $upload_path)) {
            $uploaded_files[$input_name] = $new_filename;
        } else {
            $errors[] = "Error uploading " . str_replace('_', ' ', $input_name);
        }
    }
}

// Handle errors (if any)
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
} else {
    // Successful upload message
    echo "Files uploaded successfully!";
}



  if (empty($errors)) {
    $stmt = $conn->prepare("INSERT INTO employees (
      emp_code, institute_name, department, designation, location,
      joining_date, leaving_date, emp_category, full_name, gender,
      blood_group, nationality, dob, father_name, mother_name,
      spouse_name, mobile_number, alt_number, email, address,
      bank_name, branch_name, account_number, ifsc_code,
      pan_number, aadhar_number, salary_category,
      duty_hours, total_hours, hours_per_day, salary_pay_band,
      basic_salary, ca, da, hra, ta, ma, other_allowance, pf_number, pf_join_date,
      profile_photo, aadhar_copy, pan_copy, bank_copy
  ) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
"ssssssssssssssssssssssssssssssssssssssssssss",
$_POST['institute_name'],
$_POST['department'],
$_POST['designation'],
$_POST['location'],
$_POST['joining_date'],
$_POST['leaving_date'],
$_POST['emp_category'],
$_POST['full_name'],
$_POST['gender'],
$_POST['blood_group'],
$_POST['nationality'],
$_POST['dob'],
$_POST['father_name'],
$_POST['mother_name'],
$_POST['spouse_name'],
$_POST['mobile_number'],
$_POST['alt_number'],
$_POST['email'],
$_POST['address'],
$_POST['bank_name'],
$_POST['branch_name'],
$_POST['account_number'],
$_POST['ifsc_code'],
$_POST['pan_number'],
$_POST['aadhar_number'],
$_POST['salary_category'],
$_POST['duty_hours'],
$_POST['total_hours'],
$_POST['hours_per_day'],
$_POST['salary_pay_band'],
$_POST['basic_salary'],
$_POST['ca'],
$_POST['da'],
$_POST['hra'],
$_POST['ta'],
$_POST['ma'],
$_POST['other_allowance'],
$_POST['pf_number'],
$_POST['pf_join_date'],
$uploaded_files['profile_photo'],
$uploaded_files['aadhar_copy'],
$uploaded_files['pan_copy'],
$uploaded_files['bank_copy']
);


    if ($stmt->execute()) {
      echo "New record created successfully";
    } else {
      echo "Error: " . $stmt->error;
    }

    $stmt->close();
  } else {
    foreach ($errors as $error) {
      echo $error . "<br>";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Management System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
  <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl my-8">
    <?php

    if (isset($_SESSION['errors'])) {
      echo '<div class="error-message"><ul>';
      foreach ($_SESSION['errors'] as $error) {
        echo "<li>$error</li>";
      }
      echo '</ul></div>';
      unset($_SESSION['errors']);
    }

    if (isset($_SESSION['success'])) {
      echo '<div class="success-message">' . $_SESSION['success'] . '</div>';
      unset($_SESSION['success']);
    }
    ?>

    <form id="employeeForm" action="insert.php" method="POST" enctype="multipart/form-data" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header Section -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
          <i class="fas fa-user-plus mr-3 text-blue-600"></i>
          Add New Employee
        </h1>
        <p class="mt-2 text-sm text-gray-600">
          Fill in the information below to add a new employee to the system. All fields marked with * are required.
        </p>
      </div>
      
      <!-- Progress Bar -->
      <div class="mb-8 bg-gray-100 rounded-full h-2">
        <div class="bg-blue-600 h-2 rounded-full" style="width: 0%" id="formProgress"></div>
      </div>

        <!-- Employer Details Section -->
        <div class="bg-white p-8 rounded-xl shadow-lg mb-8">
          <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-building mr-3 text-blue-600"></i>
            Employer Details
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700" for="emp_code">Employee Code</label>
              <input type="text" name="emp_code" id="emp_code" 
                placeholder="Auto-generated"
                readonly
                class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 cursor-not-allowed" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Institute Name</label>
              <input type="text" name="institute_name" required
                placeholder="Enter institute name"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Department</label>
              <input type="text" name="department" required
                placeholder="Enter department"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Designation</label>
              <input type="text" name="designation" required
                placeholder="Enter designation"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Location</label>
              <select name="location" required
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 bg-white">
                <option value="">Select Campus</option>
                <option value="dn">DN Campus</option>
                <option value="mogri">Mogri Campus</option>
                <option value="khetiwadi">Khetiwadi Campus</option>
                <option value="mbpatel">MB Patel Science College</option>
              </select>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Date of Joining</label>
              <input type="date" name="joining_date" required
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Date of Leaving</label>
              <input type="date" name="leaving_date"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Employee Category</label>
              <select name="emp_category" required
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 bg-white">
                <option value="">Select Category</option>
                <option value="adhoc">Adhoc</option>
                <option value="permanent">Permanent</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Employee Personal Details Section -->
        <div class="bg-white p-8 rounded-xl shadow-lg mb-8">
          <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-user mr-3 text-blue-600"></i>
            Personal Information
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Full Name</label>
              <input type="text" name="full_name" required
                placeholder="Enter full name"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Gender</label>
              <select name="gender" required
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 bg-white">
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Blood Group</label>
              <select name="blood_group" required
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 bg-white">
                <option value="">Select Blood Group</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
              </select>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Nationality</label>
              <select name="nationality" required
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 bg-white">
                <option value="">Select Nationality</option>
                <option value="indian">Indian</option>
                <option value="other">Other</option>
              </select>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Date of Birth</label>
              <input type="date" name="dob" required
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Father's Name</label>
              <input type="text" name="father_name" required
                placeholder="Enter father's name"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Mother's Name</label>
              <input type="text" name="mother_name" required
                placeholder="Enter mother's name"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Spouse Name</label>
              <input type="text" name="spouse_name"
                placeholder="Enter spouse name (if applicable)"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Mobile Number</label>
              <input type="tel" name="mobile_number" required pattern="[0-9]{10}"
                placeholder="Enter 10-digit mobile number"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Alternative Number</label>
              <input type="tel" name="alt_number" pattern="[0-9]{10}"
                placeholder="Enter alternative number (optional)"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Email ID</label>
              <input type="email" name="email" required
                placeholder="Enter email address"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
            </div>

            <div class="md:col-span-3 space-y-2">
              <label class="text-sm font-semibold text-gray-700">Complete Residential Address</label>
              <textarea name="address" required rows="3"
                placeholder="Enter your complete residential address"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"></textarea>
            </div>
          </div>
        </div>

        <!-- Bank Details Section -->
        <div class="bg-white p-8 rounded-xl shadow-lg mb-8">
          <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-university mr-3 text-blue-600"></i>
            Bank Details
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Bank Name</label>
              <select name="bank_name" required
          class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 bg-white">
          <option value="">Select Bank</option>
          <option value="sbi">State Bank of India</option>
          <option value="pnb">Punjab National Bank</option>
          <option value="bob">Bank of Baroda</option>
          <option value="hdfc">HDFC Bank</option>
          <option value="icici">ICICI Bank</option>
          <option value="axis">Axis Bank</option>
              </select>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Branch Name</label>
              <input type="text" name="branch_name" required
          placeholder="Enter branch name"
          class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Account Number</label>
              <div class="relative">
          <input type="text" name="account_number" required
            placeholder="Enter account number"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
          <div class="absolute inset-y-0 right-0 flex items-center px-3">
            <i class="fas fa-lock text-gray-400"></i>
          </div>
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">IFSC Code</label>
              <div class="relative">
          <input type="text" name="ifsc_code" required
            placeholder="Enter IFSC code"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 uppercase" />
          <div class="absolute inset-y-0 right-0 flex items-center px-3">
            <i class="fas fa-building-columns text-gray-400"></i>
          </div>
              </div>
              <p class="text-xs text-gray-500 mt-1">Enter the 11-digit IFSC code of your bank branch</p>
            </div>
          </div>
        </div>

        <!-- Statutory Details Section -->
        <div class="bg-white p-8 rounded-xl shadow-lg mb-8">
          <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-file-contract mr-3 text-blue-600"></i>
            Statutory Details
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">PAN Number</label>
              <div class="relative">
          <input type="text" name="pan_number" required 
            pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}"
            placeholder="Enter PAN number"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 uppercase" />
          <div class="absolute inset-y-0 right-0 flex items-center px-3">
            <i class="fas fa-id-card text-gray-400"></i>
          </div>
              </div>
              <p class="text-xs text-gray-500">Format: ABCDE1234F</p>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Aadhar Number</label>
              <div class="relative">
          <input type="text" name="aadhar_number" required 
            pattern="[0-9]{12}"
            placeholder="Enter Aadhar number" 
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
            maxlength="12" />
          <div class="absolute inset-y-0 right-0 flex items-center px-3">
            <i class="fas fa-fingerprint text-gray-400"></i>
          </div>
              </div>
              <p class="text-xs text-gray-500">Enter your 12-digit Aadhar number</p>
            </div>
          </div>
        </div>

        <!-- Salary Details Section -->
        <div class="bg-white p-8 rounded-xl shadow-lg mb-8">
          <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-money-bill-wave mr-3 text-blue-600"></i>
            Salary Details
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Salary Category</label>
              <select name="salary_category" required
          class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 bg-white">
          <option value="">Select Category</option>
          <option value="ADHOC With PF">Adhoc with PF</option>
          <option value="ADHOC Without PF">Adhoc without PF</option>
          <option value="5th Pay">5th Pay</option>
          <option value="6th Pay">6th Pay</option>
          <option value="Any Other">Any Other</option>
              </select>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Duty Hours</label>
              <div class="relative">
          <input type="number" name="duty_hours" required
            placeholder="Enter duty hours"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
          <div class="absolute inset-y-0 right-0 flex items-center px-3">
            <i class="fas fa-clock text-gray-400"></i>
          </div>
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Total Hours</label>
              <div class="relative">
          <input type="number" name="total_hours" required
            placeholder="Enter total hours"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
          <div class="absolute inset-y-0 right-0 flex items-center px-3">
            <i class="fas fa-hourglass text-gray-400"></i>
          </div>
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Hours Per Day</label>
              <div class="relative">
          <input type="number" name="hours_per_day" required
            placeholder="Enter hours per day"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
          <div class="absolute inset-y-0 right-0 flex items-center px-3">
            <i class="fas fa-business-time text-gray-400"></i>
          </div>
              </div>
              <p class="text-xs text-gray-500">For hourly basis employees</p>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Salary Pay Band</label>
              <div class="relative">
          <input type="text" name="salary_pay_band" required
            placeholder="Enter pay band"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
          <div class="absolute inset-y-0 right-0 flex items-center px-3">
            <i class="fas fa-layer-group text-gray-400"></i>
          </div>
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Basic Salary</label>
              <div class="relative">
          <input type="number" name="basic_salary" required
            placeholder="Enter basic salary"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
          <div class="absolute inset-y-0 right-0 flex items-center px-3">
            <i class="fas fa-indian-rupee-sign text-gray-400"></i>
          </div>
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">PF Account Number (UAN)</label>
              <div class="relative">
          <input type="text" name="pf_number"
            placeholder="Enter UAN number"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
          <div class="absolute inset-y-0 right-0 flex items-center px-3">
            <i class="fas fa-id-badge text-gray-400"></i>
          </div>
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">PF Join Date</label>
              <div class="relative">
          <input type="date" name="pf_join_date"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
          <div class="absolute inset-y-0 right-0 flex items-center px-3">
            <i class="fas fa-calendar text-gray-400"></i>
          </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Salary Additions Section -->
        <div class="bg-white p-8 rounded-xl shadow-lg mb-8">
          <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-money-bill-trend-up mr-3 text-blue-600"></i>
            Salary Additions
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">City Allowance (CA)</label>
              <div class="relative">
          <input type="number" name="ca"
            placeholder="Enter CA amount"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
          <div class="absolute inset-y-0 right-0 flex items-center px-3">
            <i class="fas fa-indian-rupee-sign text-gray-400"></i>
          </div>
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Dearness Allowance (DA)</label>
              <div class="relative">
          <input type="number" name="da"
            placeholder="Enter DA amount"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
          <div class="absolute inset-y-0 right-0 flex items-center px-3">
            <i class="fas fa-indian-rupee-sign text-gray-400"></i>
          </div>
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">House Rent Allowance (HRA)</label>
              <div class="relative">
          <input type="number" name="hra"
            placeholder="Enter HRA amount"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
          <div class="absolute inset-y-0 right-0 flex items-center px-3">
            <i class="fas fa-indian-rupee-sign text-gray-400"></i>
          </div>
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Travelling Allowance (TA)</label>
              <div class="relative">
          <input type="number" name="ta"
            placeholder="Enter TA amount"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
          <div class="absolute inset-y-0 right-0 flex items-center px-3">
            <i class="fas fa-indian-rupee-sign text-gray-400"></i>
          </div>
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Medical Allowance (MA)</label>
              <div class="relative">
          <input type="number" name="ma"
            placeholder="Enter MA amount"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
          <div class="absolute inset-y-0 right-0 flex items-center px-3">
            <i class="fas fa-indian-rupee-sign text-gray-400"></i>
          </div>
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-700">Other Allowance</label>
              <div class="relative">
          <input type="number" name="other_allowance"
            placeholder="Enter other allowances"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
          <div class="absolute inset-y-0 right-0 flex items-center px-3">
            <i class="fas fa-indian-rupee-sign text-gray-400"></i>
          </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Required Documents Section -->
        <div class="bg-white p-8 rounded-xl shadow-lg mb-8">
          <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-file-upload mr-3 text-blue-600"></i>
            Required Documents
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Profile Photo Upload -->
            <div class="space-y-4">
              <div class="p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 transition-colors">
                <label class="flex flex-col items-center cursor-pointer">
                  <i class="fas fa-user-circle text-4xl text-gray-400 mb-2"></i>
                  <span class="text-sm font-semibold text-gray-700 mb-2">Profile Photo</span>
                  <input type="file" name="profile_photo" accept="image/*" required class="hidden" />
                  <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-sm font-medium">
                    Choose Photo
                  </span>
                  <span class="text-xs text-gray-500 mt-2 text-center">Upload a clear passport size photograph</span>
                </label>
              </div>
            </div>

            <!-- Aadhar Card Upload -->
            <div class="space-y-4">
              <div class="p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 transition-colors">
                <label class="flex flex-col items-center cursor-pointer">
                  <i class="fas fa-id-card text-4xl text-gray-400 mb-2"></i>
                  <span class="text-sm font-semibold text-gray-700 mb-2">Aadhar Card</span>
                  <input type="file" name="aadhar_copy" accept=".pdf,.jpg,.jpeg,.png" required class="hidden" />
                  <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-sm font-medium">
                    Upload Aadhar
                  </span>
                  <span class="text-xs text-gray-500 mt-2 text-center">Upload both sides in a single file</span>
                </label>
              </div>
            </div>

            <!-- PAN Card Upload -->
            <div class="space-y-4">
              <div class="p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 transition-colors">
                <label class="flex flex-col items-center cursor-pointer">
                  <i class="fas fa-address-card text-4xl text-gray-400 mb-2"></i>
                  <span class="text-sm font-semibold text-gray-700 mb-2">PAN Card</span>
                  <input type="file" name="pan_copy" accept=".pdf,.jpg,.jpeg,.png" required class="hidden" />
                  <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-sm font-medium">
                    Upload PAN
                  </span>
                  <span class="text-xs text-gray-500 mt-2 text-center">Upload a clear copy of your PAN card</span>
                </label>
              </div>
            </div>

            <!-- Bank Details Upload -->
            <div class="space-y-4">
              <div class="p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 transition-colors">
                <label class="flex flex-col items-center cursor-pointer">
                  <i class="fas fa-university text-4xl text-gray-400 mb-2"></i>
                  <span class="text-sm font-semibold text-gray-700 mb-2">Bank Details</span>
                  <input type="file" name="bank_copy" accept=".pdf,.jpg,.jpeg,.png" required class="hidden" />
                  <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-sm font-medium">
                    Upload Document
                  </span>
                  <span class="text-xs text-gray-500 mt-2 text-center">Upload passbook or cancelled cheque</span>
                </label>
              </div>
            </div>
          </div>

          <!-- File Upload Guidelines -->
          <div class="mt-6 bg-gray-50 p-4 rounded-lg">
            <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
              <i class="fas fa-info-circle mr-2 text-blue-600"></i>
              Upload Guidelines
            </h4>
            <ul class="text-xs text-gray-600 space-y-1 ml-6 list-disc">
              <li>Maximum file size: 2MB per document</li>
              <li>Supported formats: PDF, JPG, JPEG, PNG</li>
              <li>Documents should be clear and readable</li>
              <li>All fields marked with * are mandatory</li>
            </ul>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end mt-8 mb-12">
          <button type="submit" 
            class="group relative inline-flex items-center justify-center px-8 py-3 text-lg font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-full overflow-hidden shadow-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <span class="absolute inset-0 w-full h-full bg-white/10 group-hover:scale-x-100 group-hover:opacity-0 transition-transform duration-300"></span>
            <i class="fas fa-save mr-2 group-hover:scale-110 transition-transform duration-300"></i>
            <span class="relative">Save Employee Details</span>
            <i class="fas fa-arrow-right ml-2 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300"></i>
          </button>
        </div>

    </form>
  </div>
</body>

</html>