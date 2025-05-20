<?php
/**
 * Attendance Management
 * 
 * This file allows teachers to manage student attendance records.
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

// Handle form submission for attendance
$message = '';
$selected_class = '';
$attendance_date = date('Y-m-d');
$students = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    $selected_class = $_POST['class_id'] ?? '';
    $attendance_date = $_POST['attendance_date'] ?? date('Y-m-d');
    
    if (!empty($selected_class) && !empty($attendance_date)) {
        // Process attendance data
        foreach ($_POST['attendance'] as $student_id => $status) {
            $check_query = "SELECT id FROM attendance WHERE student_id = ? AND class_id = ? AND attendance_date = ?";
            $existing = $db->getRecord($check_query, [$student_id, $selected_class, $attendance_date]);
            
            if ($existing) {
                // Update existing record
                $update_query = "UPDATE attendance SET status = ? WHERE student_id = ? AND class_id = ? AND attendance_date = ?";
                $db->executeQuery($update_query, [$status, $student_id, $selected_class, $attendance_date]);
            } else {
                // Insert new record
                $insert_query = "INSERT INTO attendance (student_id, class_id, attendance_date, status, marked_by) VALUES (?, ?, ?, ?, ?)";
                $db->executeQuery($insert_query, [$student_id, $selected_class, $attendance_date, $status, $teacher_id]);
            }
        }
        $message = 'Attendance has been recorded successfully.';
    }
}

// Get students for selected class
if (!empty($_GET['class_id']) || !empty($selected_class)) {
    $class_id = $_GET['class_id'] ?? $selected_class;
    $selected_class = $class_id;
    
    $students_query = "SELECT s.id, s.name, s.roll_number, a.status 
                     FROM users s 
                     JOIN class_students cs ON s.id = cs.student_id 
                     LEFT JOIN attendance a ON s.id = a.student_id AND a.class_id = ? AND a.attendance_date = ? 
                     WHERE cs.class_id = ? AND s.role = 'student' 
                     ORDER BY s.roll_number";
    $students = $db->getRecords($students_query, [$class_id, $attendance_date, $class_id]);
}

// Set page variables for header
$page_title = 'Attendance Management';
$base_path = '..';

// Include header
include_once '../includes/header.php';
?>

<!-- Main Content -->
<main class="flex-grow container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-black">Attendance Management</h1>
        <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-black py-2 px-4 rounded transition">Back to Dashboard</a>
    </div>
    
    <?php if (!empty($message)): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
    </div>
    <?php endif; ?>
    
    <!-- Class Selection Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="get" class="flex flex-wrap gap-4">
            <div class="w-full md:w-auto">
                <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">Select Class</label>
                <select id="class_id" name="class_id" class="w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                    <option value="">-- Select Class --</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>" <?php echo ($selected_class == $class['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['class_name'] . ' - ' . $class['section']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="w-full md:w-auto flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded transition">Load Students</button>
            </div>
        </form>
    </div>
    
    <?php if (!empty($students)): ?>
    <!-- Attendance Form -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form method="post" action="">
                <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($selected_class); ?>">
                
                <div class="mb-6">
                    <label for="attendance_date" class="block text-sm font-medium text-gray-700 mb-1">Attendance Date</label>
                    <input type="date" id="attendance_date" name="attendance_date" value="<?php echo htmlspecialchars($attendance_date); ?>" 
                           class="w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="bg-gray-100 text-black">
                                <th class="py-3 px-4 text-left">Roll No</th>
                                <th class="py-3 px-4 text-left">Name</th>
                                <th class="py-3 px-4 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center space-x-4">
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="present" 
                                                       <?php echo ($student['status'] === 'present' || empty($student['status'])) ? 'checked' : ''; ?> 
                                                       class="form-radio h-4 w-4 text-green-600">
                                                <span class="ml-2 text-green-600">Present</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="absent" 
                                                       <?php echo ($student['status'] === 'absent') ? 'checked' : ''; ?> 
                                                       class="form-radio h-4 w-4 text-red-600">
                                                <span class="ml-2 text-red-600">Absent</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="late" 
                                                       <?php echo ($student['status'] === 'late') ? 'checked' : ''; ?> 
                                                       class="form-radio h-4 w-4 text-yellow-600">
                                                <span class="ml-2 text-yellow-600">Late</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6">
                    <button type="submit" name="mark_attendance" class="bg-green-600 hover:bg-green-700 text-white py-2 px-6 rounded-md transition">Save Attendance</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>