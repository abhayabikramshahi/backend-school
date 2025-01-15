<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="https://badimalikasecschool.netlify.app/471f74d9-7a7c-4024-82b7-251a5aba58a3.jpg" type="image/x-icon">
    <title>Admin - Badimalika Secondary School</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
<div id="main-content" class="container mx-auto mt-8 p-8 bg-white rounded-xl shadow-lg">
    <!-- Header Section -->
    <header class="text-center">
        <h2 class="text-3xl font-bold text-blue-600">Digital Portal</h2>
        <p class="text-gray-600 text-lg mt-2">Streamlining communication and access to resources for students, parents, and staff.</p>
    </header>

    <!-- Welcome Section -->
    <div class="text-center my-6">
        <?php if (isset($_SESSION['username'])): ?>
            <h3 class="text-2xl font-semibold text-gray-700">Welcome, 
                <span class="text-blue-600"><?php echo htmlspecialchars($_SESSION['username']); ?></span>!</h3>
            <p class="text-gray-500 mt-2">Access your dashboard below.</p>
        <?php else: ?>
            <h3 class="text-2xl font-semibold text-gray-700">Welcome, Guest!</h3>
            <p class="text-gray-500 mt-2">Please log in or register to access the portal.</p>
        <?php endif; ?>
    </div>

    <!-- Navigation Section -->
    <nav class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <?php if (isset($_SESSION['username'])): ?>
            <!-- Dashboard -->
            <a href="welcome.php" class="block p-6 rounded-lg shadow-md bg-gradient-to-br from-blue-50 to-blue-100 hover:shadow-xl transition">
                <div class="flex items-center space-x-4">
                    <div class="p-4 bg-blue-200 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3v7.5m4.5-7.5v7.5m-9 10.5h18m-6-10.5l3 3m0 0l3-3m-3 3V3" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-blue-600">Dashboard</h3>
                        <p class="text-gray-600">Manage your tasks and view personalized updates.</p>
                    </div>
                </div>
            </a>

            <!-- Notices -->
            <a href="view_notices.php" class="block p-6 rounded-lg shadow-md bg-gradient-to-br from-green-50 to-green-100 hover:shadow-xl transition">
                <div class="flex items-center space-x-4">
                    <div class="p-4 bg-green-200 rounded-full">
                        <svg class="w-8 h-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h5m1 4H7m5-14a2 2 0 100 4 2 2 0 000-4zM4 22h16a2 2 0 002-2V6a2 2 0 00-2-2h-4.68a2 2 0 01-1.316-.508L9.68 2.508A2 2 0 008.368 2H4a2 2 0 00-2 2v16a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-green-600">Notices</h3>
                        <p class="text-gray-600">Check the latest announcements.</p>
                    </div>
                </div>
            </a>

            <!-- Admin Options -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="edit_vacancy.php" class="block p-6 rounded-lg shadow-md bg-gradient-to-br from-purple-50 to-purple-100 hover:shadow-xl transition">
                    <h3 class="text-xl font-bold text-purple-600">Edit Vacancy</h3>
                </a>
                <a href="manage_notice.php" class="block p-6 rounded-lg shadow-md bg-gradient-to-br from-red-50 to-red-100 hover:shadow-xl transition">
                    <h3 class="text-xl font-bold text-red-600">Manage Notices</h3>
                </a>
                <a href="manage_teachers.php" class="block p-6 rounded-lg shadow-md bg-gradient-to-br from-yellow-50 to-yellow-100 hover:shadow-xl transition">
                    <h3 class="text-xl font-bold text-yellow-600">Manage Teachers</h3>
                </a>
                <a href="manage_vacancies.php" class="block p-6 rounded-lg shadow-md bg-gradient-to-br from-green-50 to-green-100 hover:shadow-xl transition">
                    <h3 class="text-xl font-bold text-green-600">Manage Vacancies</h3>
                </a>
                <a href="upload_notice.php" class="block p-6 rounded-lg shadow-md bg-gradient-to-br from-blue-50 to-blue-100 hover:shadow-xl transition">
                    <h3 class="text-xl font-bold text-blue-600">Upload Notice</h3>
                </a>
                <a href="upload_teachers.php" class="block p-6 rounded-lg shadow-md bg-gradient-to-br from-indigo-50 to-indigo-100 hover:shadow-xl transition">
                    <h3 class="text-xl font-bold text-indigo-600">Upload Teachers</h3>
                </a>
                <a href="upload_vacancy.php" class="block p-6 rounded-lg shadow-md bg-gradient-to-br from-pink-50 to-pink-100 hover:shadow-xl transition">
                    <h3 class="text-xl font-bold text-pink-600">Upload Vacancy</h3>
                </a>
                <a href="edit_student.php" class="block p-6 rounded-lg shadow-md bg-gradient-to-br from-teal-50 to-teal-100 hover:shadow-xl transition">
                    <h3 class="text-xl font-bold text-teal-600">Edit Student</h3>
                </a>
                <a href="delete_notice.php" class="block p-6 rounded-lg shadow-md bg-gradient-to-br from-gray-50 to-gray-100 hover:shadow-xl transition">
                    <h3 class="text-xl font-bold text-gray-600">Delete Notice</h3>
                </a>
                <a href="add_student.php" class="block p-6 rounded-lg shadow-md bg-gradient-to-br from-orange-50 to-orange-100 hover:shadow-xl transition">
                    <h3 class="text-xl font-bold text-orange-600">Add Student</h3>
                </a>
            <?php endif; ?>
        <?php else: ?>
            <!-- Login -->
            <a href="login.php" class="block p-6 rounded-lg shadow-md bg-gradient-to-br from-yellow-50 to-yellow-100 hover:shadow-xl transition">
                <div>
                    <h3 class="text-xl font-bold text-yellow-600">Login</h3>
                </div>
            </a>
        <?php endif; ?>
    </nav>
</div>
</body>
</html>
```