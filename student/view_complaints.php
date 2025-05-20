<?php
/**
 * View Complaints
 * 
 * This file allows students to view their submitted complaints
 * and track their status.
 */

// Include authentication functions
require_once __DIR__ . '/../auth/auth_functions.php';

// Initialize Auth class
$auth = new Auth();

// Check if user is logged in and has student role
if (!$auth->isLoggedIn() || !$auth->hasRole('student')) {
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

// Initialize Database
$db = Database::getInstance();

// Get user's complaints
$complaints = $db->getRecords(
    "SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC", 
    [$_SESSION['user_id']]
);

// Set page variables for header
$page_title = 'My Complaints';
$base_path = '..';

// Include header
include_once '../includes/header.php';
?>

<!-- Main Content -->
<main class="flex-grow container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-blue-600 text-white p-4 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">My Complaints</h2>
                <p class="text-sm opacity-90">View and track the status of your submitted complaints</p>
            </div>
            <a href="submit_complaint.php" class="bg-white text-blue-600 hover:bg-blue-100 font-medium py-2 px-4 rounded-md text-sm transition duration-150 ease-in-out">
                Submit New Complaint
            </a>
        </div>
        
        <div class="p-6">
            <?php if (empty($complaints)): ?>
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No complaints found</h3>
                    <p class="mt-1 text-sm text-gray-500">You haven't submitted any complaints yet.</p>
                    <div class="mt-6">
                        <a href="submit_complaint.php" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Submit a Complaint
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Anonymous</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($complaints as $complaint): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($complaint['title']); ?>
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
                                        <?php echo $complaint['is_anonymous'] ? 'Yes' : 'No'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <button type="button" class="text-blue-600 hover:text-blue-900" 
                                                onclick="showDetails('<?php echo htmlspecialchars(addslashes($complaint['title'])); ?>', '<?php echo htmlspecialchars(addslashes($complaint['description'])); ?>', '<?php echo $statusText; ?>', '<?php echo date('M d, Y', strtotime($complaint['created_at'])); ?>')">
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
        <div class="bg-blue-600 px-4 py-3 text-white">
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
            <div>
                <p class="text-sm text-gray-500 mb-1">Description</p>
                <p class="text-gray-700" id="modalDescription"></p>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal()">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    function showDetails(title, description, status, date) {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalDescription').textContent = description;
        document.getElementById('modalStatus').textContent = status;
        document.getElementById('modalDate').textContent = date;
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

<?php include_once '../includes/footer.php'; ?>