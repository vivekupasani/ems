<?php
// No-cache headers to prevent login page being shown after login
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();

// Redirect if already logged in
if (isset($_SESSION['institute_id']) && $_SESSION['is_institute'] === true) {
    header("Location: ins_dashboard.php");
    exit();
}

// Check remember me cookie
if (isset($_COOKIE['institute_remember']) && !isset($_SESSION['is_institute'])) {
    require_once 'config/database.php';

    $token = $_COOKIE['institute_remember'];
    $stmt = $pdo->prepare("SELECT * FROM institute_users WHERE remember_token = :token AND token_expiry > NOW()");
    $stmt->execute(['token' => $token]);
    $institute = $stmt->fetch();

    if ($institute) {
        session_regenerate_id(true);
        $_SESSION['institute_id'] = $institute['user_id'];
        $_SESSION['institute_name'] = $institute['institute_name'];
        $_SESSION['user_name'] = $institute['name'];
        $_SESSION['is_institute'] = true;
        header("Location: ins_dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Institute Login - Multi-Role Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                window.location.reload();
            }
        });
    </script>

</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo $_SESSION['error']; ?></span>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo $_SESSION['success']; ?></span>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="text-center mb-8">
            <i class="fas fa-university text-6xl text-blue-600"></i>
            <h2 class="mt-4 text-3xl font-bold text-gray-900">Institute Login</h2>
            <p class="mt-2 text-gray-600">Access your institute portal</p>
        </div>

        <div class="bg-white py-8 px-6 shadow-2xl rounded-lg">
            <form action="institute_process.php" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Institute ID</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-building text-gray-400"></i>
                        </div>
                        <input type="text" name="institute_id" required
                            class="block w-full pl-10 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter your institute ID">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" required
                            class="block w-full pl-10 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter your password">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember-me" name="remember-me"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">Remember me</label>
                    </div>
                    <a href="institute_forgot_password.php" class="text-sm text-blue-600 hover:text-blue-500">Forgot
                        password?</a>
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Sign in to Institute Portal
                </button>
            </form>
        </div>

        <div class="mt-4 text-center">
            <a href="default.php" class="text-sm text-gray-600 hover:text-blue-500">
                <i class="fas fa-arrow-left mr-2"></i>Back to Role Selection
            </a>
        </div>
    </div>
</body>

</html>