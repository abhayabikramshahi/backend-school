<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Handle form submission to upload the vacancy
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['vacancy_image'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $imagePath = 'uploads/' . basename($_FILES['vacancy_image']['name']);

    // Validate image file type (optional)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $imageType = $_FILES['vacancy_image']['type'];
    if (!in_array($imageType, $allowedTypes)) {
        echo "Error: Only JPEG, PNG, and GIF images are allowed.";
    } else {
        // Upload the image
        if (move_uploaded_file($_FILES['vacancy_image']['tmp_name'], $imagePath)) {
            // Insert the vacancy into the database
            $stmt = $conn->prepare("INSERT INTO vacancies (title, description, image_path) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $title, $description, $imagePath);
            if ($stmt->execute()) {
                echo "<div class='text-green-500'>Vacancy uploaded successfully!</div>";
            } else {
                echo "<div class='text-red-500'>Error uploading vacancy: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } else {
            echo "<div class='text-red-500'>Error uploading image.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Vacancy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <div class="max-w-4xl mx-auto py-6 px-4 bg-white rounded-lg shadow-md mt-6">
        <h1 class="text-3xl font-bold text-blue-600 mb-6">Upload a New Vacancy</h1>

        <form action="upload_vacancy.php" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="title" class="block text-gray-700">Title</label>
                <input type="text" name="title" class="w-full p-2 border border-gray-300 rounded-md" required><br><br>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700">Description</label>
                <textarea name="description" class="w-full p-2 border border-gray-300 rounded-md" required></textarea><br><br>
            </div>

            <div class="mb-4">
                <label for="vacancy_image" class="block text-gray-700">Upload Image</label>
                <input type="file" name="vacancy_image" class="w-full p-2 border border-gray-300 rounded-md" required><br><br>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Upload Vacancy</button>
        </form>

        <p class="mt-4 text-center"><a href="index.php" class="text-blue-500 hover:underline">Go back</a></p>
    </div>

</body>
</html>
