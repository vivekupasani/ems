<?php
// Include database configuration and get the PDO object
$pdo = require_once 'config/database.php';

// Define encryption constants
define('SECRET_KEY', 'Ek-Biladi-Jadi');
define('CIPHER_METHOD', 'AES-256-CBC');

// Function to encrypt password
function encryptPassword($password) {
    $key = hash('sha256', SECRET_KEY, true);
    $iv = openssl_random_pseudo_bytes(16);
    $cipherText = openssl_encrypt($password, CIPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $cipherText);
}

// Check if user_id exists (AJAX request handling)
if (isset($_POST['check_user_id']) && isset($_POST['user_id']) && isset($_POST['role'])) {
    $user_id = htmlspecialchars($_POST['user_id']); // Basic sanitization
    $role = htmlspecialchars($_POST['role']);
    
    // Determine which table to check based on role
    if ($role == 'admin') {
        $table = 'admin_users';
    } elseif ($role == 'school') {
        $table = 'school_users';
    } elseif ($role == 'institute') {
        $table = 'institute_users';
    } else {
        die(json_encode(['error' => 'Invalid role']));
    }
    
    // Check if user_id exists using PDO
    $sql = "SELECT user_id FROM $table WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }
    
    exit;
}

// Form processing logic
$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['check_user_id'])) {
    // Validate required fields
    $required_fields = ['role', 'user_id', 'name', 'email', 'password', 'confirm_password'];
    $errors = [];
    
    // Add role-specific required fields
    if ($_POST['role'] == 'school') {
        $required_fields[] = 'school_name';
    } elseif ($_POST['role'] == 'institute') {
        $required_fields[] = 'institute_name';
    }
    
    // Check for missing fields
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
        }
    }
    
    // Validate email format
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    // Check if passwords match
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $errors[] = "Passwords do not match.";
    }
    
    // Process form if no errors
    if (empty($errors)) {
        // Sanitize input data
        $role = htmlspecialchars($_POST['role']);
        $user_id = htmlspecialchars($_POST['user_id']);
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        
        // Encrypt the password
        $encryptedPassword = encryptPassword($_POST['password']);
        
        // Prepare SQL based on role
        if ($role == 'admin') {
            $table = 'admin_users';
            $sql = "INSERT INTO $table (user_id, password, name, email) 
                    VALUES (:user_id, :password, :name, :email)";
            $params = [
                'user_id' => $user_id,
                'password' => $encryptedPassword,
                'name' => $name,
                'email' => $email
            ];
        } elseif ($role == 'school') {
            $school_name = htmlspecialchars($_POST['school_name']);
            $table = 'school_users';
            $sql = "INSERT INTO $table (user_id, password, name, email, school_name) 
                    VALUES (:user_id, :password, :name, :email, :school_name)";
            $params = [
                'user_id' => $user_id,
                'password' => $encryptedPassword,
                'name' => $name,
                'email' => $email,
                'school_name' => $school_name
            ];
        } elseif ($role == 'institute') {
            $institute_name = htmlspecialchars($_POST['institute_name']);
            $table = 'institute_users';
            $sql = "INSERT INTO $table (user_id, password, name, email, institute_name) 
                    VALUES (:user_id, :password, :name, :email, :institute_name)";
            $params = [
                'user_id' => $user_id,
                'password' => $encryptedPassword,
                'name' => $name,
                'email' => $email,
                'institute_name' => $institute_name
            ];
        }
        
        // Check if user_id already exists
        $check_sql = "SELECT user_id FROM $table WHERE user_id = :user_id";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute(['user_id' => $user_id]);
        
        if ($check_stmt->rowCount() > 0) {
            $message = "Error: User ID already exists. Please choose another.";
            $messageType = "error";
        } else {
            // Insert data using PDO
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute($params)) {
                // Redirect to index.php with create-user section
                header("Location: index.php?section=create-user&status=success");
                exit; // Ensure no further code is executed after redirection
            } else {
                $message = "Error: Unable to create user.";
                $messageType = "error";
            }
        }
    } else {
        $message = implode("<br>", $errors);
        $messageType = "error";
    }
}

// If there’s an error, you might want to redirect with an error message
if (!empty($message) && $messageType === "error") {
    header("Location: index.php?section=create-user&status=error&message=" . urlencode($message));
    exit;
}
?>