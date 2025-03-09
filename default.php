<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Selection Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="container mx-auto max-w-4xl">
        <div class="bg-white shadow-2xl rounded-2xl overflow-hidden">
            <div class="bg-purple-600 text-white p-6">
                <h1 class="text-3xl font-bold">Welcome to Your Dashboard</h1>
                <p class="mt-2 text-purple-100">Please select or continue to your designated role</p>
            </div>

            
            
            <div class="p-8">
                <div class="grid md:grid-cols-3 gap-6">
                    <!-- Admin Card -->
                    <div class="bg-gray-50 border-2 border-purple-200 rounded-lg p-6 text-center hover:shadow-lg transition-all">
                        <i class="fas fa-user-shield text-6xl text-purple-600 mb-4"></i>
                        <h2 class="text-xl font-semibold mb-2">Admin</h2>
                        <p class="text-gray-600 mb-4">Manage system-wide settings and users</p>
                        <a href="admin_login.php" class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-700 transition-colors">
                            Enter Admin Panel
                        </a>
                    </div>

                    <!-- School Card -->
                    <div class="bg-gray-50 border-2 border-green-200 rounded-lg p-6 text-center hover:shadow-lg transition-all">
                        <i class="fas fa-school text-6xl text-green-600 mb-4"></i>
                        <h2 class="text-xl font-semibold mb-2">School</h2>
                        <p class="text-gray-600 mb-4">Manage school-related operations</p>
                        <a href="school_login.php" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition-colors">
                            Enter School Dashboard
                        </a>
                    </div>

                    <!-- Institute Card -->
                    <div class="bg-gray-50 border-2 border-blue-200 rounded-lg p-6 text-center hover:shadow-lg transition-all">
                        <i class="fas fa-university text-6xl text-blue-600 mb-4"></i>
                        <h2 class="text-xl font-semibold mb-2">Institute</h2>
                        <p class="text-gray-600 mb-4">Manage institute-level activities</p>
                        <a href="ins_login.php" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition-colors">
                            Enter Institute Portal
                        </a>
                    </div>
                </div>

                <div class="mt-8 text-center bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-yellow-700 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>Important Information
                    </h3>
                    <ul class="text-yellow-600 space-y-2">
                        <li>• Ensure you have proper authorization before accessing any dashboard</li>
                        <li>• Contact system administrator if you cannot access your designated role</li>
                        <li>• Maintain confidentiality of your login credentials</li>
                    </ul>
                </div>
            </div>

            <div class="bg-gray-100 p-4 text-center">
                <p class="text-gray-600">
                    © 2024 Multi-Role Management System. All Rights Reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>