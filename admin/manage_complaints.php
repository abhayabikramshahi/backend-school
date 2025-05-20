<?php
/**
 * Manage Complaints (Gunaso Peti)
 * 
 * This file allows administrators to view and manage all complaints
 * submitted by students.
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
if (!$auth->hasRole('admin') && $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Check session timeout
if (!$auth->checkSessionTimeout()) {
    header('Location: ../auth/login.php');
    exit;
}

// Initialize Database
$db = Database::getInstance();

// Initialize variables
$error_message = '';
$success_message = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $complaint_id = isset($_POST['complaint_id']) ? (int)$_POST['complaint_id'] : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    
    if ($complaint_id > 0 && in_array($status, ['pending', 'in_progress', 'resolved'])) {
        $result = $db->update('complaints', ['status' => $status], 'id = ?', [$complaint_id]);
        
        if ($result) {
            $success_message = 'Complaint status updated successfully.';
        } else {
            $error_message = 'Failed to update complaint status.';
        }
    } else {
        $error_message = 'Invalid complaint ID or status.';
    }
}

// Get all complaints with user information
$complaints = $db->getRecords(
    "SELECT c.*, u.username 
     FROM complaints c 
     JOIN users u ON c.user_id = u.id 
     ORDER BY c.created_at DESC"
);

// Set page title
$page_title = 'Manage Complaints';
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
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Header/Navigation -->
    <?php include_once 'admin_header.php'; ?>
    
    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-primary p-4 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-white">Manage Complaints (Gunaso Peti)</h2>
                    <p class="text-sm text-white opacity-90">View and manage student complaints and feedback</p>
                </div>
            </div>
            
            <div class="p-6">
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
                
                <?php if (empty($complaints)): ?>
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No complaints found</h3>
                        <p class="mt-1 text-sm text-gray-500">There are no complaints in the system yet.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted By</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($complaints as $complaint): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($complaint['title']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo $complaint['is_anonymous'] ? 'Anonymous' : htmlspecialchars($complaint['username']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <?php 
                                            $statusClass = '';
                                            $statusText = '';
                                            
                                            switch ($complaint['status']) {
                                                case 'pending':
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                    $statusText = 'Pending';
                                                    break;
                                                case 'in_progress':
                                                    $statusClass = 'bg-blue-100 text-blue-800';
                                                    $statusText = 'In Progress';
                                                    break;
                                                case 'resolved':
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                    $statusText = 'Resolved';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-gray-100 text-gray-800';
                                                    $statusText = 'Unknown';
                                            }
                                            ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                                                <?php echo $statusText; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('M d, Y', strtotime($complaint['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <button type="button" class="text-blue-600 hover:text-blue-900 mr-3" 
                                                    onclick="showDetails('<?php echo htmlspecialchars(addslashes($complaint['title'])); ?>', '<?php echo htmlspecialchars(addslashes($complaint['description'])); ?>', '<?php echo $statusText; ?>', '<?php echo date('M d, Y', strtotime($complaint['created_at'])); ?>', '<?php echo $complaint['id']; ?>', '<?php echo $complaint['status']; ?>')">
                                                View Details
                                            </button>
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
    
    <!-- Modal for complaint details -->
    <div id="detailsModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <div class="bg-primary px-4 py-3 text-white">
                <h3 class="text-lg font-medium" id="modalTitle"></h3>
            </div>
            <div class="px-4 py-3">
                <div class="mb-4">
                    <p class="text-sm text-gray-500 mb-1">Status</p>
                    <p class="font-medium" id="modalStatus"></p>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-500 mb-1">Submitted on</p>
                    <p class="font-medium" id="modalDate"></p>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-500 mb-1">Description</p>
                    <p class="text-gray-700" id="modalDescription"></p>
                </div>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="statusForm">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="complaint_id" id="complaintId">
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Update Status</label>
                        <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal()">
                    Close
                </button>
            </div>
        </div>
    </div>
    
    <script>
        function showDetails(title, description, status, date, id, currentStatus) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalDescription').textContent = description;
            document.getElementById('modalStatus').textContent = status;
            document.getElementById('modalDate').textContent = date;
            document.getElementById('complaintId').value = id;
            document.getElementById('status').value = currentStatus;
            document.getElementById('detailsModal').classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('detailsModal').classList.add('hidden');
        }
        
        // Close modal when clicking outside of it
        document.getElementById('detailsModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !document.getElementById('detailsModal').classList.contains('hidden')) {
                closeModal();
            }
        });
    </script>
    
    <!-- Footer -->
    <?php include_once '../includes/footer.php'; ?>
</body>
</html>