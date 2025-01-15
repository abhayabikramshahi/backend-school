<?php
// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=look", "root", "");



// Fetch notices
$stmt = $pdo->query("SELECT * FROM notices ORDER BY created_at DESC");
$notices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include the navbar
include 'navbar.php'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Notices</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">




<!-- Notices Section -->
<div class="container mx-auto p-6 bg-white shadow-md rounded-md mt-6">
    <h1 class="text-3xl font-bold text-blue-600 mb-6 text-center">Notices</h1>
    <?php if (!empty($notices)): ?>
        <div class="space-y-6">
            <?php foreach ($notices as $notice): ?>
                <div class="flex flex-col md:flex-row items-center md:items-start border-b pb-6">
                    <!-- Image -->
                    <?php if (!empty($notice['image_path'])): ?>
                        <div class="md:w-1/3 mb-4 md:mb-0">
                            <img src="<?php echo htmlspecialchars($notice['image_path']); ?>" alt="Notice Image" class="w-full h-48 object-cover rounded-md shadow-md">
                        </div>
                    <?php endif; ?>

                    <!-- Text Content -->
                    <div class="md:w-2/3 md:ml-6">
                        <h2 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($notice['title']); ?></h2>
                        <p class="text-gray-700 mt-2"><?php echo nl2br(htmlspecialchars($notice['description'])); ?></p>
                        <p class="text-sm text-gray-500 mt-2">Posted on: <?php echo $notice['created_at']; ?></p>
                        <?php if (!empty($notice['file_path'])): ?>
                            <a href="<?php echo htmlspecialchars($notice['file_path']); ?>" download class="inline-block mt-4 px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600 transition">
                                Download Attachment
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-700 text-center">No notices available.</p>
    <?php endif; ?>
</div>

</body>
</html>
