<?php
/**
 * Admin Header
 * 
 * This file contains the header and navigation for the admin panel.
 */

// Get current user if not already set
if (!isset($user) && isset($_SESSION['user_id'])) {
    $user = $auth->getCurrentUser();
}
?>

<header class="bg-white border-b border-gray-200 shadow-sm">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-3">
            <div class="flex items-center">
                <a href="index.php" class="text-xl font-bold text-gray-800">School Management System</a>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">Welcome, <?php echo htmlspecialchars($user['username'] ?? 'Admin'); ?></span>
                <a href="../auth/logout.php" class="text-sm text-red-600 hover:text-red-800">Logout</a>
            </div>
        </div>
        
        <!-- Admin Navigation -->
        <nav class="flex space-x-6 py-3">
            <a href="index.php" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Dashboard</a>
            <a href="manage_users.php" class="text-gray-600 hover:text-gray-900 text-sm font-medium">User Management</a>
            <a href="manage_complaints.php" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Complaints</a>
            <a href="../result-system/manage/manage.php" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Results</a>
        </nav>
    </div>
</header>