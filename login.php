<?php
/**
 * Login Page
 * 
 * This file handles user authentication with proper security measures
 * and redirects to appropriate dashboard based on user role.
 */

// Include authentication functions
require_once 'auth/auth_functions.php';

// Initialize Auth class
$auth = new Auth();

// Check if user is already logged in
if ($auth->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $user = $auth->login($username, $password);
        
        if ($user) {
            // Login successful - redirect to dashboard
            header('Location: index.php');
            exit();
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/modern-theme.css" rel="stylesheet">
</head>
<body class="bg-white min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-black mb-2">Welcome Back</h1>
            <p class="text-gray-600">Please sign in to continue</p>
        </div>

        <div class="card transform hover:scale-105 transition-transform duration-300">
            <div class="p-8">
                <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
                <?php endif; ?>

                <form method="POST" action="login.php" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" id="username" name="username" required
                                   class="form-input pl-10 w-full"
                                   placeholder="Enter your username">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password" name="password" required
                                   class="form-input pl-10 w-full"
                                   placeholder="Enter your password">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember"
                                   class="h-4 w-4 text-black focus:ring-black border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Remember me
                            </label>
                        </div>
                        <a href="#" class="text-sm text-black hover:text-gray-700 transition-colors duration-300">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit" class="btn w-full">
                        <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account? 
                        <a href="contact_admin.php" class="text-black hover:text-gray-700 font-medium transition-colors duration-300">
                            Contact Administrator
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add animation class to card when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const card = document.querySelector('.card');
            card.classList.add('fade-in');
        });
    </script>
</body>
</html>
