<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Delete Notice
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM notices WHERE id = ?");
        if ($stmt->execute([$deleteId])) {
            echo "Notice deleted successfully!";
        } else {
            echo "Error deleting notice.";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}

// Bulk Delete Notices
if (isset($_POST['delete_selected'])) {
    if (!empty($_POST['selected_notices'])) {
        $placeholders = implode(',', array_fill(0, count($_POST['selected_notices']), '?'));
        try {
            $stmt = $pdo->prepare("DELETE FROM notices WHERE id IN ($placeholders)");
            if ($stmt->execute($_POST['selected_notices'])) {
                echo "Selected notices deleted successfully!";
            } else {
                echo "Error deleting selected notices.";
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }
}

// Fetch All Notices
try {
    $stmt = $pdo->query("SELECT * FROM notices ORDER BY created_at DESC");
    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    $notices = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notices</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="https://badimalikasecschool.netlify.app/471f74d9-7a7c-4024-82b7-251a5aba58a3.jpg" type="image/x-icon">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6 bg-white rounded-lg shadow-md mt-6">
        <h1 class="text-2xl font-bold text-blue-600">Manage Notices</h1>

        <?php if (count($notices) > 0): ?>
            <form action="manage_notice.php" method="POST">
                <div class="mb-4">
                    <input type="checkbox" id="select-all" class="form-checkbox h-5 w-5 text-blue-500"> 
                    <label for="select-all" class="text-sm text-gray-600">Select All</label>
                    <button type="submit" name="delete_selected" class="ml-4 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Delete Selected</button>
                </div>
                <table class="min-w-full table-auto">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border-b text-left">#</th>
                            <th class="px-4 py-2 border-b text-left">Title</th>
                            <th class="px-4 py-2 border-b text-left">Description</th>
                            <th class="px-4 py-2 border-b text-left">Image</th>
                            <th class="px-4 py-2 border-b text-left">Actions</th>
                            <th class="px-4 py-2 border-b text-left">Select</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notices as $index => $notice): ?>
                            <tr>
                                <td class="px-4 py-2 border-b"><?php echo $index + 1; ?></td>
                                <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($notice['title']); ?></td>
                                <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($notice['description']); ?></td>
                                <td class="px-4 py-2 border-b">
                                    <img src="<?php echo htmlspecialchars($notice['image_path']); ?>" alt="Notice Image" class="w-20">
                                </td>
                                <td class="px-4 py-2 border-b">
                                    <a href="edit_notice.php?id=<?php echo $notice['id']; ?>" class="text-blue-500 hover:underline">Edit</a> |
                                    <a href="manage_notice.php?delete_id=<?php echo $notice['id']; ?>" onclick="return confirm('Are you sure?')" class="text-red-500 hover:underline">Delete</a>
                                </td>
                                <td class="px-4 py-2 border-b">
                                    <input type="checkbox" name="selected_notices[]" value="<?php echo $notice['id']; ?>" class="form-checkbox h-5 w-5 text-blue-500">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        <?php else: ?>
            <p class="text-center text-gray-600 mt-6">No notices found.</p>
        <?php endif; ?>
        <p class="mt-4 text-center"><a href="index.php" class="text-blue-500 hover:underline">Go back</a></p>
    </div>

    <script>
        // Select All functionality
        document.getElementById('select-all').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('input[name="selected_notices[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>
</body>
</html>
