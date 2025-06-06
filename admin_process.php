<?php
// admin_process.php
session_start();
require_once 'config/database.php';

define('SECRET_KEY', 'Ek-Biladi-Jadi');
define('CIPHER_METHOD', 'AES-256-CBC');

// Function to decrypt the password
function decryptPassword($encryptedPassword)
{
    $key = hash('sha256', SECRET_KEY, true);
    $data = base64_decode($encryptedPassword);
    $iv = substr($data, 0, 16);
    $cipherText = substr($data, 16);
    return openssl_decrypt($cipherText, CIPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv);
}

// Function to validate admin credentials
function validateAdminLogin($pdo, $admin_id, $password)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE user_id = :admin_id");
        $stmt->execute(['admin_id' => $admin_id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            $decryptedStoredPassword = decryptPassword($admin['password']);
            if ($decryptedStoredPassword === $password) {
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

        // Handle remember me
        if ($remember_me) {
            $token = bin2hex(random_bytes(32));
            $expiry = time() + (30 * 24 * 60 * 60); // 30 days

            // Store token in database
            $stmt = $pdo->prepare("UPDATE admin_users SET remember_token = :token, token_expiry = :expiry WHERE user_id = :admin_id");
            $stmt->execute([
                'token' => $token,
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
?>