<?php
/**
 * View Notices
 * 
 * This file allows teachers to view school notices and announcements.
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

// Include database connection
require_once __DIR__ . '/../includes/Database.php';
$db = Database::getInstance();
$conn = $db->getConnection();

// Get notices
$notices_query = "SELECT n.id, n.title, n.content, n.date_posted, n.category, u.name as posted_by 
               FROM notices n 
               LEFT JOIN users u ON n.posted_by = u.id 
               ORDER BY n.date_posted DESC";
$notices = $db->getRecords($notices_query);

// Set page variables for header
$page_title = 'School Notices';
$base_path = '..';

// Include header
include_once '../includes/header.php';
?>

<!-- Main Content -->
<main class="flex-grow container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">School Notices</h2>
        <p class="text-gray-600">View important announcements and notices from the school administration.</p>
    </div>
    
    <!-- Notices List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <?php if (empty($notices)): ?>
            <div class="p-6">
                <p class="text-gray-600">No notices available at this time.</p>
            </div>
        <?php else: ?>
            <div class="divide-y divide-gray-200">
                <?php foreach ($notices as $notice): ?>
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($notice['title']); ?></h3>
                                <div class="flex items-center text-sm text-gray-500 mt-1">
                                    <span class="mr-3"><?php echo date('F j, Y', strtotime($notice['date_posted'])); ?></span>
                                    <span class="mr-3">Posted by: <?php echo htmlspecialchars($notice['posted_by']); ?></span>
                                    <?php if (!empty($notice['category'])): ?>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded"><?php echo htmlspecialchars($notice['category']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-gray-700">
                            <?php echo nl2br(htmlspecialchars($notice['content'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include_once '../includes/footer.php'; ?>