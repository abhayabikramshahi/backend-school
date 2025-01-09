<?php
include 'db.php';

// Fetch notices
$stmt = $pdo->query("SELECT * FROM notices ORDER BY created_at DESC");
$notices = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Notices</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto py-6">
        <h1 class="text-3xl font-bold mb-6">Notices</h1>
        <?php if ($notices): ?>
            <ul class="space-y-4">
                <?php foreach ($notices as $notice): ?>
                    <li class="bg-white p-4 rounded-lg shadow">
                        <h2 class="text-xl font-bold"><?php echo htmlspecialchars($notice['title']); ?></h2>
                        <p><?php echo htmlspecialchars($notice['description']); ?></p>
                        <?php if ($notice['image_path']): ?>
                            <img src="<?php echo htmlspecialchars($notice['image_path']); ?>" alt="Notice Image" class="mt-4">
                        <?php endif; ?>
                        <small>Posted on: <?php echo $notice['created_at']; ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No notices found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
