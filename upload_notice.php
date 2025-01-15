<?php
// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=look", "root", "");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);

    // Handle image upload
    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $imagePath = $targetDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO notices (title, description, image_path) VALUES (?, ?, ?)");
    if ($stmt->execute([$title, $description, $imagePath])) {
        $success_message = "Notice uploaded successfully.";
    } else {
        $error_message = "Failed to upload notice.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Notice</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
        }
        .input, .textarea, .button {
            transition: all 0.3s ease;
        }
        .input:focus, .textarea:focus {
            border-color: #4C51BF;
            outline: none;
        }
        .button:hover {
            background-color: #4C51BF;
        }
    </style>
</head>
<body class="bg-gray-100">

    <div class="container p-8 bg-white shadow-lg rounded-lg mt-10">
        <h1 class="text-3xl font-bold text-indigo-600 mb-6 text-center">Upload Notice</h1>

        <?php if (isset($success_message)): ?>
            <div class="bg-green-500 text-white p-4 mb-6 rounded-lg shadow-md">
                <?php echo $success_message; ?>
            </div>
        <?php elseif (isset($error_message)): ?>
            <div class="bg-red-500 text-white p-4 mb-6 rounded-lg shadow-md">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-6">
                <label for="title" class="block text-lg font-semibold text-gray-700">Title</label>
                <input type="text" id="title" name="title" class="input w-full p-4 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500" required>
            </div>

            <div class="mb-6">
                <label for="description" class="block text-lg font-semibold text-gray-700">Description</label>
                <textarea id="description" name="description" class="textarea w-full p-4 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500" rows="6" required></textarea>
            </div>

            <div class="mb-6">
                <label for="image" class="block text-lg font-semibold text-gray-700">Image</label>
                <input type="file" id="image" name="image" class="input w-full p-4 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
            </div>

            <button type="submit" class="button w-full p-4 bg-indigo-600 text-white rounded-md shadow-md hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500">Upload Notice</button>
        </form>
    </div>

</body>
</html>
