<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = trim($_POST['user_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);
    $school_name = $role === 'school' ? trim($_POST['school_name']) : null;
    $institute_name = $role === 'institute' ? trim($_POST['institute_name']) : null;

    $errors = [];

    // Validate fields
    if (empty($user_id) || empty($name) || empty($email) || empty($password) || empty($role)) {
        $errors[] = "All required fields must be filled.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        try {
            if ($role === 'school') {
            // Check if user already exists
            $stmt = $pdo->prepare("SELECT id FROM school_users WHERE user_id = :user_id OR email = :email");
            $stmt->execute([':user_id' => $user_id, ':email' => $email]);
            
            }elseif ($role === 'institute') {
                // Check if user already exists
                $stmt = $pdo->prepare("SELECT id FROM institute_users WHERE user_id = :user_id OR email = :email");
                $stmt->execute([':user_id' => $user_id, ':email' => $email]);
                
            }elseif ($role === 'admin') {
                    // Check if user already exists
                    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE user_id = :user_id OR email = :email");
                    $stmt->execute([':user_id' => $user_id, ':email' => $email]);
                    }

            if ($stmt->rowCount() > 0) {
                $errors[] = "User ID or email already exists.";
            } else {
                // Insert user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                if ($role === 'school') {
                    $stmt = $pdo->prepare("INSERT INTO school_users (user_id, password, name, email, school_name) VALUES (:user_id, :password, :name, :email, :school_name)");
                    $stmt->execute([
                        ':user_id' => $user_id,
                        ':password' => $hashed_password,
                        ':name' => $name,
                        ':email' => $email,
                        ':school_name' => $school_name,
                    ]);
                } elseif ($role === 'institute') {
                    $stmt = $pdo->prepare("INSERT INTO institute_users (user_id, password, name, email, institute_name) VALUES (:user_id, :password, :name, :email, :institute_name)");
                    $stmt->execute([
                        ':user_id' => $user_id,
                        ':password' => $hashed_password,
                        ':name' => $name,
                        ':email' => $email,
                        ':institute_name' => $institute_name,
                    ]);
                } elseif ($role === 'admin') {
                    $stmt = $pdo->prepare("INSERT INTO admin_users (user_id, password, name, email) VALUES (:user_id, :password, :name, :email)");
                    $stmt->execute([
                        ':user_id' => $user_id,
                        ':password' => $hashed_password,
                        ':name' => $name,
                        ':email' => $email,
                    ]);
                }
                $_SESSION['success_message'] = "User created successfully.";
                header('Location: index.php#create-user');
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    $_SESSION['error_messages'] = $errors;
    header('Location: index.php#create-user');
    
}
?>
