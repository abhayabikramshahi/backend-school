<?php
/**
 * Manage Vacancies
 * 
 * This file allows administrators to manage job vacancies.
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

// Handle vacancy actions (add, edit, delete)
$message = '';
$error = '';
$edit_vacancy = null;

// Delete vacancy
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $vacancy_id = $_GET['delete'];
    
    // Check if vacancy exists
    $check_query = "SELECT id FROM vacancies WHERE id = ?";
    $vacancy = $db->getRecord($check_query, [$vacancy_id]);
    
    if ($vacancy) {
        // Delete vacancy
        $delete_query = "DELETE FROM vacancies WHERE id = ?";
        $db->executeQuery($delete_query, [$vacancy_id]);
        
        $message = 'Vacancy has been deleted successfully.';
    } else {
        $error = 'Vacancy not found.';
    }
}

// Edit vacancy - load data
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $vacancy_id = $_GET['edit'];
    
    $edit_query = "SELECT * FROM vacancies WHERE id = ?";
    $edit_vacancy = $db->getRecord($edit_query, [$vacancy_id]);
    
    if (!$edit_vacancy) {
        $error = 'Vacancy not found.';
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_vacancy']) || isset($_POST['update_vacancy'])) {
        $position = $_POST['position'] ?? '';
        $department = $_POST['department'] ?? '';
        $qualifications = $_POST['qualifications'] ?? '';
        $description = $_POST['description'] ?? '';
        $deadline = $_POST['deadline'] ?? '';
        $status = $_POST['status'] ?? 'Open';
        
        if (empty($position) || empty($department) || empty($qualifications) || empty($description) || empty($deadline)) {
            $error = 'Please fill in all required fields.';
        } else {
            if (isset($_POST['add_vacancy'])) {
                // Insert new vacancy
                $insert_query = "INSERT INTO vacancies (position, department, qualifications, description, posted_date, deadline, status, posted_by) 
                               VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)";
                $db->executeQuery($insert_query, [$position, $department, $qualifications, $description, $deadline, $status, $user['id']]);
                
                $message = 'Vacancy has been added successfully.';
            } else if (isset($_POST['update_vacancy']) && isset($_POST['vacancy_id'])) {
                $vacancy_id = $_POST['vacancy_id'];
                
                // Update vacancy
                $update_query = "UPDATE vacancies SET position = ?, department = ?, qualifications = ?, description = ?, deadline = ?, status = ? WHERE id = ?";
                $db->executeQuery($update_query, [$position, $department, $qualifications, $description, $deadline, $status, $vacancy_id]);
                
                $message = 'Vacancy has been updated successfully.';
                
                // Clear edit mode
                $edit_vacancy = null;
            }
        }
    }
}

// Get all vacancies
$vacancies_query = "SELECT v.id, v.position, v.department, v.posted_date, v.deadline, v.status, u.name as posted_by 
                 FROM vacancies v 
                 LEFT JOIN users u ON v.posted_by = u.id 
                 ORDER BY v.posted_date DESC";
$vacancies = $db->getRecords($vacancies_query);

// Set page variables for header
$page_title = 'Manage Vacancies';
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
            <h1 class="text-3xl font-bold text-black">Manage Vacancies</h1>
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
        
        <!-- Vacancy Form -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="bg-red-600 text-white p-4">
                <h2 class="text-xl font-bold"><?php echo $edit_vacancy ? 'Edit Vacancy' : 'Add New Vacancy'; ?></h2>
            </div>
            <div class="p-6">
                <form method="post" action="" class="space-y-4">
                    <?php if ($edit_vacancy): ?>
                        <input type="hidden" name="vacancy_id" value="<?php echo $edit_vacancy['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position*</label>
                            <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($edit_vacancy['position'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50" required>
                        </div>
                        
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department*</label>
                            <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($edit_vacancy['department'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50" required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="qualifications" class="block text-sm font-medium text-gray-700 mb-1">Qualifications*</label>
                        <textarea id="qualifications" name="qualifications" rows="3" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50" required><?php echo htmlspecialchars($edit_vacancy['qualifications'] ?? ''); ?></textarea>
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Job Description*</label>
                        <textarea id="description" name="description" rows="5" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50" required><?php echo htmlspecialchars($edit_vacancy['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">Application Deadline*</label>
                            <input type="date" id="deadline" name="deadline" value="<?php echo htmlspecialchars($edit_vacancy['deadline'] ?? ''); ?>" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50" required>
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="status" name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
                                <option value="Open" <?php echo (isset($edit_vacancy['status']) && $edit_vacancy['status'] == 'Open') ? 'selected' : ''; ?>>Open</option>
                                <option value="Closed" <?php echo (isset($edit_vacancy['status']) && $edit_vacancy['status'] == 'Closed') ? 'selected' : ''; ?>>Closed</option>
                                <option value="On Hold" <?php echo (isset($edit_vacancy['status']) && $edit_vacancy['status'] == 'On Hold') ? 'selected' : ''; ?>>On Hold</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-4">
                        <?php if ($edit_vacancy): ?>
                            <a href="manage_vacancies.php" class="bg-gray-200 hover:bg-gray-300 text-black py-2 px-4 rounded transition">Cancel</a>
                            <button type="submit" name="update_vacancy" class="bg-red-600 hover:bg-red-700 text-white py-2 px-6 rounded-md transition">Update Vacancy</button>
                        <?php else: ?>
                            <button type="submit" name="add_vacancy" class="bg-red-600 hover:bg-red-700 text-white py-2 px-6 rounded-md transition">Add Vacancy</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Vacancies List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-red-600 text-white p-4">
                <h2 class="text-xl font-bold">Vacancies List</h2>
            </div>
            <div class="p-6">
                <?php if (empty($vacancies)): ?>
                    <div class="text-center py-4">
                        <p class="text-gray-600">No vacancies found.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-100 text-black">
                                    <th class="py-3 px-4 text-left">Position</th>
                                    <th class="py-3 px-4 text-left">Department</th>
                                    <th class="py-3 px-4 text-left">Posted Date</th>
                                    <th class="py-3 px-4 text-left">Deadline</th>
                                    <th class="py-3 px-4 text-left">Status</th>
                                    <th class="py-3 px-4 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vacancies as $vacancy): ?>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($vacancy['position']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($vacancy['department']); ?></td>
                                        <td class="py-3 px-4"><?php echo date('d M Y', strtotime($vacancy['posted_date'])); ?></td>
                                        <td class="py-3 px-4"><?php echo date('d M Y', strtotime($vacancy['deadline'])); ?></td>
                                        <td class="py-3 px-4">
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                                <?php 
                                                    switch($vacancy['status']) {
                                                        case 'Open': echo 'bg-green-100 text-green-800'; break;
                                                        case 'Closed': echo 'bg-red-100 text-red-800'; break;
                                                        case 'On Hold': echo 'bg-yellow-100 text-yellow-800'; break;
                                                        default: echo 'bg-gray-100 text-gray-800';
                                                    }
                                                ?>"
                                            >
                                                <?php echo htmlspecialchars($vacancy['status']); ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <a href="manage_vacancies.php?edit=<?php echo $vacancy['id']; ?>" class="text-blue-600 hover:text-blue-800 mr-2">Edit</a>
                                            <a href="manage_vacancies.php?delete=<?php echo $vacancy['id']; ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this vacancy?')">Delete</a>
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