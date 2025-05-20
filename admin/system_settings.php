<?php
/**
 * System Settings
 * 
 * This file allows administrators to manage system-wide settings.
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

// Handle settings update
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    // Get form data
    $school_name = $_POST['school_name'] ?? '';
    $school_address = $_POST['school_address'] ?? '';
    $school_phone = $_POST['school_phone'] ?? '';
    $school_email = $_POST['school_email'] ?? '';
    $school_website = $_POST['school_website'] ?? '';
    $session_year = $_POST['session_year'] ?? '';
    $system_theme = $_POST['system_theme'] ?? 'light';
    
    // Update settings in database
    $settings = [
        'school_name' => $school_name,
        'school_address' => $school_address,
        'school_phone' => $school_phone,
        'school_email' => $school_email,
        'school_website' => $school_website,
        'session_year' => $session_year,
        'system_theme' => $system_theme
    ];
    
    foreach ($settings as $key => $value) {
        // Check if setting exists
        $check_query = "SELECT id FROM system_settings WHERE setting_key = ?";
        $setting = $db->getRecord($check_query, [$key]);
        
        if ($setting) {
            // Update existing setting
            $update_query = "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?";
            $db->executeQuery($update_query, [$value, $key]);
        } else {
            // Insert new setting
            $insert_query = "INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?)";
            $db->executeQuery($insert_query, [$key, $value]);
        }
    }
    
    $message = 'System settings updated successfully.';
}

// Get current settings
$settings_query = "SELECT setting_key, setting_value FROM system_settings";
$settings_records = $db->getRecords($settings_query);

$settings = [];
foreach ($settings_records as $record) {
    $settings[$record['setting_key']] = $record['setting_value'];
}

// Set default values if not set
$settings['school_name'] = $settings['school_name'] ?? 'Badimalika Secondary School';
$settings['school_address'] = $settings['school_address'] ?? 'Badimalika, Nepal';
$settings['school_phone'] = $settings['school_phone'] ?? '+977-1234567890';
$settings['school_email'] = $settings['school_email'] ?? 'info@badimalika.edu.np';
$settings['school_website'] = $settings['school_website'] ?? 'www.badimalika.edu.np';
$settings['session_year'] = $settings['session_year'] ?? '2023-2024';
$settings['system_theme'] = $settings['system_theme'] ?? 'light';

// Set page variables for header
$page_title = 'System Settings';
$base_path = '..';

// Include header
include_once '../includes/header.php';
?>

<!-- Main Content -->
<main class="flex-grow container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">System Settings</h2>
        <p class="text-gray-600">Manage system-wide settings for the school management system.</p>
    </div>
    
    <?php if (!empty($message)): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
        <p><?php echo $message; ?></p>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p><?php echo $error; ?></p>
    </div>
    <?php endif; ?>
    
    <!-- Settings Form -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <form method="POST" action="">
            <div class="p-6 space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">School Information</h3>
                
                <!-- School Name -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="school_name" class="block text-sm font-medium text-gray-700 mb-1">School Name</label>
                        <input type="text" id="school_name" name="school_name" value="<?php echo htmlspecialchars($settings['school_name']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <!-- School Address -->
                    <div>
                        <label for="school_address" class="block text-sm font-medium text-gray-700 mb-1">School Address</label>
                        <input type="text" id="school_address" name="school_address" value="<?php echo htmlspecialchars($settings['school_address']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="school_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" id="school_phone" name="school_phone" value="<?php echo htmlspecialchars($settings['school_phone']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="school_email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="school_email" name="school_email" value="<?php echo htmlspecialchars($settings['school_email']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="school_website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                        <input type="url" id="school_website" name="school_website" value="<?php echo htmlspecialchars($settings['school_website']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mt-8">System Settings</h3>
                
                <!-- Academic Session -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="session_year" class="block text-sm font-medium text-gray-700 mb-1">Academic Session</label>
                        <input type="text" id="session_year" name="session_year" value="<?php echo htmlspecialchars($settings['session_year']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <!-- System Theme -->
                    <div>
                        <label for="system_theme" class="block text-sm font-medium text-gray-700 mb-1">System Theme</label>
                        <select id="system_theme" name="system_theme" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="light" <?php echo $settings['system_theme'] === 'light' ? 'selected' : ''; ?>>Light Theme</option>
                            <option value="dark" <?php echo $settings['system_theme'] === 'dark' ? 'selected' : ''; ?>>Dark Theme</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 text-right">
                <button type="submit" name="update_settings" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</main>

<?php include_once '../includes/footer.php'; ?>