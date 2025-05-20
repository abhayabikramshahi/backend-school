<?php
/**
 * Manage Notices
 * 
 * This file allows administrators to manage school notices and announcements.
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

// Handle notice actions (add, edit, delete)
$message = '';
$error = '';
$edit_notice = null;

// Delete notice
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $notice_id = $_GET['delete'];
    
    // Check if notice exists
    $check_query = "SELECT id FROM notices WHERE id = ?";
    $notice = $db->getRecord($check_query, [$notice_id]);
    
    if ($notice) {
        // Delete notice
        $delete_query = "DELETE FROM notices WHERE id = ?";
        $db->executeQuery($delete_query, [$notice_id]);
        
        $message = 'Notice has been deleted successfully.';
    } else {
        $error = 'Notice not found.';
    }
}

// Edit notice - load data
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $notice_id = $_GET['edit'];
    
    $edit_query = "SELECT * FROM notices WHERE id = ?";
    $edit_notice = $db->getRecord($edit_query, [$notice_id]);
    
    if (!$edit_notice) {
        $error = 'Notice not found.';
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_notice']) || isset($_POST['update_notice'])) {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $category = $_POST['category'] ?? '';
        
        if (empty($title) || empty($content)) {
            $error = 'Please fill in all required fields.';
        } else {
            if (isset($_POST['add_notice'])) {
                // Insert new notice
                $insert_query = "INSERT INTO notices (title, content, category, date_posted, posted_by) VALUES (?, ?, ?, NOW(), ?)";
                $db->executeQuery($insert_query, [$title, $content, $category, $user['id']]);
                
                $message = 'Notice has been added successfully.';
            } else if (isset($_POST['update_notice']) && isset($_POST['notice_id'])) {
                $notice_id = $_POST['notice_id'];
                
                // Update notice
                $update_query = "UPDATE notices SET title = ?, content = ?, category = ? WHERE id = ?";
                $db->executeQuery($update_query, [$title, $content, $category, $notice_id]);
                
                $message = 'Notice has been updated successfully.';
                
                // Clear edit mode
                $edit_notice = null;
            }
        }
    }
}

// Get all notices
$notices_query = "SELECT n.id, n.title, n.content, n.date_posted, n.category, u.name as posted_by 
               FROM notices n 
               LEFT JOIN users u ON n.posted_by = u.id 
               ORDER BY n.date_posted DESC";
$notices = $db->getRecords($notices_query);

// Set page variables for header
$page_title = 'Manage Notices';
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
            <h1 class="text-3xl font-bold text-black">Manage Notices</h1>
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
        
        <!-- Notice Form -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="bg-yellow-600 text-white p-4">
                <h2 class="text-xl font-bold"><?php echo $edit_notice ? 'Edit Notice' : 'Add New Notice'; ?></h2>
            </div>
            <div class="p-6">
                <form method="post" action="" class="space-y-4">
                    <?php if ($edit_notice): ?>
                        <input type="hidden" name="notice_id" value="<?php echo $edit_notice['id']; ?>">
                    <?php endif; ?>
                    
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title*</label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($edit_notice['title'] ?? ''); ?>" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-500 focus:ring-opacity-50" required>
                    </div>
                    
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select id="category" name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-500 focus:ring-opacity-50">
                            <option value="General" <?php echo (isset($edit_notice['category']) && $edit_notice['category'] == 'General') ? 'selected' : ''; ?>>General</option>
                            <option value="Academic" <?php echo (isset($edit_notice['category']) && $edit_notice['category'] == 'Academic') ? 'selected' : ''; ?>>Academic</option>
                            <option value="Examination" <?php echo (isset($edit_notice['category']) && $edit_notice['category'] == 'Examination') ? 'selected' : ''; ?>>Examination</option>
                            <option value="Event" <?php echo (isset($edit_notice['category']) && $edit_notice['category'] == 'Event') ? 'selected' : ''; ?>>Event</option>
                            <option value="Holiday" <?php echo (isset($edit_notice['category']) && $edit_notice['category'] == 'Holiday') ? 'selected' : ''; ?>>Holiday</option>
                            <option value="Important" <?php echo (isset($edit_notice['category']) && $edit_notice['category'] == 'Important') ? 'selected' : ''; ?>>Important</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content*</label>
                        <textarea id="content" name="content" rows="6" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-500 focus:ring-opacity-50" required><?php echo htmlspecialchars($edit_notice['content'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-4">
                        <?php if ($edit_notice): ?>
                            <a href="manage_notices.php" class="bg-gray-200 hover:bg-gray-300 text-black py-2 px-4 rounded transition">Cancel</a>
                            <button type="submit" name="update_notice" class="bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-6 rounded-md transition">Update Notice</button>
                        <?php else: ?>
                            <button type="submit" name="add_notice" class="bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-6 rounded-md transition">Add Notice</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Notices List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-yellow-600 text-white p-4">
                <h2 class="text-xl font-bold">Notices List</h2>
            </div>
            <div class="p-6">
                <?php if (empty($notices)): ?>
                    <div class="text-center py-4">
                        <p class="text-gray-600">No notices found.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-100 text-black">
                                    <th class="py-3 px-4 text-left">Title</th>
                                    <th class="py-3 px-4 text-left">Category</th>
                                    <th class="py-3 px-4 text-left">Date Posted</th>
                                    <th class="py-3 px-4 text-left">Posted By</th>
                                    <th class="py-3 px-4 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($notices as $notice): ?>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($notice['title']); ?></td>
                                        <td class="py-3 px-4">
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                                <?php 
                                                    switch($notice['category']) {
                                                        case 'Academic': echo 'bg-blue-100 text-blue-800'; break;
                                                        case 'Examination': echo 'bg-purple-100 text-purple-800'; break;
                                                        case 'Event': echo 'bg-green-100 text-green-800'; break;
                                                        case 'Holiday': echo 'bg-red-100 text-red-800'; break;
                                                        case 'Important': echo 'bg-yellow-100 text-yellow-800'; break;
                                                        default: echo 'bg-gray-100 text-gray-800';
                                                    }
                                                ?>"
                                            >
                                                <?php echo htmlspecialchars($notice['category']); ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-4"><?php echo date('d M Y', strtotime($notice['date_posted'])); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($notice['posted_by']); ?></td>
                                        <td class="py-3 px-4">
                                            <a href="manage_notices.php?edit=<?php echo $notice['id']; ?>" class="text-blue-600 hover:text-blue-800 mr-2">Edit</a>
                                            <a href="manage_notices.php?delete=<?php echo $notice['id']; ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this notice?')">Delete</a>
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