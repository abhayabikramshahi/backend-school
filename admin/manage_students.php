<?php
/**
 * Manage Students
 * 
 * This file allows administrators to manage student records.
 */

// Include authentication functions
require_once __DIR__ . '/../auth/auth_functions.php';

// Initialize Auth class
$auth = new Auth();

// Check if user is logged in and has admin role
if (!$auth->isLoggedIn() || !$auth->hasRole('admin')) {
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

// Include database connection
require_once __DIR__ . '/../includes/Database.php';
$db = Database::getInstance();
$conn = $db->getConnection();

// Handle student actions (add, edit, delete)
$message = '';
$error = '';
$edit_student = null;

// Delete student
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $student_id = $_GET['delete'];
    
    // Check if student exists
    $check_query = "SELECT id FROM users WHERE id = ? AND role = 'student'";
    $student = $db->getRecord($check_query, [$student_id]);
    
    if ($student) {
        // Delete student records
        $delete_query = "DELETE FROM users WHERE id = ?";
        $db->executeQuery($delete_query, [$student_id]);
        
        // Delete related records
        $db->executeQuery("DELETE FROM student_details WHERE user_id = ?", [$student_id]);
        $db->executeQuery("DELETE FROM class_students WHERE student_id = ?", [$student_id]);
        
        $message = 'Student has been deleted successfully.';
    } else {
        $error = 'Student not found.';
    }
}

// Edit student - load data
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $student_id = $_GET['edit'];
    
    $edit_query = "SELECT u.*, sd.parent_name, sd.parent_contact 
                 FROM users u 
                 LEFT JOIN student_details sd ON u.id = sd.user_id 
                 WHERE u.id = ? AND u.role = 'student'";
    $edit_student = $db->getRecord($edit_query, [$student_id]);
    
    if (!$edit_student) {
        $error = 'Student not found.';
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_student']) || isset($_POST['update_student'])) {
        $name = $_POST['name'] ?? '';
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $class = $_POST['class'] ?? '';
        $section = $_POST['section'] ?? '';
        $roll_number = $_POST['roll_number'] ?? '';
        $parent_name = $_POST['parent_name'] ?? '';
        $parent_contact = $_POST['parent_contact'] ?? '';
        
        if (empty($name) || empty($username) || empty($class) || empty($section) || empty($roll_number)) {
            $error = 'Please fill in all required fields.';
        } else {
            // Check if username already exists (for new students)
            if (isset($_POST['add_student'])) {
                $check_query = "SELECT id FROM users WHERE username = ?";
                $existing_user = $db->getRecord($check_query, [$username]);
                
                if ($existing_user) {
                    $error = 'Username already exists. Please choose a different username.';
                } else {
                    // Generate a default password (can be changed later)
                    $default_password = 'student123';
                    $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);
                    
                    // Insert new student
                    $insert_query = "INSERT INTO users (name, username, password, email, contact_number, address, role, class, section, roll_number) 
                                   VALUES (?, ?, ?, ?, ?, ?, 'student', ?, ?, ?)";
                    $db->executeQuery($insert_query, [
                        $name, $username, $hashed_password, $email, $phone, $address, $class, $section, $roll_number
                    ]);
                    
                    $new_student_id = $conn->lastInsertId();
                    
                    // Insert student details
                    $insert_details = "INSERT INTO student_details (user_id, parent_name, parent_contact) VALUES (?, ?, ?)";
                    $db->executeQuery($insert_details, [$new_student_id, $parent_name, $parent_contact]);
                    
                    $message = 'Student has been added successfully. Default password is: ' . $default_password;
                }
            } else if (isset($_POST['update_student']) && isset($_POST['student_id'])) {
                $student_id = $_POST['student_id'];
                
                // Update student
                $update_query = "UPDATE users SET name = ?, email = ?, contact_number = ?, address = ?, class = ?, section = ?, roll_number = ? WHERE id = ?";
                $db->executeQuery($update_query, [
                    $name, $email, $phone, $address, $class, $section, $roll_number, $student_id
                ]);
                
                // Update student details
                $check_details = "SELECT id FROM student_details WHERE user_id = ?";
                $existing_details = $db->getRecord($check_details, [$student_id]);
                
                if ($existing_details) {
                    $update_details = "UPDATE student_details SET parent_name = ?, parent_contact = ? WHERE user_id = ?";
                    $db->executeQuery($update_details, [$parent_name, $parent_contact, $student_id]);
                } else {
                    $insert_details = "INSERT INTO student_details (user_id, parent_name, parent_contact) VALUES (?, ?, ?)";
                    $db->executeQuery($insert_details, [$student_id, $parent_name, $parent_contact]);
                }
                
                // Reset password if requested
                if (isset($_POST['reset_password']) && $_POST['reset_password'] === 'yes') {
                    $default_password = 'student123';
                    $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);
                    
                    $update_password = "UPDATE users SET password = ? WHERE id = ?";
                    $db->executeQuery($update_password, [$hashed_password, $student_id]);
                    
                    $message = 'Student has been updated successfully. Password has been reset to: ' . $default_password;
                } else {
                    $message = 'Student has been updated successfully.';
                }
                
                // Clear edit mode
                $edit_student = null;
            }
        }
    }
}

// Get all students
$students_query = "SELECT u.id, u.name, u.username, u.email, u.contact_number, u.class, u.section, u.roll_number 
                FROM users u 
                WHERE u.role = 'student' 
                ORDER BY u.class, u.section, u.roll_number";
