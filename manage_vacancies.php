<?php
include 'db.php';

if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM vacancies WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    echo "<script>alert('Vacancy deleted successfully!'); window.location.href = 'manage_vacancies.php';</script>";
}

$vacancies = $pdo->query("SELECT * FROM vacancies ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Vacancies</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto p-6">
        <h2 class="text-2xl font-bold text-blue-600 mb-4">Manage Vacancies</h2>
        <?php if ($vacancies): ?>
            <table class="w-full bg-white rounded-md shadow-md">
                <thead>
                    <tr>
                        <th class="p-2 border-b">Title</th>
                        <th class="p-2 border-b">Description</th>
                        <th class="p-2 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vacancies as $vacancy): ?>
                        <tr>
                            <td class="p-2 border-b"><?php echo htmlspecialchars($vacancy['title']); ?></td>
                            <td class="p-2 border-b"><?php echo htmlspecialchars($vacancy['description']); ?></td>
                            <td class="p-2 border-b">
                                <a href="edit_vacancy.php?id=<?php echo $vacancy['id']; ?>" class="text-blue-500">Edit</a> |
                                <a href="?delete_id=<?php echo $vacancy['id']; ?>" onclick="return confirm('Delete this vacancy?')" class="text-red-500">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No vacancies available.</p>
        <?php endif; ?>
    </div>
</body>
</html>
