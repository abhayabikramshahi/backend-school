<?php
/**
 * Main Entry Point
 * 
 * This file serves as the main entry point to the School Management System.
 * It checks for authentication and redirects to appropriate pages.
 */

// Load environment variables
require_once __DIR__ . '/env_loader.php';

// Include authentication functions
require_once __DIR__ . '/auth/auth_functions.php';

// Initialize Auth class
$auth = new Auth();

// Initialize database connection
// Use bootstrap to load the appropriate database configuration
require_once __DIR__ . '/config/bootstrap.php';
$db = Database::getInstance();

// Get counts for dashboard
try {
    $students = $db->getStudents();
    $teachers = $db->getTeachers();
    $results = $db->getResults();
    $classes = $db->getClasses();
    
    $studentCount = is_array($students) ? count($students) : 0;
    $teacherCount = is_array($teachers) ? count($teachers) : 0;
    $resultCount = is_array($results) ? count($results) : 0;
    $classCount = is_array($classes) ? count($classes) : 0;
} catch (Exception $e) {
    // Log error and set default values
    error_log('Error getting dashboard counts: ' . $e->getMessage());
    $studentCount = $teacherCount = $resultCount = $classCount = 0;
}

// Check if user is logged in
if ($auth->isLoggedIn()) {
    // Check session timeout
    if (!$auth->checkSessionTimeout()) {
        header('Location: auth/login.php');
        exit;
    }
    
    // Get current user
    $user = $auth->getCurrentUser();
    
    // Redirect based on role if accessing the root
    if (basename($_SERVER['PHP_SELF']) === 'index.php') {
        switch ($user['role']) {
            case 'admin':
                header('Location: admin/index.php');
                break;
            case 'teacher':
                header('Location: teacher/index.php');
                break;
            case 'student':
                header('Location: student/index.php');
                break;
            default:
                // Stay on this page
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/modern-theme.css" rel="stylesheet">
</head>
<body class="bg-white">
    <?php include 'navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-black mb-4">Welcome to School Management System</h1>
            <p class="text-gray-600">Manage your school operations efficiently</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <!-- Students Card -->
            <div class="card transform hover:scale-105 transition-transform duration-300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-user-graduate text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Total Students</h2>
                            <p class="text-2xl font-bold text-black"><?php echo $studentCount; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teachers Card -->
            <div class="card transform hover:scale-105 transition-transform duration-300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-chalkboard-teacher text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Total Teachers</h2>
                            <p class="text-2xl font-bold text-black"><?php echo $teacherCount; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classes Card -->
            <div class="card transform hover:scale-105 transition-transform duration-300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-school text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Total Classes</h2>
                            <p class="text-2xl font-bold text-black"><?php echo $classCount; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Card -->
            <div class="card transform hover:scale-105 transition-transform duration-300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-chart-bar text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Total Results</h2>
                            <p class="text-2xl font-bold text-black"><?php echo $resultCount; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'teacher'])): ?>
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-black mb-4">Student Management</h3>
                    <div class="space-y-3">
                        <a href="manage_students.php" class="btn w-full text-center">
                            <i class="fas fa-users mr-2"></i> View All Students
                        </a>
                        <a href="add_student.php" class="btn btn-secondary w-full text-center">
                            <i class="fas fa-user-plus mr-2"></i> Add New Student
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-black mb-4">Teacher Management</h3>
                    <div class="space-y-3">
                        <a href="manage_teachers.php" class="btn w-full text-center">
                            <i class="fas fa-chalkboard-teacher mr-2"></i> View All Teachers
                        </a>
                        <a href="add_teacher.php" class="btn btn-secondary w-full text-center">
                            <i class="fas fa-user-plus mr-2"></i> Add New Teacher
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'teacher'])): ?>
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-black mb-4">Results Management</h3>
                    <div class="space-y-3">
                        <a href="manage_results.php" class="btn w-full text-center">
                            <i class="fas fa-chart-bar mr-2"></i> View All Results
                        </a>
                        <a href="add_result.php" class="btn btn-secondary w-full text-center">
                            <i class="fas fa-plus mr-2"></i> Add New Result
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Add animation classes to cards when they come into view
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            cards.forEach(card => observer.observe(card));
        });
    </script>
</body>
</html>


