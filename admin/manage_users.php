<?php
/**
 * User Management
 * 
 * This file allows administrators to manage user accounts,
 * including creating, editing, deleting, and suspending users with different roles.
 * It also provides functionality to change admin credentials.
 */

// Include authentication functions
require_once __DIR__ . '/../auth/auth_functions.php';

// Initialize Auth class
$auth = new Auth();

// Check if user is logged in and has admin role
if (!$auth->isLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

// Check if user has admin role
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Check session timeout
if (!$auth->checkSessionTimeout()) {
    header('Location: ../auth/login.php');
    exit;
}

// Initialize Database
$db = Database::getInstance();

// Ensure admin user exists
$auth->ensureAdminExists();

// Initialize variables
$users = [];
$error_message = '';
$success_message = '';
$edit_user = null;

// Handle suspend user
if (isset($_GET['action']) && $_GET['action'] === 'suspend' && isset($_GET['id']) && isset($_GET['days'])) {
    $user_id = (int)$_GET['id'];
    $days = (int)$_GET['days'];
    $reason = $_GET['reason'] ?? 'Violation of terms of service';
    
    // Don't allow suspending the admin user or current user
    $user = $db->getRecord("SELECT role FROM users WHERE id = ?", [$user_id]);
    
    if ($user && $user['role'] === 'admin') {
        $error_message = 'Cannot suspend the admin user.';
    } elseif ($user_id === (int)$_SESSION['user_id']) {
        $error_message = 'Cannot suspend your own account.';
    } else {
        // Use the Auth class to suspend the user
        $result = $auth->suspendUser($user_id, $days, $reason);
        
        if ($result) {
            $success_message = 'User suspended successfully for ' . $days . ' days.';
        } else {
            $error_message = 'Failed to suspend user.';
        }
    }
}

// Handle ban user
if (isset($_GET['action']) && $_GET['action'] === 'ban' && isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    $reason = $_GET['reason'] ?? 'Permanent violation of terms of service';
    
    // Don't allow banning the admin user or current user
    $user = $db->getRecord("SELECT role FROM users WHERE id = ?", [$user_id]);
    
    if ($user && $user['role'] === 'admin') {
        $error_message = 'Cannot ban the admin user.';
    } elseif ($user_id === (int)$_SESSION['user_id']) {
        $error_message = 'Cannot ban your own account.';
    } else {
        // Use the Auth class to ban the user
        $result = $auth->banUser($user_id, $reason);
        
        if ($result) {
            $success_message = 'User banned permanently.';
        } else {
            $error_message = 'Failed to ban user.';
        }
    }
}

// Handle unban user
if (isset($_GET['action']) && $_GET['action'] === 'unban' && isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    
    // Use the Auth class to unban the user
    $result = $auth->unbanUser($user_id);
    
    if ($result) {
        $success_message = 'User unbanned successfully.';
    } else {
        $error_message = 'Failed to unban user.';
    }
}

// Handle unsuspend user
if (isset($_GET['action']) && $_GET['action'] === 'unsuspend' && isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    
    // Use the Auth class to unsuspend the user
    $result = $auth->unsuspendUser($user_id);
    
    if ($result) {
        $success_message = 'User unsuspended successfully.';
    } else {
        $error_message = 'Failed to unsuspend user.';
    }
}

// Handle delete user
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    
    // Don't allow deleting the admin user
    $user = $db->getRecord("SELECT role FROM users WHERE id = ?", [$user_id]);
    
    if ($user && $user['role'] === 'admin') {
        $error_message = 'Cannot delete the admin user.';
    } elseif ($user_id === (int)$_SESSION['user_id']) {
        $error_message = 'Cannot delete your own account.';
    } else {
        $result = $db->execute("DELETE FROM users WHERE id = ?", [$user_id]);
        
        if ($result) {
            $success_message = 'User deleted successfully.';
        } else {
            $error_message = 'Failed to delete user.';
        }
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update Admin Credentials
    if (isset($_POST['action']) && $_POST['action'] === 'update_admin') {
        $newUsername = trim($_POST['admin_username'] ?? '');
        $newPassword = $_POST['admin_password'] ?? '';
        
        if (empty($newUsername) || empty($newPassword)) {
            $error_message = 'Username and password are required.';
        } else {
            $result = $auth->updateAdminCredentials($newUsername, $newPassword);
            
            if ($result) {
                $success_message = 'Admin credentials updated successfully. Please log in again with the new credentials.';
                // Log out the user to force login with new credentials
                $auth->logout();
                header('Location: ../auth/login.php?message=' . urlencode('Admin credentials updated. Please log in with your new credentials.'));
                exit;
            } else {
                $error_message = 'Failed to update admin credentials.';
            }
        }
    }
    
    // Add/Edit User
    if (isset($_POST['action']) && ($_POST['action'] === 'add' || $_POST['action'] === 'edit')) {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'student';
        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        
        // Validate input
        if (empty($username)) {
            $error_message = 'Username is required.';
        } else {
            // Check if username exists (for new users)
            if ($_POST['action'] === 'add') {
                $existing_user = $db->getRecord(
                    "SELECT id FROM users WHERE username = ?", 
                    [$username]
                );
                
                if ($existing_user) {
                    $error_message = 'Username already exists.';
                } else if (empty($password)) {
                    $error_message = 'Password is required for new users.';
                } else {
                    // Add new user
                    $userData = [
                        'username' => $username,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'role' => $role,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    try {
                        // Debug information
                        error_log("Attempting to add user with data: " . print_r($userData, true));
                        
                        // Check if the users table exists
                        $tableExists = $db->getRecord("SHOW TABLES LIKE 'users'");
                        if (!$tableExists) {
                            throw new Exception("Users table does not exist");
                        }
                        
                        // Check table structure
                        $columns = $db->getRecords("SHOW COLUMNS FROM users");
                        error_log("Table structure: " . print_r($columns, true));
                        
                        $result = $db->insert('users', $userData);
                        
                        if ($result) {
                            $success_message = 'User added successfully.';
                        } else {
                            // Get PDO error info
                            $errorInfo = $db->getConnection()->errorInfo();
                            $error_message = 'Failed to add user. Database error: ' . ($errorInfo[2] ?? 'Unknown error');
                            error_log("Database error: " . print_r($errorInfo, true));
                        }
                    } catch (Exception $e) {
                        $error_message = 'Failed to add user. Error: ' . $e->getMessage();
                        error_log("Exception: " . $e->getMessage());
                    }
                }
            } else { // Edit existing user
                $data = ['username' => $username, 'role' => $role];
                
                // Only update password if provided
                if (!empty($password)) {
                    $data['password'] = password_hash($password, PASSWORD_DEFAULT);
                }
                
                $result = $db->update('users', $data, 'id = ?', [$user_id]);
                
                if ($result !== false) {
                    $success_message = 'User updated successfully.';
                } else {
                    $error_message = 'Failed to update user.';
                }
            }
        }
    }
}

// Handle edit request
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $user_id = (int)$_GET['edit'];
    $edit_user = $db->getRecord("SELECT id, username, role FROM users WHERE id = ?", [$user_id]);
    
    if (!$edit_user) {
        $error_message = 'User not found.';
    }
}

// Get all users with suspension info
$users = $db->getRecords("SELECT id, username, role, password, created_at, is_suspended, suspend_until FROM users ORDER BY username");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - School Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
        }
        body {
            background-color: white;
            color: black;
        }
        .card {
            background-color: white;
            color: black;
            border: 1px solid #e5e7eb;
        }
        .table th {
            color: black;
        }
        .table td {
            color: black;
        }
    </style>
