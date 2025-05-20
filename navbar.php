<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badimalika Secondary School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/school-theme.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Professional Navbar -->
    <?php
    // Get current page for active state
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="index.php" class="text-2xl font-bold text-black hover:text-gray-700 transition-colors duration-300">
                            <span class="bg-clip-text text-transparent bg-gradient-to-r from-black to-gray-600">School Management</span>
                        </a>
                    </div>
                    
                    <!-- Navigation Links -->
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="index.php" 
                           class="<?php echo $current_page === 'index.php' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-black'; ?> nav-link inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-home mr-2"></i> Dashboard
                        </a>
                        
                        <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'teacher'])): ?>
                        <a href="manage_students.php" 
                           class="<?php echo $current_page === 'manage_students.php' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-black'; ?> nav-link inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-user-graduate mr-2"></i> Students
                        </a>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="manage_teachers.php" 
                           class="<?php echo $current_page === 'manage_teachers.php' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-black'; ?> nav-link inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-chalkboard-teacher mr-2"></i> Teachers
                        </a>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'teacher'])): ?>
                        <a href="manage_results.php" 
                           class="<?php echo $current_page === 'manage_results.php' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-black'; ?> nav-link inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-chart-bar mr-2"></i> Results
                        </a>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="manage_classes.php" 
                           class="<?php echo $current_page === 'manage_classes.php' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-black'; ?> nav-link inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-school mr-2"></i> Classes
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Right side -->
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="ml-3 relative">
                            <div class="flex items-center space-x-4">
                                <div class="text-gray-700 bg-gray-100 px-4 py-2 rounded-full">
                                    <i class="fas fa-user-circle mr-2"></i>
                                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                                </div>
                                <a href="logout.php" class="btn text-white">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn text-white">
                            <i class="fas fa-sign-in-alt mr-2"></i> Login
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile menu button -->
                <div class="flex items-center sm:hidden">
                    <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-black hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-black transition-colors duration-300">
                        <span class="sr-only">Open main menu</span>
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div class="sm:hidden mobile-menu hidden">
            <div class="pt-2 pb-3 space-y-1">
                <a href="index.php" 
                   class="<?php echo $current_page === 'index.php' ? 'bg-gray-100 border-black text-black' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:text-black'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors duration-300">
                    <i class="fas fa-home mr-2"></i> Dashboard
                </a>
                
                <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'teacher'])): ?>
                <a href="manage_students.php" 
                   class="<?php echo $current_page === 'manage_students.php' ? 'bg-gray-100 border-black text-black' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:text-black'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors duration-300">
                    <i class="fas fa-user-graduate mr-2"></i> Students
                </a>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="manage_teachers.php" 
                   class="<?php echo $current_page === 'manage_teachers.php' ? 'bg-gray-100 border-black text-black' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:text-black'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors duration-300">
                    <i class="fas fa-chalkboard-teacher mr-2"></i> Teachers
                </a>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'teacher'])): ?>
                <a href="manage_results.php" 
                   class="<?php echo $current_page === 'manage_results.php' ? 'bg-gray-100 border-black text-black' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:text-black'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors duration-300">
                    <i class="fas fa-chart-bar mr-2"></i> Results
                </a>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="manage_classes.php" 
                   class="<?php echo $current_page === 'manage_classes.php' ? 'bg-gray-100 border-black text-black' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:text-black'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors duration-300">
                    <i class="fas fa-school mr-2"></i> Classes
                </a>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="pt-4 pb-3 border-t border-gray-200">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-circle text-gray-400 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                <?php echo htmlspecialchars($_SESSION['role']); ?>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <a href="logout.php" class="block px-4 py-2 text-base font-medium text-red-600 hover:text-red-800 hover:bg-gray-50 transition-colors duration-300">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <div class="pt-4 pb-3 border-t border-gray-200">
                    <a href="login.php" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-black hover:bg-gray-50 transition-colors duration-300">
                        <i class="fas fa-sign-in-alt mr-1"></i> Login
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <script>
        // Mobile menu toggle with animation
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('.mobile-menu-button');
            const mobileMenu = document.querySelector('.mobile-menu');
            
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
                if (!mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('fade-in');
                }
            });
        });
    </script>
</body>
</html>