$students = $db->getRecords($students_query);

// Get available classes
$classes_query = "SELECT DISTINCT class_name FROM classes ORDER BY class_name";
$classes = $db->getRecords($classes_query);

// Get available sections
$sections_query = "SELECT DISTINCT section FROM classes ORDER BY section";
$sections = $db->getRecords($sections_query);

// Set page variables for header
$page_title = 'Manage Students';
$base_path = '..';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - School Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/school-theme.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                        <li><a href="index.php" class="text-black hover:text-gray-600 font-medium transition duration-300">Dashboard</a></li>
                        <li><a href="../auth/logout.php" class="text-black hover:text-gray-600 font-medium transition duration-300">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-black">Manage Students</h1>
            <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-black py-2 px-4 rounded transition">Back to Dashboard</a>
        </div>
        
        <?php if (!empty($message)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
        </div>
        <?php endif; ?>
        
        <!-- Student Form -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="bg-blue-600 text-white p-4">
                <h2 class="text-xl font-bold"><?php echo $edit_student ? 'Edit Student' : 'Add New Student'; ?></h2>
            </div>
            <div class="p-6">
                <form method="post" action="" class="space-y-4">
                    <?php if ($edit_student): ?>
                        <input type="hidden" name="student_id" value="<?php echo $edit_student['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Personal Information -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name*</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($edit_student['name'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                        </div>
                        
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username*</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($edit_student['username'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                   <?php echo $edit_student ? 'readonly' : 'required'; ?>>
                            <?php if ($edit_student): ?>
                                <p class="text-sm text-gray-500 mt-1">Username cannot be changed</p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($edit_student['email'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($edit_student['contact_number'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                        
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($edit_student['address'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                        
                        <!-- Academic Information -->
                        <div>
                            <label for="class" class="block text-sm font-medium text-gray-700 mb-1">Class*</label>
                            <select id="class" name="class" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                <option value="">Select Class</option>
                                <?php foreach ($classes as $class_item): ?>
                                    <option value="<?php echo htmlspecialchars($class_item['class_name']); ?>" 
                                            <?php echo (isset($edit_student['class']) && $edit_student['class'] == $class_item['class_name']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($class_item['class_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="section" class="block text-sm font-medium text-gray-700 mb-1">Section*</label>
                            <select id="section" name="section" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                <option value="">Select Section</option>
                                <?php foreach ($sections as $section_item): ?>
                                    <option value="<?php echo htmlspecialchars($section_item['section']); ?>" 
                                            <?php echo (isset($edit_student['section']) && $edit_student['section'] == $section_item['section']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($section_item['section']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="roll_number" class="block text-sm font-medium text-gray-700 mb-1">Roll Number*</label>
                            <input type="text" id="roll_number" name="roll_number" value="<?php echo htmlspecialchars($edit_student['roll_number'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                        </div>
                        
                        <!-- Parent Information -->
                        <div>
                            <label for="parent_name" class="block text-sm font-medium text-gray-700 mb-1">Parent/Guardian Name</label>
                            <input type="text" id="parent_name" name="parent_name" value="<?php echo htmlspecialchars($edit_student['parent_name'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                        
                        <div>
                            <label for="parent_contact" class="block text-sm font-medium text-gray-700 mb-1">Parent/Guardian Contact</label>
                            <input type="text" id="parent_contact" name="parent_contact" value="<?php echo htmlspecialchars($edit_student['parent_contact'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                    </div>
                    
                    <?php if ($edit_student): ?>
                    <div class="mt-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="reset_password" value="yes" class="form-checkbox h-4 w-4 text-blue-600">
                            <span class="ml-2 text-gray-700">Reset password to default</span>
                        </label>
                    </div>
                    <?php endif; ?>
                    
                    <div class="flex justify-end space-x-4">
                        <?php if ($edit_student): ?>
                            <a href="manage_students.php" class="bg-gray-200 hover:bg-gray-300 text-black py-2 px-4 rounded transition">Cancel</a>
                            <button type="submit" name="update_student" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-md transition">Update Student</button>
                        <?php else: ?>
                            <button type="submit" name="add_student" class="bg-green-600 hover:bg-green-700 text-white py-2 px-6 rounded-md transition">Add Student</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Students List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-600 text-white p-4">
                <h2 class="text-xl font-bold">Students List</h2>
            </div>
            <div class="p-6">
                <?php if (empty($students)): ?>
                    <div class="text-center py-4">
                        <p class="text-gray-600">No students found.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-100 text-black">
                                    <th class="py-3 px-4 text-left">Name</th>
                                    <th class="py-3 px-4 text-left">Username</th>
                                    <th class="py-3 px-4 text-left">Class</th>
                                    <th class="py-3 px-4 text-left">Section</th>
                                    <th class="py-3 px-4 text-left">Roll No</th>
                                    <th class="py-3 px-4 text-left">Contact</th>
                                    <th class="py-3 px-4 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($student['name']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($student['username']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($student['class']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($student['section']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($student['contact_number']); ?></td>
                                        <td class="py-3 px-4">
                                            <a href="manage_students.php?edit=<?php echo $student['id']; ?>" class="text-blue-600 hover:text-blue-800 mr-2">Edit</a>
                                            <a href="manage_students.php?delete=<?php echo $student['id']; ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
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

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-xl font-bold">School Management System</h3>
                    <p class="text-gray-400">Admin Portal</p>
                </div>
                <div>
                    <p>&copy; <?php echo date('Y'); ?> All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>