<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['notice_image'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $file = $_FILES['notice_image'];

    // Validate inputs
    if (empty($title) || empty($description) || empty($file['name'])) {
        echo "All fields are required!";
        exit();
    }

    // Validate file type and size
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes) || $file['size'] > 2 * 1024 * 1024) {
        echo "Invalid file type or size exceeds 2MB.";
        exit();
    }

    // Save the file
    $uploadDir = 'uploads/';
    $imagePath = $uploadDir . time() . '_' . basename($file['name']);
    if (!move_uploaded_file($file['tmp_name'], $imagePath)) {
        echo "Error uploading the file.";
        exit();
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO notices (title, description, image_path) VALUES (?, ?, ?)");
    if ($stmt->execute([$title, $description, $imagePath])) {
        header("Location: view_notices.php?message=Notice uploaded successfully!");
        exit();
    } else {
        echo "Error uploading notice.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Notice</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        input, textarea, button { width: 100%; margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #4CAF50; color: white; cursor: pointer; }
        button:hover { background: #45a049; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Upload Notice</h1>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Title" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="file" name="notice_image" accept="image/*" required>
            <button type="submit">Upload</button>
        </form>
    </div>
</body>
</html>
