<?php
/**
 * Student Dashboard
 * 
 * This file serves as the main dashboard for students.
 * It provides access to student-specific functions.
 */

// Include authentication functions
require_once __DIR__ . '/../auth/auth_functions.php';

// Initialize Auth class
$auth = new Auth();

// Check if user is logged in and has student role
if (!$auth->isLoggedIn() || !$auth->hasRole('student')) {
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
$page_title = 'Student Dashboard';
$base_path = '..';

// Include header
include_once '../includes/header.php';
?>

<!-- Main Content -->
<main class="flex-grow container mx-auto px-4 py-8">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-md p-6 mb-8 text-white">
        <h2 class="text-3xl font-bold mb-3">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        <p class="text-white opacity-90">You are logged in as a student. From here, you can access your academic information and school resources.</p>
    </div>
    
    <!-- Student Functions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Complaints / Gunaso Peti -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-600 text-white p-4">
                <h3 class="text-lg font-semibold">Complaints / Gunaso Peti</h3>
                <p class="text-sm opacity-90">Submit and track your complaints</p>
            </div>
            <div class="p-4">
                <p class="text-gray-600 mb-4">Use our complaint system to submit feedback or report issues.</p>
                <div class="flex space-x-2">
                    <a href="submit_complaint.php" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded transition duration-150 ease-in-out">Submit Complaint</a>
                    <a href="view_complaints.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium py-2 px-4 rounded transition duration-150 ease-in-out">View Complaints</a>
                </div>
            </div>
        </div>
        
        <!-- My Profile -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-600 text-white p-4">
                <h3 class="text-xl font-bold">My Profile</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">View and update your personal information</p>
                <a href="profile.php" class="block w-full bg-blue-600 text-white text-center py-2 rounded-md hover:bg-blue-700 transition">View Profile</a>
            </div>
        </div>
        
        <!-- My Classes -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-green-600 text-white p-4">
                <h3 class="text-xl font-bold">My Classes</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">View your enrolled classes and subjects</p>
                <a href="my_classes.php" class="block w-full bg-green-600 text-white text-center py-2 rounded-md hover:bg-green-700 transition">View Classes</a>
            </div>
        </div>
        
        <!-- My Results -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden transform transition-all hover:scale-105 hover:shadow-lg">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white p-4">
                <h3 class="text-xl font-bold">My Results</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">View your examination results and academic performance</p>
                <a href="my_results.php" class="block w-full bg-purple-600 text-white text-center py-3 rounded-md hover:bg-purple-700 transition font-semibold">View Results</a>
            </div>
        </div>
        
        <!-- My Attendance -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-yellow-600 text-white p-4">
                <h3 class="text-xl font-bold">My Attendance</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">View your attendance records</p>
                <a href="my_attendance.php" class="block w-full bg-yellow-600 text-white text-center py-2 rounded-md hover:bg-yellow-700 transition">View Attendance</a>
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
        
        <!-- Events -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-indigo-600 text-white p-4">
                <h3 class="text-xl font-bold">Events</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">View upcoming school events</p>
                <a href="events.php" class="block w-full bg-indigo-600 text-white text-center py-2 rounded-md hover:bg-indigo-700 transition">View Events</a>
            </div>
        </div>
    </div>
</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>