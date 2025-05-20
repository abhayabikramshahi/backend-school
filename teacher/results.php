<?php
/**
 * Results Management
 * 
 * This file allows teachers to manage student examination results.
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

// Handle form submission for results
$message = '';
$selected_class = '';
$exam_name = '';
$exam_date = date('Y-m-d');
$students = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_results'])) {
    $selected_class = $_POST['class_id'] ?? '';
    $exam_name = $_POST['exam_name'] ?? '';
    $exam_date = $_POST['exam_date'] ?? date('Y-m-d');
    
    if (!empty($selected_class) && !empty($exam_name) && !empty($exam_date)) {
        // Process results data
        foreach ($_POST['marks'] as $student_id => $subjects) {
            foreach ($subjects as $subject => $marks) {
                $check_query = "SELECT id FROM results WHERE student_id = ? AND class = ? AND exam_name = ? AND subject = ?";
                $existing = $db->getRecord($check_query, [$student_id, $selected_class, $exam_name, $subject]);
                
                if ($existing) {
                    // Update existing record
                    $update_query = "UPDATE results SET marks = ?, max_marks = ?, exam_date = ?, updated_by = ? WHERE id = ?";
                    $db->executeQuery($update_query, [$marks, $_POST['max_marks'][$subject], $exam_date, $teacher_id, $existing['id']]);
                } else {
                    // Insert new record
                    $insert_query = "INSERT INTO results (student_id, class, exam_name, subject, marks, max_marks, exam_date, created_by) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $db->executeQuery($insert_query, [
                        $student_id, 
                        $selected_class, 
                        $exam_name, 
                        $subject, 
                        $marks, 
                        $_POST['max_marks'][$subject], 
                        $exam_date, 
                        $teacher_id
                    ]);
                }
            }
        }
        $message = 'Results have been saved successfully.';
    }
}

// Get students and subjects for selected class
if (!empty($_GET['class_id']) || !empty($selected_class)) {
    $class_id = $_GET['class_id'] ?? $selected_class;
    $selected_class = $class_id;
    
    // Get class details
    $class_query = "SELECT class_name, section FROM classes WHERE id = ?";
    $class_details = $db->getRecord($class_query, [$class_id]);
    
    // Get students in this class
    $students_query = "SELECT s.id, s.name, s.roll_number 
                     FROM users s 
                     JOIN class_students cs ON s.id = cs.student_id 
                     WHERE cs.class_id = ? AND s.role = 'student' 
                     ORDER BY s.roll_number";
    $students = $db->getRecords($students_query, [$class_id]);
    
    // Get subjects for this class
    $subjects_query = "SELECT DISTINCT subject FROM class_subjects WHERE class_id = ?";
    $subjects = $db->getRecords($subjects_query, [$class_id]);
    
    // If exam name and date are provided, get existing results
    if (!empty($_GET['exam_name']) && !empty($_GET['exam_date'])) {
        $exam_name = $_GET['exam_name'];
        $exam_date = $_GET['exam_date'];
        
        // Get existing results
        $results_query = "SELECT student_id, subject, marks FROM results 
                        WHERE class = ? AND exam_name = ? AND exam_date = ?";
        $existing_results = $db->getRecords($results_query, [$class_id, $exam_name, $exam_date]);
        
        // Organize results by student and subject
        $student_results = [];
        foreach ($existing_results as $result) {
            $student_results[$result['student_id']][$result['subject']] = $result['marks'];
        }
    }
}

// Set page variables for header
$page_title = 'Results Management';
$base_path = '..';

// Include header
include_once '../includes/header.php';
?>

<!-- Main Content -->
<main class="flex-grow container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-black">Results Management</h1>
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
            
            <?php if (!empty($selected_class)): ?>
            <div class="w-full md:w-auto">
                <label for="exam_name" class="block text-sm font-medium text-gray-700 mb-1">Exam Name</label>
                <input type="text" id="exam_name" name="exam_name" value="<?php echo htmlspecialchars($exam_name); ?>" 
                       class="w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
            </div>
            
            <div class="w-full md:w-auto">
                <label for="exam_date" class="block text-sm font-medium text-gray-700 mb-1">Exam Date</label>
                <input type="date" id="exam_date" name="exam_date" value="<?php echo htmlspecialchars($exam_date); ?>" 
                       class="w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
            </div>
            <?php endif; ?>
            
            <div class="w-full md:w-auto flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded transition">
                    <?php echo empty($selected_class) ? 'Load Class' : 'Load Results'; ?>
                </button>
            </div>
        </form>
    </div>
    
    <?php if (!empty($students) && !empty($subjects)): ?>
    <!-- Results Form -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form method="post" action="">
                <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($selected_class); ?>">
                <input type="hidden" name="exam_name" value="<?php echo htmlspecialchars($exam_name); ?>">
                <input type="hidden" name="exam_date" value="<?php echo htmlspecialchars($exam_date); ?>">
                
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-2">
                        <?php echo htmlspecialchars($class_details['class_name'] . ' - ' . $class_details['section']); ?>
                        <span class="ml-2 text-gray-600"><?php echo htmlspecialchars($exam_name); ?></span>
                    </h2>
                    <p class="text-gray-600">Exam Date: <?php echo date('d M Y', strtotime($exam_date)); ?></p>
                </div>
                
                <!-- Set Maximum Marks -->
                <div class="mb-6 p-4 bg-gray-50 rounded-md">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Maximum Marks</h3>
                    <div class="flex flex-wrap gap-4">
                        <?php foreach ($subjects as $subject): ?>
                            <div>
                                <label for="max_<?php echo htmlspecialchars($subject['subject']); ?>" class="block text-sm font-medium text-gray-700 mb-1">
                                    <?php echo htmlspecialchars($subject['subject']); ?>
                                </label>
                                <input type="number" id="max_<?php echo htmlspecialchars($subject['subject']); ?>" 
                                       name="max_marks[<?php echo htmlspecialchars($subject['subject']); ?>]" 
                                       value="100" min="0" max="100" 
                                       class="w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="bg-gray-100 text-black">
                                <th class="py-3 px-4 text-left">Roll No</th>
                                <th class="py-3 px-4 text-left">Name</th>
                                <?php foreach ($subjects as $subject): ?>
                                    <th class="py-3 px-4 text-center"><?php echo htmlspecialchars($subject['subject']); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($student['name']); ?></td>
                                    <?php foreach ($subjects as $subject): ?>
                                        <td class="py-3 px-4 text-center">
                                            <input type="number" 
                                                   name="marks[<?php echo $student['id']; ?>][<?php echo htmlspecialchars($subject['subject']); ?>]" 
                                                   value="<?php echo isset($student_results[$student['id']][$subject['subject']]) ? htmlspecialchars($student_results[$student['id']][$subject['subject']]) : ''; ?>" 
                                                   min="0" max="100" 
                                                   class="w-16 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6">
                    <button type="submit" name="save_results" class="bg-green-600 hover:bg-green-700 text-white py-2 px-6 rounded-md transition">Save Results</button>
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