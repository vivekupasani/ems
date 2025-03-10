<?php
session_start();
require_once 'config/database.php';

// Define encryption constants
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

    return openssl_decrypt($cipherText, CIPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv);
}

// Function to validate admin credentials
function validateAdminLogin($pdo, $admin_id, $password) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE user_id = :admin_id");
        $stmt->execute(['admin_id' => $admin_id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            // Decrypt stored password and compare
            $decryptedPassword = decryptPassword($admin['password']);
            if ($decryptedPassword !== false && $decryptedPassword === $password) {
                return $admin;
            }
        }
        return false;
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_id = trim($_POST['admin_id'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember-me']);

    // Validate input
    if (empty($admin_id) || empty($password)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: admin_login.php");
        exit();
    }

    // Attempt login
    $admin = validateAdminLogin($pdo, $admin_id, $password);

    if ($admin) {
        // Set session variables
        $_SESSION['admin_id'] = $admin['user_id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['is_admin'] = true;

        // Secure session handling
        session_regenerate_id(true);

        // Handle remember me
        if ($remember_me) {
            $token = bin2hex(random_bytes(32));
            $hashedToken = password_hash($token, PASSWORD_DEFAULT);
            $expiry = time() + (30 * 24 * 60 * 60); // 30 days

            // Store hashed token in the database
            $stmt = $pdo->prepare("UPDATE admin_users SET remember_token = :token, token_expiry = :expiry WHERE user_id = :admin_id");
            $stmt->execute([
                'token' => $hashedToken,
                'expiry' => date('Y-m-d H:i:s', $expiry),
                'admin_id' => $admin_id
            ]);

            // Set cookies (secure and HTTP-only)
            setcookie('admin_remember', $token, $expiry, '/', '', true, true);
        }

        // Redirect to dashboard
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid credentials";
        header("Location: admin_login.php");
        exit();
    }
}

// Auto-login with remember-me cookie
if (!isset($_SESSION['admin_id']) && isset($_COOKIE['admin_remember'])) {
    $token = $_COOKIE['admin_remember'];
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE token_expiry > NOW()");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($token, $admin['remember_token'])) {
        $_SESSION['admin_id'] = $admin['user_id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['is_admin'] = true;
        session_regenerate_id(true);
        header("Location: index.php");
        exit();
    } else {
        setcookie('admin_remember', '', time() - 3600, '/', '', true, true);
    }
}
?>
