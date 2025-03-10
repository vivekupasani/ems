<?php
session_start();

// Secure admin authentication check
if (!isset($_SESSION['admin_id']) || $_SESSION['is_admin'] !== true) {
  $_SESSION['error'] = "Unauthorized access. Please log in.";
  header("Location: admin_login.php");
  exit();
}

// Assuming config.php contains connectDB() function; adjust if it's in database.php
require_once 'config.php'; // Or 'config/database.php' if using PDO

$conn = connectDB(); // Assuming this returns a MySQLi connection; adjust if using PDO

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

  // Employee ID will be set after insertion
  $employee_id = null;

  // Directory for uploads
  $upload_dir = "uploads/";

  // Required files to upload
  $required_files = [
    'profile_photo' => "profile.jpg",  // Will be renamed after getting employee_id
    'aadhar_copy' => 'aadharcard.jpg',
    'pan_copy' => 'pancard.jpg',
    'bank_copy' => 'bankcopy.jpg'
  ];

  $uploaded_files = [];

  // Process file uploads
  foreach ($required_files as $input_name => $new_filename) {
    if (!isset($_FILES[$input_name]) || $_FILES[$input_name]['error'] === UPLOAD_ERR_NO_FILE) {
      $errors[] = ucfirst(str_replace('_', ' ', $input_name)) . " is required";
    } elseif ($_FILES[$input_name]['error'] === UPLOAD_ERR_OK) {
      $file_info = pathinfo($_FILES[$input_name]['name']);
      $file_ext = strtolower($file_info['extension']);
      $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];

      if (!in_array($file_ext, $allowed_extensions)) {
        $errors[] = "Invalid file type for " . str_replace('_', ' ', $input_name);
        continue;
      }

      $temp_path = $upload_dir . "temp_" . uniqid() . "." . $file_ext;
      if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $temp_path)) {
        $uploaded_files[$input_name] = $temp_path;
      } else {
        $errors[] = "Error uploading " . str_replace('_', ' ', $input_name);
      }
    }
  }

  if (empty($errors)) {
    $stmt = $conn->prepare("INSERT INTO employees (
            institute_name, department, designation, location, joining_date, 
            leaving_date, emp_category, full_name, gender, blood_group, 
            nationality, dob, father_name, mother_name, spouse_name, 
            mobile_number, alt_number, email, address, bank_name, 
            branch_name, account_number, ifsc_code, pan_number, aadhar_number, 
            salary_category, duty_hours, total_hours, hours_per_day, 
            salary_pay_band, basic_salary, ca, da, hra, ta, ma, 
            other_allowance, pf_number, pf_join_date, profile_photo, 
            aadhar_copy, pan_copy, bank_copy
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
      "sssssssssssssssssssssssssssssssssssssssss",
      $_POST['institute_name'],
      $_POST['department'],
      $_POST['designation'],
      $_POST['location'],
      $_POST['joining_date'],
      $_POST['leaving_date'] ?? null,
      $_POST['emp_category'],
      $_POST['full_name'],
      $_POST['gender'],
      $_POST['blood_group'],
      $_POST['nationality'],
      $_POST['dob'],
      $_POST['father_name'],
      $_POST['mother_name'],
      $_POST['spouse_name'] ?? null,
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
      $employee_id = $conn->insert_id;
      $employee_dir = $upload_dir . $employee_id . "/";
      if (!file_exists($employee_dir)) {
        mkdir($employee_dir, 0777, true);
      }

      foreach ($required_files as $input_name => $new_filename) {
        $new_path = $employee_dir . ($input_name === 'profile_photo' ? "$employee_id.jpg" : $new_filename);
        rename($uploaded_files[$input_name], $new_path);
        $uploaded_files[$input_name] = $new_path;

        $update_stmt = $conn->prepare("UPDATE employees SET $input_name = ? WHERE emp_code = ?");
        $update_stmt->bind_param("si", $new_path, $employee_id);
        $update_stmt->execute();
        $update_stmt->close();
      }

      $_SESSION['success'] = "New employee record created successfully!";
    } else {
      $_SESSION['error'] = "Error: " . $stmt->error;
    }

    $stmt->close();
  } else {
    $_SESSION['error'] = implode("<br>", $errors);
    foreach ($uploaded_files as $file) {
      if (file_exists($file))
        unlink($file);
    }
  }

  header("Location: admin_dashboard.php"); // Refresh the page
  exit();
}

// Fetch all employee records
$sql = "SELECT * FROM employees";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Employee Appointment Management System</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="static/script.js"></script>
  <!-- <link rel="stylesheet" href="static/style.css"> -->
</head>

