<?php
/**
 * Teacher Dashboard
 * 
 * This file serves as the main dashboard for teachers.
 * It provides access to teacher-specific functions.
 */

// Include authentication functions
require_once __DIR__ . '/../auth/auth_functions.php';

// Initialize Auth class
$auth = new Auth();

// Check if user is logged in and has teacher role
if (!$auth->isLoggedIn() || !$auth->hasRole('teacher')) {
    header('Location: ../auth/login.php');
    exit;
}

// Check session timeout
if (!$auth->checkSessionTimeout()) {
    header('Location: ../auth/login.php');
    exit;
}

// Get current user
$user = $auth->getCurrentUser();

// Set page variables for header
$page_title = 'Teacher Dashboard';
$base_path = '..';

// Include header
include_once '../includes/header.php';
?>

<!-- Main Content -->
<main class="flex-grow container mx-auto px-4 py-8">
    <!-- Welcome Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        <p class="text-gray-600">You are logged in as a teacher. From here, you can manage your classes and students.</p>
    </div>
    
    <!-- Teacher Functions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- My Classes -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-green-600 text-white p-4">
                <h3 class="text-xl font-bold">My Classes</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">View and manage your assigned classes</p>
                <a href="my_classes.php" class="block w-full bg-green-600 text-white text-center py-2 rounded-md hover:bg-green-700 transition">View Classes</a>
            </div>
        </div>
        
        <!-- Student Records -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-600 text-white p-4">
                <h3 class="text-xl font-bold">Student Records</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">View and manage student information</p>
                <a href="student_records.php" class="block w-full bg-blue-600 text-white text-center py-2 rounded-md hover:bg-blue-700 transition">View Students</a>
            </div>
        </div>
        
        <!-- Attendance -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-purple-600 text-white p-4">
                <h3 class="text-xl font-bold">Attendance</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Manage student attendance records</p>
                <a href="attendance.php" class="block w-full bg-purple-600 text-white text-center py-2 rounded-md hover:bg-purple-700 transition">Manage Attendance</a>
            </div>
        </div>
        
        <!-- Results -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-yellow-600 text-white p-4">
                <h3 class="text-xl font-bold">Results</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Manage student examination results</p>
                <a href="results.php" class="block w-full bg-yellow-600 text-white text-center py-2 rounded-md hover:bg-yellow-700 transition">Manage Results</a>
            </div>
        </div>
        
        <!-- Notices -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-red-600 text-white p-4">
                <h3 class="text-xl font-bold">Notices</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">View school notices and announcements</p>
                <a href="../view_notices.php" class="block w-full bg-red-600 text-white text-center py-2 rounded-md hover:bg-red-700 transition">View Notices</a>
            </div>
        </div>
        
        <!-- My Profile -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-700 text-white p-4">
                <h3 class="text-xl font-bold">My Profile</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">View and update your profile information</p>
                <a href="profile.php" class="block w-full bg-gray-700 text-white text-center py-2 rounded-md hover:bg-gray-800 transition">View Profile</a>
            </div>
        </div>
    </div>
</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>