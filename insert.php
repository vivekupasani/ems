<?php
// process.php
session_start();
require_once 'config.php';

$conn = connectDB();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

    // Validate required fields
    $required_fields = [
        'institute_name', 'department', 'designation', 'location', 'joining_date',
        'emp_category', 'full_name', 'gender', 'blood_group', 'nationality', 'dob',
        'father_name', 'mother_name', 'mobile_number', 'alt_number', 'email',
        'address', 'bank_name', 'branch_name', 'account_number', 'ifsc_code',
        'pan_number', 'aadhar_number', 'salary_category', 'duty_hours', 'total_hours',
        'hours_per_day', 'salary_pay_band', 'basic_salary', 'ca', 'da', 'hra', 'ta',
        'ma', 'other_allowance', 'pf_number', 'pf_join_date'
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

    // File upload handling
    $upload_dir = "uploads/";
    $required_files = [
        'profile_photo' => '.jpg',
        'aadhar_copy' => 'aadharcard.jpg',
        'pan_copy' => 'pancard.jpg',
        'bank_copy' => 'bankcopy.jpg'
    ];

    $uploaded_files = [];

    if (empty($errors)) {
        // Prepare variables for binding
        $emp_code = null; // Keep blank as per your request
        $institute_name = $_POST['institute_name'];
        $department = $_POST['department'];
        $designation = $_POST['designation'];
        $location = $_POST['location'];
        $joining_date = $_POST['joining_date'];
        $leaving_date = $_POST['leaving_date'] ?? null;
        $emp_category = $_POST['emp_category'];
        $full_name = $_POST['full_name'];
        $gender = $_POST['gender'];
        $blood_group = $_POST['blood_group'];
        $nationality = $_POST['nationality'];
        $dob = $_POST['dob'];
        $father_name = $_POST['father_name'];
        $mother_name = $_POST['mother_name'];
        $spouse_name = $_POST['spouse_name'] ?? null;
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
        $other_salary_category = null; // Not in form, set to NULL
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
        $ta = $_POST['ta'];
        $ma = $_POST['ma'];
        $other_allowance = $_POST['other_allowance'];
        $profile_photo = null;
        $aadhar_copy = null;
        $pan_copy = null;
        $bank_copy = null;

        // Prepare the SQL statement (45 columns, excluding id, created_at, updated_at, approval_status)
        $stmt = $conn->prepare("INSERT INTO employees (
            emp_code, institute_name, department, designation, location,
            joining_date, leaving_date, emp_category, full_name, gender,
            blood_group, nationality, dob, father_name, mother_name,
            spouse_name, mobile_number, alt_number, email, address,
            bank_name, branch_name, account_number, ifsc_code,
            pan_number, aadhar_number, salary_category, other_salary_category,
            duty_hours, total_hours, hours_per_day, salary_pay_band,
            basic_salary, pf_number, pf_join_date, ca, da, hra, ta, ma,
            other_allowance, profile_photo, aadhar_copy, pan_copy, bank_copy
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        // Bind parameters (45 values)
        $stmt->bind_param(
            "sssssssssssssssssssssssssssssssssssssssssssss",
            $emp_code, $institute_name, $department, $designation, $location,
            $joining_date, $leaving_date, $emp_category, $full_name, $gender,
            $blood_group, $nationality, $dob, $father_name, $mother_name,
            $spouse_name, $mobile_number, $alt_number, $email, $address,
            $bank_name, $branch_name, $account_number, $ifsc_code,
            $pan_number, $aadhar_number, $salary_category, $other_salary_category,
            $duty_hours, $total_hours, $hours_per_day, $salary_pay_band,
            $basic_salary, $pf_number, $pf_join_date, $ca, $da, $hra, $ta, $ma,
            $other_allowance, $profile_photo, $aadhar_copy, $pan_copy, $bank_copy
        );

        // Execute the statement
        if ($stmt->execute()) {
            // Get the auto-incremented ID
            $employee_id = $conn->insert_id;

            // Handle file uploads
            $employee_dir = $upload_dir . $employee_id . "/";
            if (!file_exists($employee_dir)) {
                mkdir($employee_dir, 0777, true);
            }

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

                    $final_filename = ($input_name === 'profile_photo') ? $employee_id . $new_filename : $new_filename;
                    $upload_path = $employee_dir . $final_filename;

                    if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $upload_path)) {
                        $uploaded_files[$input_name] = $final_filename;

                        // Update the database with file paths
                        $update_stmt = $conn->prepare("UPDATE employees SET $input_name = ? WHERE id = ?");
                        $update_stmt->bind_param("si", $final_filename, $employee_id);
                        $update_stmt->execute();
                        $update_stmt->close();
                    } else {
                        $errors[] = "Error uploading " . str_replace('_', ' ', $input_name);
                    }
                }
            }

            if (empty($errors)) {
                // Set session variable for success message
                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'New employee record created successfully with files uploaded!'
                ];
                // Redirect to previous page or fallback to a default page
                $redirect_url = $_SERVER['HTTP_REFERER'] ?? 'index.php';
                header("Location: $redirect_url");
                exit();
            } else {
                foreach ($errors as $error) {
                    echo $error . "<br>";
                }
            }
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

$conn->close();
?>