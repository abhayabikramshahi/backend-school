<?php
/**
 * Teacher Classes Management
 * 
 * This file allows teachers to view and manage their assigned classes.
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
$teacher_id = $user['id'];

// Include database connection
require_once __DIR__ . '/../includes/Database.php';
$db = Database::getInstance();
$conn = $db->getConnection();

// Get teacher's assigned classes
$classes_query = "SELECT c.id, c.class_name, c.section, c.subject, c.schedule, COUNT(s.id) as student_count 
               FROM classes c 
               LEFT JOIN class_students s ON c.id = s.class_id 
               WHERE c.teacher_id = ? 
               GROUP BY c.id, c.class_name, c.section, c.subject, c.schedule";
$classes = $db->getRecords($classes_query, [$teacher_id]);

// Set page variables for header
$page_title = 'My Classes';
$base_path = '..';

// Include header
include_once '../includes/header.php';
?>

<!-- Main Content -->
<main class="flex-grow container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-black">My Classes</h1>
        <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-black py-2 px-4 rounded transition">Back to Dashboard</a>
    </div>
    
    <!-- Classes List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="p-6">
            <?php if (empty($classes)): ?>
                <div class="text-center py-8">
                    <p class="text-gray-600 text-lg">You don't have any assigned classes yet.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="bg-gray-100 text-black">
                                <th class="py-3 px-4 text-left">Class Name</th>
                                <th class="py-3 px-4 text-left">Section</th>
                                <th class="py-3 px-4 text-left">Subject</th>
                                <th class="py-3 px-4 text-left">Schedule</th>
                                <th class="py-3 px-4 text-left">Students</th>
                                <th class="py-3 px-4 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($classes as $class): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($class['class_name']); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($class['section']); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($class['subject']); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($class['schedule']); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($class['student_count']); ?></td>
                                    <td class="py-3 px-4">
                                        <a href="class_details.php?id=<?php echo $class['id']; ?>" class="text-blue-600 hover:text-blue-800 mr-2">View Details</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>