<?php
// school_process.php
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

// Function to validate school credentials
function validateSchoolLogin($pdo, $school_id, $password) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM school_users WHERE user_id = :school_id");
        $stmt->execute(['school_id' => $school_id]);
        $school = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($school) {
            // Decrypt the stored password
            $decryptedPassword = decryptPassword($school['password']);
            if ($decryptedPassword !== false && $decryptedPassword === $password) {
                return $school;
            }
        }
        return false;
    } catch (PDOException $e) {
        error_log("School login error: " . $e->getMessage());
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $school_id = trim($_POST['school_id'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember-me']);

    // Validate input
    if (empty($school_id) || empty($password)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: school_login.php");
        exit();
    }

    // Attempt login
    $school = validateSchoolLogin($pdo, $school_id, $password);
    
    if ($school) {
        // Set session variables
        $_SESSION['school_id'] = $school['user_id'];
        $_SESSION['school_name'] = $school['school_name'];
        $_SESSION['user_name'] = $school['name'];
        $_SESSION['is_school'] = true;

        // Handle remember me
        if ($remember_me) {
            $token = bin2hex(random_bytes(32));
            $expiry = time() + (30 * 24 * 60 * 60); // 30 days

            // Store token in database
            $stmt = $pdo->prepare("UPDATE school_users SET remember_token = :token, token_expiry = :expiry WHERE user_id = :school_id");
            $stmt->execute([
                'token' => $token,
                'expiry' => date('Y-m-d H:i:s', $expiry),
                'school_id' => $school_id
            ]);

            // Set cookies (secure and HTTP-only)
            setcookie('school_remember', $token, $expiry, '/', '', true, true);
        }

        // Set success message
        $_SESSION['success'] = "Login successful! Welcome, " . htmlspecialchars($school['name']) . ".";
        header("Location: school_dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid credentials";
        header("Location: school_login.php");
        exit();
    }
}

// Optional: Auto-login with remember-me cookie
if (!isset($_SESSION['school_id']) && isset($_COOKIE['school_remember'])) {
    $token = $_COOKIE['school_remember'];
    $stmt = $pdo->prepare("SELECT * FROM school_users WHERE remember_token = :token AND token_expiry > NOW()");
    $stmt->execute(['token' => $token]);
    $school = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($school) {
        $_SESSION['school_id'] = $school['user_id'];
        $_SESSION['school_name'] = $school['school_name'];
        $_SESSION['user_name'] = $school['name'];
        $_SESSION['is_school'] = true;
        $_SESSION['success'] = "Welcome back, " . htmlspecialchars($school['name']) . ".";
        header("Location: school_dashboard.php");
        exit();
    } else {
        // Invalid or expired token, clear cookie
        setcookie('school_remember', '', time() - 3600, '/', '', true, true);
    }
}
?>