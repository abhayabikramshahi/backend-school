<?php
include 'db.php';

// Fetch all notices
$result = $conn->query("SELECT * FROM notices ORDER BY created_at DESC");
$notices = $result->fetch_all(MYSQLI_ASSOC);

// Handle file download
if (isset($_GET['download_id'])) {
    $downloadId = intval($_GET['download_id']);

    // Fetch the notice data
    $stmt = $conn->prepare("SELECT * FROM notices WHERE id = ?");
    $stmt->bind_param("i", $downloadId);
    $stmt->execute();
    $result = $stmt->get_result();
    $notice = $result->fetch_assoc();

    if ($notice) {
        // Set headers to initiate the download
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="notice_' . $notice['id'] . '.txt"');

        // Output the content of the notice
        echo "Title: " . $notice['title'] . "\n";
        echo "Description: " . $notice['description'] . "\n";
        echo "Posted on: " . $notice['created_at'] . "\n";

        exit();
    } else {
        echo "Notice not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notices</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="max-w-4xl mx-auto py-6 px-4">
        <h1 class="text-3xl font-bold text-blue-600 mb-6">Notices</h1>

        <?php if ($notices): ?>
            <ul class="space-y-6">
                <?php foreach ($notices as $notice): ?>
                    <li class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($notice['title']); ?></h3>
                        <p class="text-gray-700 mt-2"><?php echo htmlspecialchars($notice['description']); ?></p>
                        <img src="<?php echo htmlspecialchars($notice['image_path']); ?>" width="200" alt="Notice Image" class="mt-4 rounded-md">
                        <br><br>
                        <small class="text-gray-500">Posted on: <?php echo $notice['created_at']; ?></small>

                        <!-- Download Button -->
                        <div class="mt-4">
                            <a href="?<?php echo htmlspecialchars($notice['image_path']); ?>" download="<?php echo htmlspecialchars($notice['image_path']); ?>"
                               class="inline-block bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                               Download Notice
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-center text-gray-600 mt-6">No notices available at the moment.</p>
        <?php endif; ?>
    </div>
</body>
</html>
