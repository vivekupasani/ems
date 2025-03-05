<?php
// institute_process.php
session_start();
require_once 'config/database.php';

// Function to validate institute credentials
function validateInstituteLogin($pdo, $institute_id, $password) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM institute_users WHERE user_id = :institute_id");
        $stmt->execute(['institute_id' => $institute_id]);
        $institute = $stmt->fetch();

        if ($institute && md5($password, $institute['password'])) {
            return $institute;
        }
        return false;
    } catch(PDOException $e) {
        error_log("Institute login error: " . $e->getMessage());
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $institute_id = trim($_POST['institute_id'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember-me']);

    // Validate input
    if (empty($institute_id) || empty($password)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: ins_login.php");
        exit();
    }

    // Attempt login
    $institute = validateInstituteLogin($pdo, $institute_id, $password);
    
    if ($institute) {
        // Set session variables
        $_SESSION['institute_id'] = $institute['user_id'];
        $_SESSION['institute_name'] = $institute['institute_name'];
        $_SESSION['user_name'] = $institute['name'];
        $_SESSION['is_institute'] = true;

        // Handle remember me
        if ($remember_me) {
            $token = bin2hex(random_bytes(32));
            $expiry = time() + (30 * 24 * 60 * 60); // 30 days

            // Store token in database
            $stmt = $pdo->prepare("UPDATE institute_users SET remember_token = :token, token_expiry = :expiry WHERE user_id = :institute_id");
            $stmt->execute([
                'token' => $token,
                'expiry' => date('Y-m-d H:i:s', $expiry),
                'institute_id' => $institute_id
            ]);

            // Set cookies
            setcookie('institute_remember', $token, $expiry, '/', '', true, true);
        }

        // Redirect to dashboard
        header("Location: ins_dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid credentials";
        header("Location: ins_login.php");
        exit();
    }
}
?>