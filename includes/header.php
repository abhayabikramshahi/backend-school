<?php
/**
 * Common Header Component
 * 
 * This file contains the common header used across the application
 * for consistent UI and navigation.
 */

// Include authentication functions if not already included
if (!class_exists('Auth')) {
    require_once __DIR__ . '/../auth/auth_functions.php';
    $auth = new Auth();
}

// Get current user if logged in
$current_user = $auth->isLoggedIn() ? $auth->getCurrentUser() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Badimalika Secondary School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo isset($base_path) ? $base_path : ''; ?>/assets/css/school-theme.css">
    <link rel="shortcut icon" href="<?php echo isset($base_path) ? $base_path : ''; ?>/assets/images/favicon.ico" type="image/x-icon">
    <?php if (isset($extra_css)): echo $extra_css; endif; ?>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Header/Navigation -->
    <header class="shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-4 md:mb-0">
                    <h1 class="text-2xl font-bold school-header">Badimalika Secondary School</h1>
                </div>
                <nav>
                    <ul class="flex space-x-6">
                        <li><a href="<?php echo isset($base_path) ? $base_path : ''; ?>/index.php" class="transition">Home</a></li>
                        
                        <?php if ($current_user): ?>
                            <?php if ($auth->hasRole('admin')): ?>
                                <li><a href="<?php echo isset($base_path) ? $base_path : ''; ?>/admin/index.php" class="transition">Admin Dashboard</a></li>
                            <?php elseif ($auth->hasRole('teacher')): ?>
                                <li><a href="<?php echo isset($base_path) ? $base_path : ''; ?>/teacher/index.php" class="transition">Teacher Dashboard</a></li>
                            <?php elseif ($auth->hasRole('student')): ?>
                                <li><a href="<?php echo isset($base_path) ? $base_path : ''; ?>/student/index.php" class="transition">Student Dashboard</a></li>
                            <?php endif; ?>
                            <li><a href="<?php echo isset($base_path) ? $base_path : ''; ?>/auth/logout.php" class="transition">Logout</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo isset($base_path) ? $base_path : ''; ?>/auth/login.php" class="transition">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>