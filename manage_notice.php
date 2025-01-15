<?php
// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=look", "root", "");

// Handle delete request
if (isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];

    // Delete image file if it exists
    $stmt = $pdo->prepare("SELECT image_path FROM notices WHERE id = ?");
    $stmt->execute([$deleteId]);
    $notice = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($notice && !empty($notice['image_path']) && file_exists($notice['image_path'])) {
        unlink($notice['image_path']);
    }

    // Delete record
    $stmt = $pdo->prepare("DELETE FROM notices WHERE id = ?");
    $stmt->execute([$deleteId]);
    header("Location: manage_notices.php");
    exit();
}

// Fetch notices
$stmt = $pdo->query("SELECT * FROM notices ORDER BY created_at DESC");
$notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notices</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<!-- Navbar -->
<nav class="bg-blue-600 p-4 text-white">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-lg font-bold">Manage Notices</h1>
        <a href="index.php" class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-gray-200 transition">
            Back to Home
        </a>
    </div>
</nav>

<!-- Manage Notices Section -->
<div class="container mx-auto p-6 bg-white shadow-md rounded-md mt-6">
    <h1 class="text-3xl font-bold text-blue-600 mb-6 text-center">Manage Notices</h1>

    <?php if (!empty($notices)): ?>
        <div class="overflow-x-auto">
            <table class="table-auto w-full border-collapse bg-gray-50 rounded-lg shadow">
                <thead>
                    <tr class="bg-blue-500 text-white">
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Title</th>
                        <th class="border px-4 py-2">Image</th>
                        <th class="border px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notices as $notice): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="border px-4 py-2 text-center"><?php echo $notice['id']; ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($notice['title']); ?></td>
                            <td class="border px-4 py-2 text-center">
                                <?php if (!empty($notice['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($notice['image_path']); ?>" alt="Notice Image" class="w-16 h-16 object-cover rounded shadow">
                                <?php else: ?>
                                    <span class="text-gray-500">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td class="border px-4 py-2 text-center">
                            <a href="delete_notice.php?delete_id=<?php echo $notice['id']; ?>" 
   onclick="return confirm('Are you sure you want to delete this notice?');" 
   class="inline-block px-4 py-2 bg-red-500 text-white rounded shadow hover:bg-red-600 transition">
    Delete
</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-gray-700 text-center mt-6">No notices available to manage.</p>
    <?php endif; ?>
</div>

</body>
</html>
