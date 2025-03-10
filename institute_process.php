<?php
// institute_process.php
session_start();
require_once 'config/database.php';

// Define encryption constants (must match process_user.php)
define('SECRET_KEY', 'Ek-Biladi-Jadi');
define('CIPHER_METHOD', 'AES-256-CBC');

// Function to decrypt password
function decryptPassword($encryptedPassword) {
    $key = hash('sha256', SECRET_KEY, true);
    $data = base64_decode($encryptedPassword);
    if ($data === false) {
        return false; // Invalid base64 data
    }
    
    $ivLength = openssl_cipher_iv_length(CIPHER_METHOD);
    $iv = substr($data, 0, $ivLength);
    $cipherText = substr($data, $ivLength);
    
    $decrypted = openssl_decrypt($cipherText, CIPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv);
    return $decrypted !== false ? $decrypted : false;
}

// Function to validate institute credentials
function validateInstituteLogin($pdo, $institute_id, $password) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM institute_users WHERE user_id = :institute_id");
        $stmt->execute(['institute_id' => $institute_id]);
        $institute = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($institute) {
            // Decrypt the stored password
            $decryptedPassword = decryptPassword($institute['password']);
            if ($decryptedPassword !== false && $decryptedPassword === $password) {
                return $institute;
            }
        }
        return false;
    } catch (PDOException $e) {
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

            // Set cookies (secure and HTTP-only)
            setcookie('institute_remember', $token, $expiry, '/', '', true, true);
        }

        // Set success message
        $_SESSION['success'] = "Login successful! Welcome, " . htmlspecialchars($institute['name']) . ".";
        header("Location: ins_dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid credentials";
        header("Location: ins_login.php");
        exit();
    }
}

// Optional: Auto-login with remember-me cookie
if (!isset($_SESSION['institute_id']) && isset($_COOKIE['institute_remember'])) {
    $token = $_COOKIE['institute_remember'];
    $stmt = $pdo->prepare("SELECT * FROM institute_users WHERE remember_token = :token AND token_expiry > NOW()");
    $stmt->execute(['token' => $token]);
    $institute = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($institute) {
        $_SESSION['institute_id'] = $institute['user_id'];
        $_SESSION['institute_name'] = $institute['institute_name'];
        $_SESSION['user_name'] = $institute['name'];
        $_SESSION['is_institute'] = true;
        $_SESSION['success'] = "Welcome back, " . htmlspecialchars($institute['name']) . ".";
        header("Location: ins_dashboard.php");
        exit();
    } else {
        // Invalid or expired token, clear cookie
        setcookie('institute_remember', '', time() - 3600, '/', '', true, true);
    }
}
?>