<body class="min-h-screen flex flex-col bg-gray-50">
  <!-- Responsive Navigation Bar -->
  <nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4">
      <div class="flex justify-between items-center h-auto py-4 md:h-20">
        <!-- Menu Button for Mobile and Desktop -->
        <button onclick="toggleDrawer(true)"
          class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-blue-50 transition-colors duration-200 focus:outline-none hover:text-blue-600">
          <i class="fas fa-bars text-2xl text-gray-600"></i>
        </button>

        <div class="flex items-center flex-wrap md:flex-nowrap">
          <div class="flex-shrink-0">
            <img src="cesLogo.png" alt="Logo" class="h-8 w-8 md:h-10 md:w-10" />
          </div>
          <div class="ml-4">
            <h1 class="text-lg md:text-xl font-bold text-gray-800 break-words">
              <span class="hidden md:inline">Employee Appointment Management System</span>
              <span class="inline md:hidden">EMS System</span>
            </h1>
          </div>
        </div>

        <div class="flex items-center">
          <button
            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 md:px-4 md:py-2 rounded-md flex items-center text-sm md:text-base"
            onClick="window.location='default.php'">
            <i class="fas fa-sign-out-alt mr-1 md:mr-2"></i>
            <span class="hidden sm:inline">Logout</span>
          </button>
        </div>
      </div>
    </div>
  </nav>

  <!-- Overlay (Background Dim) -->
  <div id="overlay" class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 hidden z-40"
    onclick="toggleDrawer(false)">
  </div>

  <!-- Navigation Drawer -->
  <div id="navigationDrawer"
    class="fixed top-0 left-0 h-full w-64 md:w-80 bg-white shadow-2xl rounded-r-lg transform -translate-x-full transition-transform duration-300 ease-in-out z-50">
    <div class="p-4 flex justify-between items-center border-b">
      <h2 class="text-lg md:text-xl font-semibold">Navigation</h2>
      <button onclick="toggleDrawer(false)"
        class="text-gray-500 focus:outline-none p-2 rounded-full hover:text-red-400 group-hover:scale-110 transition-transform duration-400">
        <i class="fas fa-times text-2xl"></i>
      </button>
    </div>
    <div class="flex flex-col space-y-3 md:space-y-4 p-3 md:p-4">
      <a href="#" onclick="showSection('dashboard'); toggleDrawer(false);"
        class="flex items-center px-2 py-2 text-sm md:text-base text-gray-900 hover:text-blue-600 rounded-md transition-colors px-4 shadow-sm hover:shadow-md transition-all duration-200 text-gray-700 hover:bg-blue-50 group">
        <i
          class="fas fa-chart-line hover:text-blue-500 group-hover:scale-110 transition-transform duration-200 mr-2"></i>
        Dashboard
      </a>
      <a href="#" onclick="showSection('manage-employees'); toggleDrawer(false);"
        class="flex items-center px-2 py-2 text-sm md:text-base text-gray-900 hover:text-blue-600 rounded-md transition-colors px-4 shadow-sm hover:shadow-md transition-all duration-200 text-gray-700 hover:bg-blue-50 group">
        <i
          class="fas fa-users-cog hover:text-blue-500 group-hover:scale-110 transition-transform duration-200 mr-2"></i>
        Manage Employees
      </a>
      <a href="#" onclick="showSection('view-employees');toggleDrawer(false);"
        class="flex items-center px-2 py-2 text-sm md:text-base text-gray-900 hover:text-blue-600 rounded-md transition-colors px-4 shadow-sm hover:shadow-md transition-all duration-200 text-gray-700 hover:bg-blue-50 group">
        <i class="fas fa-users hover:text-blue-500 group-hover:scale-110 transition-transform duration-200 mr-2"></i>
        View Employees
      </a>
      <a href="#" onclick="showSection('create-user');toggleDrawer(false);"
        class="flex items-center px-2 py-2 text-sm md:text-base text-gray-900 hover:text-blue-600 rounded-md transition-colors px-4 shadow-sm hover:shadow-md transition-all duration-200 text-gray-700 hover:bg-blue-50 group">
        <i class="fas fa-users hover:text-blue-500 group-hover:scale-110 transition-transform duration-200 mr-2"></i>
        Create New User
      </a>
    </div>
  </div>

  <!-- Main Content -->
  <main class="flex-grow max-w-7xl mx-auto px-4 py-4 sm:py-8">
    <!-- Dashboard Section -->
    <section id="dashboard" class="section-content">
      <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-3 sm:mb-4">Dashboard</h2>
        <p class="text-sm sm:text-base text-gray-600">
          Welcome to AppointEase! Manage your employee appointments
          efficiently with our comprehensive management system.
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-6">
        <!-- Stats Card -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
          <div class="flex flex-col sm:flex-row items-start sm:items-center">
            <div class="p-2 sm:p-3 rounded-full bg-blue-100 text-blue-500 mb-3 sm:mb-0">
              <i class="fas fa-calendar-check text-lg sm:text-xl"></i>
            </div>
            <div class="sm:ml-4">
              <p class="text-xs sm:text-sm text-gray-500">Total Employees</p>
              <p class="text-base sm:text-lg font-semibold text-gray-800">0</p>
            </div>
            <div class="mt-3 sm:mt-0 sm:ml-4">
              <p class="text-xs sm:text-sm text-gray-500">Active/Inactive</p>
              <p class="text-base sm:text-lg font-semibold text-gray-800">0</p>
            </div>
          </div>
        </div>

        <!-- Total Employees Card -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Total Employees</p>
              <h3 class="text-2xl font-bold text-gray-800" id="totalEmployees">0</h3>
            </div>
            <div class="p-3 bg-blue-100 rounded-full">
              <i class="fas fa-users text-xl text-blue-600"></i>
            </div>
          </div>
        </div>

        <!-- Active Employees Card -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Active Employees</p>
              <h3 class="text-2xl font-bold text-green-600" id="activeEmployees">0</h3>
            </div>
            <div class="p-3 bg-green-100 rounded-full">
              <i class="fas fa-user-check text-xl text-green-600"></i>
            </div>
          </div>
        </div>

        <!-- Departments Card -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Departments</p>
              <h3 class="text-2xl font-bold text-purple-600" id="totalDepartments">0</h3>
            </div>
            <div class="p-3 bg-purple-100 rounded-full">
              <i class="fas fa-building text-xl text-purple-600"></i>
            </div>
          </div>
        </div>

        <!-- Total Salary Card -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Total Salary</p>
              <h3 class="text-2xl font-bold text-orange-600" id="totalSalary">₹0</h3>
            </div>
            <div class="p-3 bg-orange-100 rounded-full">
              <i class="fas fa-indian-rupee-sign text-xl text-orange-600"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Department Distribution Chart -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Department Distribution</h3>
          <canvas id="departmentChart" class="w-full h-64"></canvas>
        </div>

        <!-- Salary Distribution Chart -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Salary Distribution</h3>
          <canvas id="salaryChart" class="w-full h-64"></canvas>
        </div>
      </div>
      <!-- Quick Stats Cards Row -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
          // Function to fetch dashboard data
          async function fetchDashboardData() {
            try {
              const response = await fetch('get_dashboard_data.php');
              const data = await response.json();

              // Update stats
              document.getElementById('totalEmployees').textContent = data.totalEmployees;
              document.getElementById('activeEmployees').textContent = data.activeEmployees;
              document.getElementById('totalDepartments').textContent = data.departments.length;
              document.getElementById('totalSalary').textContent = '₹' + data.totalSalary.toLocaleString();

              // Department Chart
              new Chart(document.getElementById('departmentChart'), {
                type: 'doughnut',
                data: {
                  labels: data.departments.map(d => d.name),
                  datasets: [{
                    data: data.departments.map(d => d.count),
                    backgroundColor: [
                      '#4B56D2', '#82C3EC', '#F6BA6F', '#FFB4B4',
                      '#95BDFF', '#B4E4FF', '#DFFFD8', '#F7C8E0'
                    ]
                  }]
                },
                options: {
                  responsive: true,
                  plugins: {
                    legend: {
                      position: 'right'
                    }
                  }
                }
              });

              // Salary Chart
              new Chart(document.getElementById('salaryChart'), {
                type: 'bar',
                data: {
                  labels: ['0-25K', '25K-50K', '50K-75K', '75K-100K', '100K+'],
                  datasets: [{
                    label: 'Employees',
                    data: data.salaryRanges,
                    backgroundColor: '#4B56D2'
                  }]
                },
                options: {
                  responsive: true,
                  scales: {
                    y: {
                      beginAtZero: true,
                      ticks: {
                        stepSize: 1
                      }
                    }
                  }
                }
              });

            } catch (error) {
              console.error('Error fetching dashboard data:', error);
            }
          }

          // Initial load
          fetchDashboardData();

          // Refresh every 5 minutes
          setInterval(fetchDashboardData, 300000);
        </script>
      </div>
    </section>

    <!-- Manage Employees Section -->
    <section id="manage-employees" class="section-content hidden max-w-7xl mx-auto">
      <!-- Sub Navigation -->
      <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-3 sm:mb-4">
          Manage Employees
        </h2>

        <!-- Sub-sections -->
        <div class="border-b border-gray-200">
          <nav class="flex flex-wrap gap-3 sm:gap-6 py-2">
            <button onclick="showManageSection('appointment-form')"
              class="flex items-center px-4 py-2 rounded-lg bg-white shadow-sm hover:shadow-md transition-all duration-200 text-gray-700 hover:text-blue-600 hover:bg-blue-50 group">
              <i
                class="fas fa-calendar-plus text-blue-500 group-hover:scale-110 transition-transform duration-200 mr-2"></i>
              <span class="hidden sm:inline font-medium">Appointment Form</span>
              <span class="inline sm:hidden font-medium">Add</span>
            </button>

            <button onclick="showManageSection('upload-excel')"
              class="flex items-center px-4 py-2 rounded-lg bg-white shadow-sm hover:shadow-md transition-all duration-200 text-gray-700 hover:text-green-600 hover:bg-green-50 group">
              <i
                class="fas fa-file-excel text-green-500 group-hover:scale-110 transition-transform duration-200 mr-2"></i>
              <span class="hidden sm:inline font-medium">Upload Excel</span>
              <span class="inline sm:hidden font-medium">Upload</span>
            </button>

            <button onclick="showManageSection('help')"
              class="flex items-center px-4 py-2 rounded-lg bg-white shadow-sm hover:shadow-md transition-all duration-200 text-gray-700 hover:text-orange-600 hover:bg-orange-50 group">
              <i
                class="fas fa-question-circle text-orange-500 group-hover:scale-110 transition-transform duration-200 mr-2"></i>
              <span class="font-medium">Help</span>
            </button>
          </nav>
        </div>

        <!-- Appointment Form Sub-section -->
        <div class="manage-section mt-6 hidden" id="appointment-form">
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

          <form id="employeeForm" action="insert.php" method="POST" enctype="multipart/form-data"
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header Section -->
            <div class="mb-6 md:mb-8">
              <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center flex-wrap">
                <i class="fas fa-user-plus mr-2 md:mr-3 text-blue-600"></i>
                <span class="break-words">Add New Employee</span>
              </h1>
              <p class="mt-2 text-xs md:text-sm text-gray-600 max-w-3xl">
                Fill in the information below to add a new employee to the system. All fields marked with * are
                required.
              </p>
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
                  <input type="text" name="emp_code" id="emp_code" placeholder="Auto-generated" readonly
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 cursor-not-allowed" />
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">Institute Name</label>
                  <input type="text" name="institute_name" required placeholder="Enter institute name"
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">Department</label>
                  <input type="text" name="department" required placeholder="Enter department"
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">Designation</label>
                  <input type="text" name="designation" required placeholder="Enter designation"
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
                  <input type="text" name="full_name" required placeholder="Enter full name"
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
                  <input type="text" name="father_name" required placeholder="Enter father's name"
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">Mother's Name</label>
                  <input type="text" name="mother_name" required placeholder="Enter mother's name"
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">Spouse Name</label>
                  <input type="text" name="spouse_name" placeholder="Enter spouse name (if applicable)"
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
                  <input type="email" name="email" required placeholder="Enter email address"
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
                </div>

                <div class="md:col-span-3 space-y-2">
                  <label class="text-sm font-semibold text-gray-700">Complete Residential Address</label>
                  <textarea name="address" required rows="3" placeholder="Enter your complete residential address"
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
                  <input type="text" name="branch_name" required placeholder="Enter branch name"
                    class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">Account Number</label>
                  <div class="relative">
                    <input type="text" name="account_number" required placeholder="Enter account number"
                      class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
                    <div class="absolute inset-y-0 right-0 flex items-center px-3">
                      <i class="fas fa-lock text-gray-400"></i>
                    </div>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">IFSC Code</label>
                  <div class="relative">
                    <input type="text" name="ifsc_code" required placeholder="Enter IFSC code"
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
                    <input type="text" name="pan_number" required pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}"
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
                    <input type="text" name="aadhar_number" required pattern="[0-9]{12}"
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
                    <input type="number" name="duty_hours" required placeholder="Enter duty hours"
                      class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
                    <div class="absolute inset-y-0 right-0 flex items-center px-3">
                      <i class="fas fa-clock text-gray-400"></i>
                    </div>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">Total Hours</label>
                  <div class="relative">
                    <input type="number" name="total_hours" required placeholder="Enter total hours"
                      class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
                    <div class="absolute inset-y-0 right-0 flex items-center px-3">
                      <i class="fas fa-hourglass text-gray-400"></i>
                    </div>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">Hours Per Day</label>
                  <div class="relative">
                    <input type="number" name="hours_per_day" required placeholder="Enter hours per day"
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
                    <input type="text" name="salary_pay_band" required placeholder="Enter pay band"
                      class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
                    <div class="absolute inset-y-0 right-0 flex items-center px-3">
                      <i class="fas fa-layer-group text-gray-400"></i>
                    </div>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">Basic Salary</label>
                  <div class="relative">
                    <input type="number" name="basic_salary" required placeholder="Enter basic salary"
                      class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
                    <div class="absolute inset-y-0 right-0 flex items-center px-3">
                      <i class="fas fa-indian-rupee-sign text-gray-400"></i>
                    </div>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">PF Account Number (UAN)</label>
                  <div class="relative">
                    <input type="text" name="pf_number" placeholder="Enter UAN number"
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
                    <input type="number" name="ca" placeholder="Enter CA amount"
                      class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
                    <div class="absolute inset-y-0 right-0 flex items-center px-3">
                      <i class="fas fa-indian-rupee-sign text-gray-400"></i>
                    </div>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">Dearness Allowance (DA)</label>
                  <div class="relative">
                    <input type="number" name="da" placeholder="Enter DA amount"
                      class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
                    <div class="absolute inset-y-0 right-0 flex items-center px-3">
                      <i class="fas fa-indian-rupee-sign text-gray-400"></i>
                    </div>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">House Rent Allowance (HRA)</label>
                  <div class="relative">
                    <input type="number" name="hra" placeholder="Enter HRA amount"
                      class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
                    <div class="absolute inset-y-0 right-0 flex items-center px-3">
                      <i class="fas fa-indian-rupee-sign text-gray-400"></i>
                    </div>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">Travelling Allowance (TA)</label>
                  <div class="relative">
                    <input type="number" name="ta" placeholder="Enter TA amount"
                      class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
                    <div class="absolute inset-y-0 right-0 flex items-center px-3">
                      <i class="fas fa-indian-rupee-sign text-gray-400"></i>
                    </div>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">Medical Allowance (MA)</label>
                  <div class="relative">
                    <input type="number" name="ma" placeholder="Enter MA amount"
                      class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" />
                    <div class="absolute inset-y-0 right-0 flex items-center px-3">
                      <i class="fas fa-indian-rupee-sign text-gray-400"></i>
                    </div>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">Other Allowance</label>
                  <div class="relative">
                    <input type="number" name="other_allowance" placeholder="Enter other allowances"
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
                  <div
                    class="p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 transition-colors">
                    <label class="flex flex-col items-center cursor-pointer">
                      <i class="fas fa-user-circle text-4xl text-gray-400 mb-2"></i>
                      <span class="text-sm font-semibold text-gray-700 mb-2">Profile Photo</span>
                      <input type="file" name="profile_photo" accept="image/*" required class="hidden" />
                      <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-sm font-medium">
                        Choose Photo
                      </span>
                      <span class="text-xs text-gray-500 mt-2 text-center">Upload a clear passport size
                        photograph</span>
                    </label>
                  </div>
                </div>

                <!-- Aadhar Card Upload -->
                <div class="space-y-4">
                  <div
                    class="p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 transition-colors">
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
                  <div
                    class="p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 transition-colors">
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
                  <div
                    class="p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 transition-colors">
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
                <span
                  class="absolute inset-0 w-full h-full bg-white/10 group-hover:scale-x-100 group-hover:opacity-0 transition-transform duration-300"></span>
                <i class="fas fa-save mr-2 group-hover:scale-110 transition-transform duration-300"></i>
                <span class="relative">Save Employee Details</span>
                <i
                  class="fas fa-arrow-right ml-2 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300"></i>
              </button>
            </div>

          </form>
        </div>

        <!-- Upload Excel Sub-section -->
        <div id="upload-excel" class="manage-section mt-6 hidden">
          <!-- Upload Container -->
          <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
              <!-- Upload Area -->
              <div
                class="border-2 border-dashed border-gray-200 rounded-xl p-6 md:p-8 transition-all duration-300 hover:border-blue-400 hover:bg-blue-50/50">
                <div class="flex flex-col items-center space-y-4">
                  <div class="rounded-full bg-blue-100 p-3">
                    <i class="fas fa-file-excel text-3xl md:text-4xl text-blue-500"></i>
                  </div>

                  <div class="text-center space-y-2">
                    <h3 class="text-xl md:text-2xl font-semibold text-gray-800">
                      Upload Excel File
                    </h3>
                    <p class="text-sm md:text-base text-gray-600">
                      Drag and drop your Excel file here or click to browse
                    </p>
                  </div>

                  <input type="file" class="hidden" id="excel-upload" accept=".xlsx, .xls" />
                  <button onclick="document.getElementById('excel-upload').click()"
                    class="inline-flex items-center px-4 py-2.5 text-sm md:text-base font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-all duration-200">
                    <i class="fas fa-upload mr-2"></i>
                    Choose File
                  </button>

                  <p class="text-xs md:text-sm text-gray-500">
                    Supported formats: .xlsx, .xls
                  </p>
                </div>
              </div>

              <!-- Preview Section -->
              <div id="preview-section" class="mt-8 hidden">
                <div class="mb-6">
                  <h4 class="text-lg md:text-xl font-semibold text-gray-800 mb-2">Data Preview</h4>
                  <p class="text-sm text-gray-600">Review your data before uploading</p>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                  <table id="preview-table" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50"></thead>
                    <tbody class="divide-y divide-gray-200"></tbody>
                  </table>
                </div>

                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                  <button id="upload-btn"
                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm md:text-base font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300 transition-all duration-200">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>
                    Upload Data
                  </button>
                  <button id="cancel-btn"
                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm md:text-base font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:ring-4 focus:ring-gray-300 transition-all duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Help Sub-section -->
        <div id="help" class="manage-section mt-6 hidden">
          <div class="space-y-4 max-w-7xl mx-auto">
            <div class="bg-gray-50 p-4 rounded-lg">
              <h3 class="text-lg font-medium text-gray-900 mb-2">FAQ</h3>
              <div class="space-y-3">
                <div>
                  <h4 class="font-medium text-gray-700">
                    How do I schedule an appointment?
                  </h4>
                  <p class="text-sm text-gray-600">
                    Use the Appointment Form section to schedule individual
                    appointments or use the Excel upload feature for bulk
                    scheduling.
                  </p>
                </div>
                <div>
                  <h4 class="font-medium text-gray-700">
                    What format should my Excel file be in?
                  </h4>
                  <p class="text-sm text-gray-600">
                    Your Excel file should include columns for Employee Name,
                    Department, Date, Time, and Purpose. Download our template
                    for the correct format.
                  </p>
                </div>
                <div>
                  <h4 class="font-medium text-gray-700">Need more help?</h4>
                  <p class="text-sm text-gray-600">
                    Contact our support team at support@appointease.com
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- View Employees -->
    <section id="view-employees" class="section-content p-6">
      <div class="bg-white rounded-lg shadow-md p-6 max-w-[98%] mx-auto">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">View Employees</h2>

        <!-- Search and Date Range -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <div class="relative">
            <input type="text" id="employee-search" placeholder="Search employees..."
              class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
            <span class="absolute left-3 top-2.5 text-gray-400">
              <i class="fas fa-search"></i>
            </span>
          </div>

          <div class="flex space-x-4">
            <input type="date" id="date-from"
              class="flex-1 px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
            <input type="date" id="date-to"
              class="flex-1 px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
          </div>

          <div class="flex justify-end">
            <button id="exportButton" onclick="exportToExcel()"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
              <i class="fas fa-file-excel mr-2"></i>
              Export to Excel
            </button>
          </div>
        </div>

        <!-- Filter Dropdowns -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
          <div>
            <select id="filter-department"
              class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
              <option value="">Department</option>
              <option value="it">IT</option>
              <option value="hr">HR</option>
              <option value="finance">Finance</option>
            </select>
          </div>
          <div>
            <select id="filter-designation"
              class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
              <option value="">Designation</option>
              <option value="manager">Manager</option>
              <option value="hod">HOD</option>
              <option value="analyst">Analyst</option>
              <option value="staff">Staff</option>
            </select>
          </div>
          <div>
            <select id="filter-location"
              class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
              <option value="">Location</option>
              <option value="dn">DN Campus</option>
              <option value="mogri">Mogri Campus</option>
              <option value="khetiwadi">Khetiwadi Campus</option>
              <option value="mbpatel">MB Patel Science College Campus</option>
            </select>
          </div>
          <div>
            <select id="filter-category"
              class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
              <option value="">Category</option>
              <option value="permanent">Permanent</option>
              <option value="adhoc">Adhoc</option>
            </select>
          </div>
          <div>
            <select id="filter-salary"
              class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
              <option value="">Salary Category</option>
              <option value="adhoc_with_pf">Adhoc with PF</option>
              <option value="adhoc_without_pf">Adhoc without PF</option>
              <option value="5th_pay">5th Pay</option>
              <option value="6th_pay">6th Pay</option>
            </select>
          </div>

        </div>

        <div class="overflow-x-auto">
          <table id="employeeTable" class="min-w-full bg-white border rounded-lg">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approval
                  Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Code
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institute
                  Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Designation
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">location</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joining Date
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">leaving Date
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blood Group
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nationality
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DOB</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Father Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mother Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spouse Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobile</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alt Number
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account
                  Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IFSC Code
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PAN Number
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aadhar Number
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary
                  Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duty Hours
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hours
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours per Day
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary
                  Payband</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PF Number
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PF Join Date
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conveyance
                  Allowance</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DA</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">HRA</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medical
                  Allowance</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Travelling
                  Allowance</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Other
                  Allowance</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" id="employeeTableBody">
              <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                  <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex space-x-2">
                        <button onclick="editEmployee(<?= $row['id'] ?>)"
                          class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                          <i class="fas fa-edit mr-1"></i>
                          Edit
                        </button>
                        <button onclick="deleteEmployee(<?= $row['id'] ?>)"
                          class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors">
                          <i class="fas fa-trash-alt mr-1"></i>
                          Delete
                        </button>
                        <button
                          onclick="window.open('print.php?id=<?php echo $row['id']; ?>&print=true', '_blank'); return false;"
                          class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-md hover:bg-red-200 transition-colors">
                          <i class="fas fa-file-pdf mr-1"></i>
                          Print
                        </button>
                        <?php if ($row['approval_status'] != 'Approved'): ?>
                          <button onclick="openApprovalModal(<?= $row['id'] ?>)"
                            class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 rounded-md hover:bg-yellow-200 transition-colors">
                            <i class="fas fa-check-circle mr-1"></i>
                            Approve
                          </button>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <?php if ($row['approval_status'] == 'Approved'): ?>
                        <span
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                          Approved
                        </span>
                      <?php else: ?>
                        <span
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                          Pending
                        </span>
                      <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['emp_code'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['institute_name'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['department'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['designation'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['location'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['joining_date'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['leaving_date'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['emp_category'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['full_name'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['gender'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['blood_group'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['nationality'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['dob'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['father_name'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['mother_name'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['spouse_name'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['mobile_number'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['alt_number'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['email'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['address'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['bank_name'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['branch_name'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['account_number'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['ifsc_code'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['pan_number'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['aadhar_number'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['salary_category'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['duty_hours'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['total_hours'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['hours_per_day'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['salary_pay_band'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['basic_salary'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['pf_number'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['pf_join_date'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['ca'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['da'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['hra'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['ma'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['ta'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['other_allowance'] ?? '') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Active
                      </span>
                    </td>

                  </tr>
                <?php endwhile; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-between items-center">
          <div id="pageInfo" class="text-sm text-gray-700"></div>
          <div id="pagination" class="flex space-x-2"></div>
        </div>
      </div>
    </section>

  </main>
  <!-- Create user section -->

  <section id="create-user" class="section-content p-6">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md">
        <div>
          <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Create New User</h2>
        </div>
        <form class="mt-8 space-y-6" action="process_user.php" method="POST" id="signupForm">
          <!-- Role Selection -->
          <div>
            <label for="role" class="block text-sm font-medium text-gray-700">User Role*</label>
            <select id="role" name="role" required
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
              <option value="">Select Role</option>
              <option value="admin">Admin</option>
              <option value="school">School</option>
              <option value="institute">Institute</option>
            </select>
          </div>

          <!-- Common Fields -->
          <div class="space-y-4">
            <div>
              <label for="user_id" class="block text-sm font-medium text-gray-700">User ID*</label>
              <input type="text" id="user_id" name="user_id" required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Name*</label>
              <input type="text" id="name" name="name" required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700">Email*</label>
              <input type="email" id="email" name="email" required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700">Password*</label>
              <input type="password" id="password" name="password" required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
              <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password*</label>
              <input type="password" id="confirm_password" name="confirm_password" required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <!-- School-specific field -->
            <div id="school-field" class="hidden">
              <label for="school_name" class="block text-sm font-medium text-gray-700">School Name*</label>
              <input type="text" id="school_name" name="school_name"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <!-- Institute-specific field -->
            <div id="institute-field" class="hidden">
              <label for="institute_name" class="block text-sm font-medium text-gray-700">Institute Name*</label>
              <input type="text" id="institute_name" name="institute_name"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
          </div>
          <div>
            <button type="submit"
              class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Create User
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- Modern Footer with responsive design -->
  <footer class="bg-white shadow-lg mt-auto border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-8">
      <!-- Main Footer Content -->
      <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-6 md:space-y-0">
        <!-- Left Side - Brand & Copyright -->
        <div class="flex flex-col space-y-4">
          <div class="flex items-center space-x-3">
            <img src="cesLogo.png" alt="CES Logo" class="h-8 w-8">
            <span class="text-sm font-semibold text-gray-800">Charotar Education Society, Anand.</span>
          </div>
        </div>

        <!-- Right Side - Links -->
        <div class="grid grid-cols-1 justify-content-center md:flex md:space-x-8 gap-4">
          <a href="#"
            class="text-sm text-gray-600 hover:text-blue-600 transition-colors duration-200 flex items-center space-x-1">
            <i class="fas fa-shield-alt text-gray-400"></i>
            <span>Privacy Policy</span>
          </a>
          <a href="#"
            class="text-sm text-gray-600 hover:text-blue-600 transition-colors duration-200 flex items-center space-x-1">
            <i class="fas fa-file-contract text-gray-400"></i>
            <span>Terms of Service</span>
          </a>
          <a href="#"
            class="text-sm text-gray-600 hover:text-blue-600 transition-colors duration-200 flex items-center space-x-1">
            <i class="fas fa-envelope text-gray-400"></i>
            <span>Contact Us</span>
          </a>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="mt-8 pt-6 border-t border-gray-100">
        <p class="text-sm text-center text-gray-600">
          © 2024 CES. All rights reserved.
        </p>
        <p class="text-sm text-center text-gray-500">
          Designed and developed with <i class="fas fa-heart text-red-500"></i> by <b>codeTech</b> Team
        </p>
      </div>
    </div>
  </footer>
  <script>
    // Custom Toast Function
    function showToast(message, type = 'success') {
      const toast = document.createElement('div');
      toast.className = `toast ${type}`;
      toast.textContent = message;
      document.body.appendChild(toast);

      // Show the toast
      setTimeout(() => {
        toast.classList.add('show');
      }, 100);

      // Hide and remove the toast after 5 seconds
      setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
          document.body.removeChild(toast);
        }, 300); // Wait for fade-out transition
      }, 5000);
    }

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
              const contentType = response.headers.get('content-type');
              if (contentType && contentType.includes('application/json')) {
                return response.json();
              }
              // If not JSON, assume redirect handled by PHP
              return response.text().then(() => {
                // Show toast on success and redirect
                showToast('Employee details updated successfully', 'success');
                setTimeout(() => {
                  window.location.href = '<?php echo $_SERVER['HTTP_REFERER'] ?? 'viewemp.php'; ?>';
                }, 2000); // Redirect after 2 seconds to allow toast visibility
                throw new Error('Non-JSON response'); // Skip further JSON processing
              });
            })
            .then(data => {
              if (data && data.success) {
                showToast(data.message, 'success');
                setTimeout(() => {
                  window.location.href = '<?php echo $_SERVER['HTTP_REFERER'] ?? 'viewemp.php'; ?>';
                }, 2000); // Redirect after 2 seconds
              } else {
                showToast(data.message || 'Error updating employee details', 'error');
              }
            })
            .catch(error => {
              console.error('Error:', error);
              showToast('An error occurred while updating employee details. Please try again.', 'error');
            });
        });
      }
    });
  </script>
  <!-- script for upload excel file to database -->
  <script>
    // Check if XLSX is loaded
    if (typeof XLSX === 'undefined') {
      console.error('XLSX library not loaded! Please check your script tags.');
    }

    // Get DOM elements
    const fileInput = document.getElementById('excel-upload');
    const previewSection = document.getElementById('preview-section');
    const previewTable = document.getElementById('preview-table');
    const uploadBtn = document.getElementById('upload-btn');
    const cancelBtn = document.getElementById('cancel-btn');

    let processedData = [];

    // Handle file selection
    fileInput.addEventListener('change', async (e) => {
      const file = e.target.files[0];
      if (!file) return;

      // Validate file type
      const validTypes = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel'
      ];
      if (!validTypes.includes(file.type)) {
        alert('Please upload a valid Excel file (.xlsx or .xls)');
        fileInput.value = '';
        return;
      }

      try {
        const data = await readExcelFile(file);
        if (data && data.length > 0) {
          processedData = data;
          showPreview(data);
          previewSection.classList.remove('hidden');
        } else {
          throw new Error('No data found in the Excel file');
        }
      } catch (error) {
        console.error('Excel processing error:', error);
        alert('Error reading Excel file: ' + error.message);
        resetUpload();
      }
    });

    // Read Excel file
    async function readExcelFile(file) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();

        reader.onload = function (e) {
          try {
            const data = e.target.result;
            const workbook = XLSX.read(data, {
              type: 'array'
            });
            const firstSheetName = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[firstSheetName];
            const jsonData = XLSX.utils.sheet_to_json(worksheet);

            if (jsonData.length === 0) {
              throw new Error('Excel file is empty');
            }

            // Validate and transform data
            const transformedData = jsonData.map((row, index) => {
              try {
                return {
                  emp_code: row['Employee Code'] || '',
                  institute_name: row['Institute Name'] || '',
                  department: row['Department'] || '',
                  designation: row['Designation'] || '',
                  location: row['Location'] || '',
                  joining_date: formatDate(row['Joining Date']),
                  leaving_date: formatDate(row['Leaving Date']),
                  emp_category: row['Category'] || '',
                  full_name: row['Full Name'] || '',
                  gender: validateGender(row['Gender']),
                  blood_group: row['Blood Group'] || '',
                  nationality: row['Nationality'] || '',
                  dob: formatDate(row['DOB']),
                  father_name: row['Father Name'] || '',
                  mother_name: row['Mother Name'] || '',
                  spouse_name: row['Spouse Name'] || '',
                  mobile_number: row['Mobile Number']?.toString() || '',
                  alt_number: row['Alt Number']?.toString() || '',
                  email: row['Email'] || '',
                  address: row['Address'] || '',
                  bank_name: row['Bank Name'] || '',
                  branch_name: row['Branch Name'] || '',
                  account_number: row['Account Number']?.toString() || '',
                  ifsc_code: row['IFSC Code'] || '',
                  pan_number: row['PAN Number'] || '',
                  aadhar_number: row['Aadhar Number']?.toString() || '',
                  salary_category: row['Salary Category'] || '',
                  duty_hours: parseFloat(row['Duty Hours']) || null,
                  total_hours: parseFloat(row['Total Hours']) || null,
                  hours_per_day: parseFloat(row['Hours Per Day']) || null,
                  salary_pay_band: row['Salary Pay Band'] || '',
                  basic_salary: parseFloat(row['Basic Salary']) || null,
                  pf_number: row['PF Number'] || '',
                  pf_join_date: formatDate(row['PF Join Date']),
                  ca: parseFloat(row['CA']) || null,
                  da: parseFloat(row['DA']) || null,
                  hra: parseFloat(row['HRA']) || null,
                  ta: parseFloat(row['TA']) || null,
                  ma: parseFloat(row['MA']) || null,
                  other_allowance: parseFloat(row['Other Allowance']) || null
                };
              } catch (error) {
                throw new Error(`Error processing row ${index + 1}: ${error.message}`);
              }
            });

            resolve(transformedData);
          } catch (error) {
            reject(error);
          }
        };

        reader.onerror = function (error) {
          reject(new Error('Error reading file: ' + error.message));
        };

        reader.readAsArrayBuffer(file);
      });
    }

    // Helper function to validate gender
    function validateGender(gender) {
      if (!gender) return null;
      const validGenders = ['Male', 'Female', 'Other'];
      const normalizedGender = gender.trim().charAt(0).toUpperCase() + gender.trim().slice(1).toLowerCase();
      return validGenders.includes(normalizedGender) ? normalizedGender : null;
    }

    // Helper function to format dates
    function formatDate(dateValue) {
      if (!dateValue) return null;

      let date;
      if (typeof dateValue === 'number') {
        // Handle Excel serial number dates
        date = new Date(Math.round((dateValue - 25569) * 86400 * 1000));
      } else if (typeof dateValue === 'string') {
        // Try parsing various date formats
        date = new Date(dateValue);
        if (isNaN(date.getTime())) {
          // Try parsing DD-MM-YYYY format
          const parts = dateValue.split(/[-\/]/);
          if (parts.length === 3) {
            date = new Date(parts[2], parts[1] - 1, parts[0]);
          }
        }
      } else {
        date = new Date(dateValue);
      }

      if (isNaN(date.getTime())) return null;

      return date.toISOString().split('T')[0];
    }

    // Show preview of the data
    function showPreview(data) {
      if (!data || data.length === 0) return;

      const headers = Object.keys(data[0]);
      const thead = document.createElement('thead');
      const headerRow = document.createElement('tr');

      headers.forEach(header => {
        const th = document.createElement('th');
        th.textContent = header.replace(/_/g, ' ').toUpperCase();
        th.className = 'px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50';
        headerRow.appendChild(th);
      });

      thead.appendChild(headerRow);

      const tbody = document.createElement('tbody');
      data.slice(0, 5).forEach((row, rowIndex) => {
        const tr = document.createElement('tr');
        tr.className = rowIndex % 2 === 0 ? 'bg-white' : 'bg-gray-50';

        headers.forEach(header => {
          const td = document.createElement('td');
          td.textContent = row[header] || '';
          td.className = 'px-4 py-2 text-sm text-gray-900 border-t';
          tr.appendChild(td);
        });

        tbody.appendChild(tr);
      });

      previewTable.innerHTML = '';
      previewTable.appendChild(thead);
      previewTable.appendChild(tbody);

      // Add preview info
      const previewInfo = document.createElement('div');
      previewInfo.className = 'text-sm text-gray-500 mt-2';
      previewInfo.textContent = `Showing ${Math.min(5, data.length)} of ${data.length} records`;
      previewTable.parentElement.appendChild(previewInfo);
    }

    // Handle upload button click
    uploadBtn.addEventListener('click', async () => {
      if (!processedData || processedData.length === 0) {
        alert('No data to upload');
        return;
      }

      try {
        // Show loading state
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';

        const response = await fetch('upload.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            employees: processedData
          })
        });

        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
          throw new Error('Server returned non-JSON response: ' + await response.text());
        }

        const result = await response.json();

        if (result.success) {
          alert('Data uploaded successfully!');
          resetUpload();
        } else {
          throw new Error(result.message || 'Upload failed');
        }
      } catch (error) {
        console.error('Upload error:', error);
        alert('Error uploading data: ' + error.message);
      } finally {
        // Reset button state
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fas fa-upload mr-2"></i>Upload Data';
      }
    });

    // Handle cancel button click
    cancelBtn.addEventListener('click', resetUpload);

    // Reset the upload form
    function resetUpload() {
      fileInput.value = '';
      previewSection.classList.remove('hidden');
      previewTable.innerHTML = '';
      processedData = [];
    }

    // Drag and drop handlers
    const dropZone = document.querySelector('.border-dashed');
    if (dropZone) {
      ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
      });

      function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
      }

      ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
      });

      ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
      });

      function highlight(e) {
        dropZone.classList.add('border-blue-500', 'bg-blue-50');
      }

      function unhighlight(e) {
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
      }

      dropZone.addEventListener('drop', handleDrop, false);

      function handleDrop(e) {
        const dt = e.dataTransfer;
        const file = dt.files[0];
        fileInput.files = dt.files;
        fileInput.dispatchEvent(new Event('change'));
      }
    }

    document.getElementById('upload-excel').addEventListener('submit', function (event) {
      event.preventDefault();
      var fileInput = document.getElementById('excel-upload');
      var file = fileInput.files[0];
      var reader = new FileReader();

      reader.onload = function (e) {
        var data = new Uint8Array(e.target.result);
        var workbook = XLSX.read(data, {
          type: 'array'
        });
        var sheetName = workbook.SheetNames[0];
        var worksheet = workbook.Sheets[sheetName];
        var json = XLSX.utils.sheet_to_json(worksheet);

        fetch('upload.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            employees: json
          })
        })
          .then(response => response.text())
          .then(data => {
            try {
              const jsonResponse = JSON.parse(data);
              alert(jsonResponse.message);
              window.location.href = 'index.php';
            } catch (error) {
              console.error('Error parsing JSON:', error);
              console.error('Server response:', data);
              alert('uploading data: ' + error.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert(' uploading data: ' + error.message);
          });
      };

      reader.readAsArrayBuffer(file);
    });
  </script>

  <!-- Approval Modal -->
  <div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3 text-center">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Approve Employee</h3>
        <div class="mt-4">
          <form id="approvalForm">
            <input type="hidden" id="approvalEmployeeId" name="employee_id">
            <div class="mb-4">
              <label for="newEmployeeCode" class="block text-sm font-medium text-gray-700">
                Employee Code
              </label>
              <input type="text" id="newEmployeeCode" name="new_employee_code"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                required>
            </div>
            <div class="flex justify-between mt-4">
              <button type="button" onclick="cancelApproval()"
                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                Cancel
              </button>
              <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Approve
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    function openApprovalModal(employeeId) {
      document.getElementById('approvalEmployeeId').value = employeeId;
      document.getElementById('approvalModal').classList.remove('hidden');
    }

    function cancelApproval() {
      document.getElementById('approvalModal').classList.add('hidden');
    }

    document.getElementById('approvalForm').addEventListener('submit', function (e) {
      e.preventDefault();

      // Create FormData to send the form data
      var formData = new FormData(this);

      // Send AJAX request to approve employee
      fetch('approve_employee.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Refresh the page or update the row
            location.reload();
          } else {
            alert('Approval failed: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred during approval');
        });
    });
  </script>
  <script>
    // Global variables
    const ROWS_PER_PAGE = 10;
    let currentPage = 1;
    let filteredData = [];
    const allRows = Array.from(document.querySelectorAll('#employeeTableBody tr'));

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
      filteredData = allRows;
      updateTable();
      setupEventListeners();
    });

    function setupEventListeners() {
      // Search input with debouncing
      let searchTimeout;
      document.getElementById('employee-search').addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => filterData(), 300);
      });

      // Date filters
      document.getElementById('date-from').addEventListener('change', filterData);
      document.getElementById('date-to').addEventListener('change', filterData);

      // Dropdown filters
      document.getElementById('filter-department').addEventListener('change', filterData);
      document.getElementById('filter-designation').addEventListener('change', filterData);
      document.getElementById('filter-location').addEventListener('change', filterData);
      document.getElementById('filter-category').addEventListener('change', filterData);
      document.getElementById('filter-salary').addEventListener('change', filterData);

      // Add other filter listeners similarly
    }

    function filterData() {
      const searchTerm = document.getElementById('employee-search').value.toLowerCase();
      const dateFrom = document.getElementById('date-from').value;
      const dateTo = document.getElementById('date-to').value;
      const department = document.getElementById('filter-department').value.toLowerCase();
      const designation = document.getElementById('filter-designation').value.toLowerCase();
      const location = document.getElementById('filter-location').value.toLowerCase();
      const category = document.getElementById('filter-category').value.toLowerCase();
      const salary = document.getElementById('filter-salary').value.toLowerCase();
      // Add other filters similarly

      filteredData = allRows.filter(row => {
        const text = row.textContent.toLowerCase();
        const rowDepartment = row.children[4].textContent.toLowerCase();
        const rowDesignation = row.children[5].textContent.toLowerCase();
        const rowLocation = row.children[6].textContent.toLowerCase();
        const rowCategory = row.children[9].textContent.toLowerCase();
        const rowSalary = row.children[28].textContent.toLowerCase();
        const joiningDate = row.children[7].textContent; // Adjust index based on your table structure

        let matchesSearch = searchTerm === '' || text.includes(searchTerm);
        let matchesDepartment = department === '' || rowDepartment === department;
        let matchesDesignation = designation === '' || rowDesignation === designation;
        let matchesLocation = location === '' || rowLocation === location;
        let matchesCategory = category === '' || rowCategory === category;
        let matchesSalary = salary === '' || rowSalary === salary;
        let matchesDate = true;

        if (dateFrom && dateTo) {
          const date = new Date(joiningDate);
          const from = new Date(dateFrom);
          const to = new Date(dateTo);
          matchesDate = date >= from && date <= to;
        }

        return matchesSearch && matchesDepartment && matchesDesignation && matchesLocation && matchesCategory && matchesSalary && matchesDate;
      });

      currentPage = 1;
      updateTable();
    }

    function updateTable() {
      const startIndex = (currentPage - 1) * ROWS_PER_PAGE;
      const endIndex = startIndex + ROWS_PER_PAGE;
      const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);

      // Hide all rows
      allRows.forEach(row => row.style.display = 'none');

      // Show filtered rows for current page
      filteredData.slice(startIndex, endIndex).forEach(row => row.style.display = '');

      updatePagination(totalPages);
      updatePageInfo(startIndex, endIndex);
    }

    function updatePagination(totalPages) {
      const pagination = document.getElementById('pagination');
      pagination.innerHTML = '';

      for (let i = 1; i <= totalPages; i++) {
        const button = document.createElement('button');
        button.textContent = i;
        button.className = `px-3 py-1 rounded-md ${i === currentPage ?
          'bg-blue-600 text-white' :
          'bg-gray-200 text-gray-700 hover:bg-gray-300'}`;
        button.onclick = () => {
          currentPage = i;
          updateTable();
        };
        pagination.appendChild(button);
      }
    }

    function updatePageInfo(startIndex, endIndex) {
      const pageInfo = document.getElementById('pageInfo');
      pageInfo.textContent = `Showing ${startIndex + 1} to ${Math.min(endIndex, filteredData.length)} of ${filteredData.length} entries`;
    }
  </script>




  <script>
    document.getElementById('role').addEventListener('change', function () {
      const schoolField = document.getElementById('school-field');
      const instituteField = document.getElementById('institute-field');
      schoolField.classList.add('hidden');
      instituteField.classList.add('hidden');
      if (this.value === 'school') {
        schoolField.classList.remove('hidden');
        document.getElementById('school_name').required = true;
        document.getElementById('institute_name').required = false;
      } else if (this.value === 'institute') {
        instituteField.classList.remove('hidden');
        document.getElementById('institute_name').required = true;
        document.getElementById('school_name').required = false;
      } else {
        document.getElementById('school_name').required = false;
        document.getElementById('institute_name').required = false;
      }
    });

    document.getElementById('signupForm').addEventListener('submit', function (e) {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
      }
    });
  </script>

</body>

</html>