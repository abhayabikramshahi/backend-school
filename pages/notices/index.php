<?php
/**
 * Notices Management Page
 * 
 * This file serves as the main page for managing notices.
 * It provides functionality to view, add, edit, and delete notices.
 */

// Include authentication functions
require_once __DIR__ . '/../../auth/auth_functions.php';

// Initialize Auth class
$auth = new Auth();

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: ../../auth/login.php');
    exit;
}

// Check session timeout
if (!$auth->checkSessionTimeout()) {
    header('Location: ../../auth/login.php');
    exit;
}

// Get current user
$user = $auth->getCurrentUser();

// Include header
include_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Manage Notices</h1>
    
    <div class="mb-4">
        <a href="add_notice.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add New Notice
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4">
            <h2 class="text-xl font-semibold mb-4">Current Notices</h2>
            
            <!-- Notices list will be displayed here -->
            <div id="notices-list">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once __DIR__ . '/../../includes/footer.php';
?>