<?php
/**
 * Vacancies Management Page
 * 
 * This file serves as the main page for managing vacancies.
 * It provides functionality to view, add, edit, and delete vacancies.
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
    <h1 class="text-3xl font-bold mb-6">Manage Vacancies</h1>
    
    <div class="mb-4">
        <a href="add_vacancy.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add New Vacancy
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4">
            <h2 class="text-xl font-semibold mb-4">Current Vacancies</h2>
            
            <!-- Vacancies list will be displayed here -->
            <div id="vacancies-list">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once __DIR__ . '/../../includes/footer.php';
?>