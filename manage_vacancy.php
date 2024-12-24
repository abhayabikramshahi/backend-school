<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Delete vacancy
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM vacancies WHERE id = ?");
        if ($stmt->execute([$deleteId])) {
            echo "<script>alert('Vacancy deleted successfully!'); window.location.href = 'manage_vacancy.php';</script>";
        } else {
            echo "Error deleting vacancy.";
        }
    } catch (PDOException $e) {
        echo "Database error: " . htmlspecialchars($e->getMessage());
    }
}


// Fetch all vacancies
$result = $pdo->query("SELECT * FROM vacancies ORDER BY created_at DESC");
$vacancies = $result->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Vacancies</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="max-w-4xl mx-auto py-6 px-4">
        <h1 class="text-3xl font-bold text-blue-600 mb-6">Manage Vacancies</h1>

        <?php if (count($vacancies) > 0): ?>
            <table class="min-w-full bg-white border border-gray-200 rounded-md shadow-md">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2 text-left">#</th>
                        <th class="px-4 py-2 text-left">Title</th>
                        <th class="px-4 py-2 text-left">Description</th>
                        <th class="px-4 py-2 text-left">Image</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vacancies as $index => $vacancy): ?>
                        <tr class="border-b">
                            <td class="px-4 py-2"><?php echo $index + 1; ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($vacancy['title']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($vacancy['description']); ?></td>
                            <td class="px-4 py-2">
                                <img src="<?php echo htmlspecialchars($vacancy['image_path']); ?>" width="100" alt="Vacancy Image" class="rounded-md">
                            </td>
                            <td class="px-4 py-2">
                                <a href="edit_vacancy.php?id=<?php echo $vacancy['id']; ?>" class="text-blue-600 hover:underline">Edit</a> |
                                <a href="manage_vacancy.php?delete_id=<?php echo $vacancy['id']; ?>" onclick="return confirm('Are you sure?')" class="text-red-600 hover:underline">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center text-gray-600 mt-6">No vacancies available at the moment.</p>
        <?php endif; ?>

        <div class="mt-4 text-center">
            <a href="index.php" class="text-blue-500 hover:underline">Go back</a>
        </div>
    </div>
</body>
</html>
