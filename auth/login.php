<?php
/**
 * Login Page for Auth Directory
 * 
 * This file handles user authentication with proper security measures
 * and redirects to appropriate dashboard based on user role.
 */

// Include authentication functions
require_once __DIR__ . '/auth_functions.php';

// Initialize Auth class
$auth = new Auth();

// Check if user is already logged in
if ($auth->isLoggedIn()) {
    // Redirect based on user role
    $user = $auth->getCurrentUser();
    if ($user && isset($user['role'])) {
        switch ($user['role']) {
            case 'admin':
                header('Location: ../admin/index.php');
                break;
            case 'teacher':
                header('Location: ../teacher/index.php');
                break;
            case 'student':
                header('Location: ../student/index.php');
                break;
            default:
                header('Location: ../index.php');
        }
        exit();
    }
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
            // Login successful - redirect based on role
            switch ($user['role']) {
                case 'admin':
                    header('Location: ../admin/index.php');
                    break;
                case 'teacher':
                    header('Location: ../teacher/index.php');
                    break;
                case 'student':
                    header('Location: ../student/index.php');
                    break;
                default:
                    header('Location: ../index.php');
            }
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
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Badimalika Secondary School</h1>
            <p class="text-gray-600">Login to your account</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-user text-gray-400"></i>
                    </span>
                    <input type="text" id="username" name="username" class="pl-10 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter your username">
                </div>
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-lock text-gray-400"></i>
                    </span>
                    <input type="password" id="password" name="password" class="pl-10 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter your password">
                </div>
            </div>
            
            <div class="flex items-center justify-between mb-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                    Sign In
                </button>
            </div>
        </form>
        
        <div class="text-center text-sm text-gray-600">
            <p>© <?php echo date('Y'); ?> Badimalika Secondary School. All rights reserved.</p>
        </div>
    </div>
</body>
</html>