</head>
<body class="bg-white min-h-screen flex flex-col">
    <!-- Header/Navigation -->
    <header class="bg-blue-600 text-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold">User Management</h1>
                </div>
                <nav>
                    <ul class="flex space-x-6">
                        <li><a href="index.php" class="hover:text-blue-200 transition">Dashboard</a></li>
                        <li><a href="../auth/logout.php" class="hover:text-blue-200 transition">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-8">
        <!-- Messages -->
        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p><?php echo htmlspecialchars($error_message); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p><?php echo htmlspecialchars($success_message); ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Admin Credentials Section -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-blue-600 text-white p-4">
                <h3 class="text-xl font-bold">Update Admin Credentials</h3>
            </div>
            <div class="p-6">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="space-y-4">
                    <input type="hidden" name="action" value="update_admin">
                    
                    <div class="flex flex-wrap -mx-2">
                        <div class="w-full md:w-1/2 px-2 mb-4">
                            <label for="admin_username" class="block text-gray-700 font-medium mb-2">New Admin Username</label>
                            <input type="text" id="admin_username" name="admin_username" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        
                        <div class="w-full md:w-1/2 px-2 mb-4">
                            <label for="admin_password" class="block text-gray-700 font-medium mb-2">New Admin Password</label>
                            <input type="password" id="admin_password" name="admin_password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200">Update Admin Credentials</button>
                </form>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- User Form -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-blue-600 text-white p-4">
                    <h3 class="text-xl font-bold"><?php echo $edit_user ? 'Edit User' : 'Add New User'; ?></h3>
                </div>
                <div class="p-6">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="space-y-4">
                        <input type="hidden" name="action" value="<?php echo $edit_user ? 'edit' : 'add'; ?>">
                        <?php if ($edit_user): ?>
                            <input type="hidden" name="user_id" value="<?php echo $edit_user['id']; ?>">
                        <?php endif; ?>
                        
                        <div>
                            <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                value="<?php echo $edit_user ? htmlspecialchars($edit_user['username']) : ''; ?>" 
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                required
                            >
                        </div>
                        
                        <div>
                            <label for="password" class="block text-gray-700 font-medium mb-2">
                                Password <?php echo $edit_user ? '(Leave blank to keep current)' : ''; ?>
                            </label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                <?php echo $edit_user ? '' : 'required'; ?>
                            >
                        </div>
                        
                        <div>
                            <label for="role" class="block text-gray-700 font-medium mb-2">Role</label>
                            <select 
                                id="role" 
                                name="role" 
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="admin" <?php echo ($edit_user && $edit_user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                                <option value="teacher" <?php echo ($edit_user && $edit_user['role'] === 'teacher') ? 'selected' : ''; ?>>Teacher</option>
                                <option value="student" <?php echo ($edit_user && $edit_user['role'] === 'student') ? 'selected' : ''; ?>>Student</option>
                            </select>
                        </div>
                        
                        <div>
                            <button 
                                type="submit" 
                                class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200"
                            >
                                <?php echo $edit_user ? 'Update User' : 'Add User'; ?>
                            </button>
                        </div>
                        
                        <?php if ($edit_user): ?>
                            <div>
                                <a 
                                    href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" 
                                    class="block w-full bg-gray-500 text-white text-center py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-200"
                                >
                                    Cancel
                                </a>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <!-- User List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden md:col-span-2">
                <div class="bg-blue-600 text-white p-4">
                    <h3 class="text-xl font-bold">User Accounts</h3>
                </div>
                <div class="p-6 overflow-x-auto">
                    <?php if (empty($users)): ?>
                        <p class="text-gray-600 text-center">No users found.</p>
                    <?php else: ?>
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($user['id']); ?></td>
                                        <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php 
                                                    switch ($user['role']) {
                                                        case 'admin':
                                                            echo 'bg-red-100 text-red-800';
                                                            break;
                                                        case 'teacher':
                                                            echo 'bg-green-100 text-green-800';
                                                            break;
                                                        default:
                                                            echo 'bg-blue-100 text-blue-800';
                                                    }
                                                ?>"
                                            >
                                                <?php echo htmlspecialchars(ucfirst($user['role'])); ?>
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <?php if (!empty($user['is_banned']) && $user['is_banned'] == 1): ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Banned (Permanent)
                                                </span>
                                            <?php elseif (!empty($user['is_suspended']) && $user['is_suspended'] == 1): ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Suspended until <?php echo date('M d, Y', strtotime($user['suspend_until'])); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <div class="flex flex-wrap space-x-2">
                                                <a href="?edit=<?php echo $user['id']; ?>" class="text-blue-600 hover:text-blue-900">Edit</a>
                                                
                                                <?php if ((int)$user['id'] !== (int)$_SESSION['user_id'] && $user['role'] !== 'admin'): ?>
                                                    <?php if (!empty($user['is_banned']) && $user['is_banned'] == 1): ?>
                                                        <a href="?action=unban&id=<?php echo $user['id']; ?>" class="text-green-600 hover:text-green-900" onclick="return confirm('Are you sure you want to unban this user?');">Unban</a>
                                                    <?php elseif (!empty($user['is_suspended']) && $user['is_suspended'] == 1): ?>
                                                        <a href="?action=unsuspend&id=<?php echo $user['id']; ?>" class="text-green-600 hover:text-green-900" onclick="return confirm('Are you sure you want to unsuspend this user?');">Unsuspend</a>
                                                    <?php else: ?>
                                                        <button onclick="openSuspendModal(<?php echo $user['id']; ?>)" class="text-yellow-600 hover:text-yellow-900">Suspend</button>
                                                        <button onclick="openBanModal(<?php echo $user['id']; ?>)" class="text-red-600 hover:text-red-900">Ban</button>
                                                    <?php endif; ?>
                                                    
                                                    <a href="?action=delete&id=<?php echo $user['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Suspend User Modal -->
    <div id="suspendModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Suspend User</h3>
                <span class="text-gray-500 text-2xl cursor-pointer" onclick="closeSuspendModal()">&times;</span>
            </div>
            <form id="suspendForm" action="" method="get">
                <input type="hidden" name="action" value="suspend">
                <input type="hidden" id="suspendUserId" name="id" value="">
                
                <div class="mb-4">
                    <label for="suspendDays" class="block text-gray-700 font-medium mb-2">Suspension Duration (days)</label>
                    <select id="suspendDays" name="days" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1">1 day</option>
                        <option value="3">3 days</option>
                        <option value="7">7 days</option>
                        <option value="14">14 days</option>
                        <option value="30">30 days</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeSuspendModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition duration-200">Suspend User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ban User Modal -->
    <div id="banModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Ban User Permanently</h3>
                <span class="text-gray-500 text-2xl cursor-pointer" onclick="closeBanModal()">&times;</span>
            </div>
            <form id="banForm" action="" method="get">
                <input type="hidden" name="action" value="ban">
                <input type="hidden" id="ban_user_id" name="id" value="">
                
                <div class="mb-4">
                    <label for="ban_reason" class="block text-gray-700 font-medium mb-2">Reason for Permanent Ban:</label>
                    <textarea id="ban_reason" name="reason" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">Permanent violation of terms of service</textarea>
                </div>
                
                <div class="mb-4">
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
                        <strong>Warning:</strong> This action will permanently ban the user. They will not be able to access the system until manually unbanned.
                    </div>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeBanModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200">Ban User Permanently</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-xl font-bold">Badimalika Secondary School</h3>
                    <p class="text-gray-400">Admin Portal</p>
                </div>
                <div class="text-gray-400 text-sm">
                    &copy; <?php echo date('Y'); ?> Badimalika Secondary School. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Modal -->
    <script>
        // Suspend Modal Functions
        function openSuspendModal(userId) {
            document.getElementById('suspendUserId').value = userId;
            document.getElementById('suspendModal').style.display = 'block';
        }
        
        function closeSuspendModal() {
            document.getElementById('suspendModal').style.display = 'none';
        }
        
        // Ban Modal Functions
        function openBanModal(userId) {
            document.getElementById('ban_user_id').value = userId;
            document.getElementById('banModal').style.display = 'block';
        }
        
        function closeBanModal() {
            document.getElementById('banModal').style.display = 'none';
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const suspendModal = document.getElementById('suspendModal');
            const banModal = document.getElementById('banModal');
            if (event.target === suspendModal) {
                closeSuspendModal();
            } else if (event.target === banModal) {
                closeBanModal();
            }
        }
    </script>
</body>
</html>