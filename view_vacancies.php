<?php
include 'db.php';

// Fetch all vacancies
$result = $conn->query("SELECT * FROM vacancies ORDER BY created_at DESC");
$vacancies = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vacancies</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div class="max-w-4xl mx-auto py-6 px-4">
        <h1 class="text-3xl font-bold text-blue-600 mb-6">Vacancies</h1>

        <?php if ($vacancies): ?>
            <div class="space-y-6">
                <?php foreach ($vacancies as $vacancy): ?>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($vacancy['title']); ?></h3>
                        <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($vacancy['description']); ?></p>
                        <img src="<?php echo htmlspecialchars($vacancy['image_path']); ?>" class="mt-4 rounded-md" width="200" alt="Vacancy Image">
                        
                        <!-- Download button -->
                        <div class="mt-4">
                            <a href="<?php echo htmlspecialchars($vacancy['image_path']); ?>" download class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Download Image</a>
                        </div>

                        <div class="mt-4 text-sm text-gray-500">
                            <small>Posted on: <?php echo $vacancy['created_at']; ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-600 mt-6">No vacancies available at the moment.</p>
        <?php endif; ?>

        <div class="mt-6 text-center">
            <a href="index.php" class="text-blue-500 hover:underline">Go back</a>
        </div>
    </div>

</body>
</html>
