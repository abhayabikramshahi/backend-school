<?php
// Include database connection using the Database class
require_once __DIR__ . '/../includes/Database.php';

// Initialize database connection
$db = Database::getInstance();
$pdo = $db->getConnection();

// Display success message if available
$successMessage = '';
if (isset($_GET['success'])) {
    $successMessage = htmlspecialchars($_GET['success']);
}

// Fetch teachers with optional search
$searchQuery = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = '%' . $_GET['search'] . '%'; // Add wildcards for LIKE query
    $searchQuery = "WHERE name LIKE :searchTerm OR role LIKE :searchTerm OR email LIKE :searchTerm OR phonenumber LIKE :searchTerm";
}

// Prepare the query
$query = "SELECT * FROM teachers $searchQuery ORDER BY id ASC";
$stmt = $pdo->prepare($query);

// Bind the search term if available
if (!empty($searchQuery)) {
    $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
}

// Execute the query
$stmt->execute();

// Fetch the results
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="https://badimalikasecschool.netlify.app/471f74d9-7a7c-4024-82b7-251a5aba58a3.jpg" type="image/x-icon">
</head>
<body class="bg-gray-100 font-sans">
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-blue-600 mb-6">Manage Teachers</h1>

        <!-- Success Message -->
        <?php if (!empty($successMessage)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $successMessage; ?></span>
            </div>
        <?php endif; ?>

        <!-- Search Bar -->
        <form method="GET" class="flex space-x-2 mb-6">
            <input 
                type="text" 
                name="search" 
                placeholder="Search by name, role, email, or phone number" 
                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" 
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button 
                type="submit" 
                class="px-6 py-2 bg-blue-500 text-white rounded-md shadow-md hover:bg-blue-600 transition duration-300">
                Search
            </button>
        </form>

        <!-- Teachers Table -->
        <?php if (is_array($teachers) && count($teachers) > 0): ?>
            <table class="table-auto w-full bg-white shadow-md rounded-md border-collapse">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Name</th>
                        <th class="border px-4 py-2">Role</th>
                        <th class="border px-4 py-2">Email</th>
                        <th class="border px-4 py-2">Phone Number</th>
                        <th class="border px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teachers as $row): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="border px-4 py-2 text-center"><?php echo htmlspecialchars($row['id']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($row['role']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($row['phonenumber']); ?></td>
                            <td class="border px-4 py-2 text-center">
                                <a href="edit_teacher.php?id=<?php echo $row['id']; ?>" class="text-blue-500 hover:underline">Edit</a> |
                                <form method="POST" action="delete_teachers.php" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="text-red-500 hover:underline bg-transparent border-none p-0 cursor-pointer">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-700">No teachers found.</p>
        <?php endif; ?>

        <!-- Footer -->
        <footer class="text-center mt-10 text-gray-500">
            &copy; 2024 Badimalika Secondary School. All rights reserved.
        </footer>
    </div>
</body>
</html>
