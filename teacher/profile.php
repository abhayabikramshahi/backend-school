<?php
/**
 * Teacher Profile
 * 
 * This file allows teachers to view and update their profile information.
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

// Handle form submission for profile update
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $qualification = $_POST['qualification'] ?? '';
    $specialization = $_POST['specialization'] ?? '';
    
    // Update user profile
    $update_query = "UPDATE users SET name = ?, email = ?, contact_number = ?, address = ? WHERE id = ?";
    $db->executeQuery($update_query, [$name, $email, $phone, $address, $teacher_id]);
    
    // Update teacher-specific information
    $check_query = "SELECT id FROM teacher_details WHERE user_id = ?";
    $teacher_details = $db->getRecord($check_query, [$teacher_id]);
    
    if ($teacher_details) {
        // Update existing record
        $update_details_query = "UPDATE teacher_details SET qualification = ?, specialization = ? WHERE user_id = ?";
        $db->executeQuery($update_details_query, [$qualification, $specialization, $teacher_id]);
    } else {
        // Insert new record
        $insert_details_query = "INSERT INTO teacher_details (user_id, qualification, specialization) VALUES (?, ?, ?)";
        $db->executeQuery($insert_details_query, [$teacher_id, $qualification, $specialization]);
    }
    
    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../uploads/profiles/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = $teacher_id . '_' . time() . '_' . basename($_FILES['profile_picture']['name']);
        $upload_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_file)) {
            // Update profile picture in database
            $update_picture_query = "UPDATE users SET profile_picture = ? WHERE id = ?";
            $db->executeQuery($update_picture_query, [$file_name, $teacher_id]);
        }
    }
    
    $message = 'Profile updated successfully.';
    
    // Refresh user data
    $user = $auth->getCurrentUser();
}

// Get teacher details
$teacher_details_query = "SELECT td.qualification, td.specialization, td.joining_date 
                        FROM teacher_details td 
                        WHERE td.user_id = ?";
$teacher_details = $db->getRecord($teacher_details_query, [$teacher_id]) ?? [];

// Set page variables for header
$page_title = 'My Profile';
$base_path = '..';

// Include header
include_once '../includes/header.php';
?>

<!-- Main Content -->
<main class="flex-grow container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">My Profile</h2>
        <p class="text-gray-600">View and update your profile information.</p>
    </div>
    
    <?php if (!empty($message)): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
        <p><?php echo $message; ?></p>
    </div>
    <?php endif; ?>
    
    <!-- Profile Form -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="p-6 space-y-6">
                <div class="flex flex-col md:flex-row">
                    <!-- Profile Picture -->
                    <div class="md:w-1/3 mb-6 md:mb-0 md:pr-6">
                        <div class="text-center">
                            <div class="mb-4">
                                <?php if (!empty($user['profile_picture'])): ?>
                                    <img src="<?php echo $base_path; ?>/uploads/profiles/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="w-40 h-40 rounded-full mx-auto object-cover border-4 border-gray-200">
                                <?php else: ?>
                                    <div class="w-40 h-40 rounded-full mx-auto bg-gray-300 flex items-center justify-center text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-1">Profile Picture</label>
                            <input type="file" id="profile_picture" name="profile_picture" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Account Information</h3>
                            <p class="text-sm text-gray-600 mb-1"><span class="font-medium">Username:</span> <?php echo htmlspecialchars($user['username']); ?></p>
                            <p class="text-sm text-gray-600 mb-1"><span class="font-medium">Role:</span> Teacher</p>
                            <p class="text-sm text-gray-600"><span class="font-medium">Joined:</span> <?php echo isset($teacher_details['joining_date']) ? date('F j, Y', strtotime($teacher_details['joining_date'])) : 'Not available'; ?></p>
                        </div>
                    </div>
                    
                    <!-- Personal Information -->
                    <div class="md:w-2/3">
                        <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Personal Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <!-- Full Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['contact_number'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <!-- Address -->
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mt-8">Professional Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <!-- Qualification -->
                            <div>
                                <label for="qualification" class="block text-sm font-medium text-gray-700 mb-1">Qualification</label>
                                <input type="text" id="qualification" name="qualification" value="<?php echo htmlspecialchars($teacher_details['qualification'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <!-- Specialization -->
                            <div>
                                <label for="specialization" class="block text-sm font-medium text-gray-700 mb-1">Specialization</label>
                                <input type="text" id="specialization" name="specialization" value="<?php echo htmlspecialchars($teacher_details['specialization'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 text-right">
                <button type="submit" name="update_profile" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</main>

<?php include_once '../includes/footer.php'; ?>