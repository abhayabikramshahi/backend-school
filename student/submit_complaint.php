<?php
/**
 * Submit Complaint (Gunaso)
 * 
 * This file allows students to submit complaints or feedback
 * that will be reviewed by administrators.
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

// Initialize Database
$db = Database::getInstance();

// Initialize variables
$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    
    // Validate input
    if (empty($title)) {
        $error_message = 'Please enter a title for your complaint.';
    } elseif (empty($description)) {
        $error_message = 'Please provide details about your complaint.';
    } else {
        // Insert complaint into database
        $complaintData = [
            'user_id' => $_SESSION['user_id'],
            'title' => $title,
            'description' => $description,
            'is_anonymous' => $is_anonymous
        ];
        
        $result = $db->insert('complaints', $complaintData);
        
        if ($result) {
            $success_message = 'Your complaint has been submitted successfully.';
            // Reset form fields
            $title = '';
            $description = '';
            $is_anonymous = 0;
        } else {
            $error_message = 'Failed to submit your complaint. Please try again.';
        }
    }
}

// Set page variables for header
$page_title = 'Submit Complaint';
$base_path = '..';

// Include header
include_once '../includes/header.php';
?>

<!-- Main Content -->
<main class="flex-grow container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-blue-600 text-white p-4">
            <h2 class="text-xl font-bold">Submit Complaint (Gunaso)</h2>
            <p class="text-sm opacity-90">Use this form to submit your complaints, suggestions, or feedback</p>
        </div>
        
        <div class="p-6">
            <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p><?php echo htmlspecialchars($success_message); ?></p>
                </div>
            <?php endif; ?>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="space-y-4">
                <div class="form-group">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title ?? ''); ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="5" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                              required><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                </div>
                
                <div class="form-group flex items-center">
                    <input type="checkbox" id="is_anonymous" name="is_anonymous" value="1" 
                           <?php echo isset($is_anonymous) && $is_anonymous ? 'checked' : ''; ?> 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_anonymous" class="ml-2 block text-sm text-gray-700">Submit anonymously</label>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                        Submit Complaint
                    </button>
                </div>
            </form>
            
            <div class="mt-4 text-center">
                <a href="view_complaints.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View My Previous Complaints
                </a>
            </div>
        </div>
    </div>
</main>

<?php include_once '../includes/footer.php'; ?>