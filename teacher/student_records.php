<?php
/**
 * Student Records Management
 * 
 * This file allows teachers to view and manage student records.
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

// Get classes taught by this teacher
$classes_query = "SELECT id, class_name, section FROM classes WHERE teacher_id = ?";
$classes = $db->getRecords($classes_query, [$teacher_id]);

// Get students from teacher's classes
$students = [];
if (!empty($classes)) {
    $class_ids = array_column($classes, 'id');
    $placeholders = implode(',', array_fill(0, count($class_ids), '?'));
    
    $students_query = "SELECT s.id, s.name, s.roll_number, s.class, s.section, s.contact_number, s.email 
                     FROM users s 
                     JOIN class_students cs ON s.id = cs.student_id 
                     WHERE cs.class_id IN ($placeholders) AND s.role = 'student' 
                     ORDER BY s.class, s.section, s.roll_number";
    $students = $db->getRecords($students_query, $class_ids);
}

// Set page variables for header
$page_title = 'Student Records';
$base_path = '..';

// Include header
include_once '../includes/header.php';
?>

<!-- Main Content -->
<main class="flex-grow container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-black">Student Records</h1>
        <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-black py-2 px-4 rounded transition">Back to Dashboard</a>
    </div>
    
    <!-- Filter Options -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="get" class="flex flex-wrap gap-4">
            <div class="w-full md:w-auto">
                <label for="class_filter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Class</label>
                <select id="class_filter" name="class_filter" class="w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <option value="">All Classes</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>">
                            <?php echo htmlspecialchars($class['class_name'] . ' - ' . $class['section']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="w-full md:w-auto flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded transition">Apply Filter</button>
            </div>
        </form>
    </div>
    
    <!-- Students List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <?php if (empty($students)): ?>
                <div class="text-center py-8">
                    <p class="text-gray-600 text-lg">No students found in your classes.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="bg-gray-100 text-black">
                                <th class="py-3 px-4 text-left">Name</th>
                                <th class="py-3 px-4 text-left">Roll Number</th>
                                <th class="py-3 px-4 text-left">Class</th>
                                <th class="py-3 px-4 text-left">Section</th>
                                <th class="py-3 px-4 text-left">Contact</th>
                                <th class="py-3 px-4 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($student['class']); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($student['section']); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($student['contact_number']); ?></td>
                                    <td class="py-3 px-4">
                                        <a href="student_details.php?id=<?php echo $student['id']; ?>" class="text-blue-600 hover:text-blue-800 mr-2">View Details</a>
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