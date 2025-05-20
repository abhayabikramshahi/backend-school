<?php
/**
 * Admin Dashboard
 * 
 * This file serves as the main dashboard for administrators.
 * It provides access to all administrative functions.
 */

// Include authentication functions
require_once __DIR__ . '/../auth/auth_functions.php';

// Initialize Auth class
$auth = new Auth();

// Check if user is logged in and has admin role
if (!$auth->isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

// Check if user has admin role
if (!$auth->hasRole('admin') && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    header('Location: ../login.php');
    exit;
}

// Check session timeout
if (!$auth->checkSessionTimeout()) {
    header('Location: ../auth/login.php');
    exit;
}

// Get current user
$user = $auth->getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Badimalika Secondary School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/school-theme.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../assets/images/favicon.ico" type="image/x-icon">
</head>
<body class="bg-white min-h-screen flex flex-col">
    <!-- Header/Navigation -->
    <header class="bg-white border-b-2 border-black shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-black">Admin Dashboard</h1>
                </div>
                <nav>
                    <ul class="flex space-x-6">
                        <li><a href="../index.php" class="text-black hover:text-gray-600 font-medium transition duration-300">Home</a></li>
                        <li><a href="../auth/logout.php" class="text-black hover:text-gray-600 font-medium transition duration-300">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-md p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
            <p class="text-gray-600">You are logged in as an administrator. From here, you can manage all aspects of the school system.</p>
        </div>
        
        <!-- Admin Functions Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- User Management -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-blue-600 text-white p-4">
                    <h3 class="text-xl font-bold">User Management</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">Manage user accounts and permissions</p>
                    <a href="manage_users.php" class="block w-full bg-blue-600 text-white text-center py-2 rounded-md hover:bg-blue-700 transition">Manage Users</a>
                </div>
            </div>
            
            <!-- Complaints Management -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-yellow-600 text-white p-4">
                    <h3 class="text-xl font-bold">Complaints (Gunaso Peti)</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">View and manage student complaints and feedback</p>
                    <a href="manage_complaints.php" class="block w-full bg-yellow-600 text-white text-center py-2 rounded-md hover:bg-yellow-700 transition">Manage Complaints</a>
                </div>
            </div>
            
            <!-- Results Management -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-purple-600 text-white p-4">
                    <h3 class="text-xl font-bold">Student Management</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">Add, edit, or remove student records</p>
                    <a href="manage_students.php" class="block w-full bg-purple-600 text-white text-center py-2 rounded-md hover:bg-purple-700 transition">Manage Students</a>
                </div>
            </div>
            
            <!-- Notice Management -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-yellow-600 text-white p-4">
                    <h3 class="text-xl font-bold">Notice Management</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">Publish and manage school notices</p>
                    <a href="manage_notices.php" class="block w-full bg-yellow-600 text-white text-center py-2 rounded-md hover:bg-yellow-700 transition">Manage Notices</a>
                </div>
            </div>
            
            <!-- Vacancy Management -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-red-600 text-white p-4">
                    <h3 class="text-xl font-bold">Vacancy Management</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">Post and manage job vacancies</p>
                    <a href="manage_vacancies.php" class="block w-full bg-red-600 text-white text-center py-2 rounded-md hover:bg-red-700 transition">Manage Vacancies</a>
                </div>
            </div>
            
            <!-- System Settings -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-700 text-white p-4">
                    <h3 class="text-xl font-bold">System Settings</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">Configure system settings and preferences</p>
                    <a href="system_settings.php" class="block w-full bg-gray-700 text-white text-center py-2 rounded-md hover:bg-gray-800 transition">System Settings</a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-xl font-bold">Badimalika Secondary School</h3>
                    <p class="text-gray-400">Admin Portal</p>
                </div>
                <div class="text-gray-400 text-sm">
                    &copy; <?php echo date('Y'); ?> Badimalika Secondary School. All rights reserved.
                </div>
            </div>
        </div>
    </footer>
</body>
</html>