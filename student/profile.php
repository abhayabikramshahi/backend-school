<?php
/**
 * Student Profile
 * 
 * This file allows students to view and update their profile information.
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
$student_id = $user['id'];

// Include database connection
require_once __DIR__ . '/../includes/Database.php';
$db = Database::getInstance();
$conn = $db->getConnection();

// Handle form submission for profile update
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $parent_name = $_POST['parent_name'] ?? '';
    $parent_contact = $_POST['parent_contact'] ?? '';
    
    // Update user profile
    $update_query = "UPDATE users SET name = ?, email = ?, contact_number = ?, address = ? WHERE id = ?";
    $db->executeQuery($update_query, [$name, $email, $phone, $address, $student_id]);
    
    // Update student-specific information
    $check_query = "SELECT id FROM student_details WHERE user_id = ?";
    $existing = $db->getRecord($check_query, [$student_id]);
    
    if ($existing) {
        $update_query = "UPDATE student_details SET parent_name = ?, parent_contact = ? WHERE user_id = ?";
        $db->executeQuery($update_query, [$parent_name, $parent_contact, $student_id]);
    } else {
        $insert_query = "INSERT INTO student_details (user_id, parent_name, parent_contact) VALUES (?, ?, ?)";
        $db->executeQuery($insert_query, [$student_id, $parent_name, $parent_contact]);
    }
    
    // Handle password change if provided
    if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            $hashed_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET password = ? WHERE id = ?";
            $db->executeQuery($update_query, [$hashed_password, $student_id]);
            $message = 'Profile and password updated successfully.';
        } else {
            $message = 'Profile updated but passwords did not match.';
        }
    } else {
        $message = 'Profile updated successfully.';
    }
    
    // Refresh user data
    $user = $auth->getCurrentUser();
}

// Get student details
$student_query = "SELECT u.*, sd.parent_name, sd.parent_contact 
               FROM users u 
               LEFT JOIN student_details sd ON u.id = sd.user_id 
               WHERE u.id = ?";
$student = $db->getRecord($student_query, [$student_id]);

// Set page variables for header
$page_title = 'My Profile';
$base_path = '..';

// Include header
include_once '../includes/header.php';
?>

<!-- Main Content -->
<main class="flex-grow container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-black">My Profile</h1>
        <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-black py-2 px-4 rounded transition">Back to Dashboard</a>
    </div>
    
    <?php if (!empty($message)): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
    </div>
    <?php endif; ?>
    
    <!-- Profile Information -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="bg-blue-600 text-white p-6">
            <div class="flex items-center">
                <div class="rounded-full bg-white text-blue-600 w-16 h-16 flex items-center justify-center text-2xl font-bold">
                    <?php echo strtoupper(substr($student['name'] ?? 'S', 0, 1)); ?>
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($student['name'] ?? ''); ?></h2>
                    <p class="text-blue-100">
                        Class: <?php echo htmlspecialchars($student['class'] ?? ''); ?> 
                        Section: <?php echo htmlspecialchars($student['section'] ?? ''); ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <form method="post" action="" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Personal Information</h3>
                        
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['name'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($student['contact_number'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                        
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea id="address" name="address" rows="3" 
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Academic Information -->
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Academic & Parent Information</h3>
                        
                        <div class="mb-4">
                            <label for="roll_number" class="block text-sm font-medium text-gray-700 mb-1">Roll Number</label>
                            <input type="text" id="roll_number" value="<?php echo htmlspecialchars($student['roll_number'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" readonly>
                        </div>
                        
                        <div class="mb-4">
                            <label for="class" class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                            <input type="text" id="class" value="<?php echo htmlspecialchars($student['class'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" readonly>
                        </div>
                        
                        <div class="mb-4">
                            <label for="parent_name" class="block text-sm font-medium text-gray-700 mb-1">Parent/Guardian Name</label>
                            <input type="text" id="parent_name" name="parent_name" value="<?php echo htmlspecialchars($student['parent_name'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                        
                        <div class="mb-4">
                            <label for="parent_contact" class="block text-sm font-medium text-gray-700 mb-1">Parent/Guardian Contact</label>
                            <input type="text" id="parent_contact" name="parent_contact" value="<?php echo htmlspecialchars($student['parent_contact'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                    </div>
                </div>
                
                <!-- Change Password -->
                <div class="border-t pt-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Change Password</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" id="new_password" name="new_password" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                        
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Leave blank if you don't want to change your password</p>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" name="update_profile" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-md transition">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>