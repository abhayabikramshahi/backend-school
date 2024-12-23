<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image = $_FILES['image'];

    if ($image['error'] === 0) {
        $imageName = time() . '-' . basename($image['name']);
        $targetDir = "uploads/";
        $targetFile = $targetDir . $imageName;

        if (move_uploaded_file($image['tmp_name'], $targetFile)) {
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                // Update existing notice
                $id = $_POST['id'];
                $stmt = $conn->prepare("UPDATE notices SET title=?, description=?, image_path=? WHERE id=?");
                $stmt->bind_param("sssi", $title, $description, $targetFile, $id);
                $stmt->execute();
            } else {
                // Insert new notice
                $stmt = $conn->prepare("INSERT INTO notices (title, description, image_path) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $title, $description, $targetFile);
                $stmt->execute();
            }

            header("Location: notice.php");
            exit();
        } else {
            echo "Error uploading the image.";
        }
    } else {
        echo "No image uploaded or an error occurred.";
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM notices WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: notice.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Notice</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Upload Notice</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" id="notice-id">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>
        <label for="description">Description:</label>
        <textarea name="description" id="description" rows="5" required></textarea>
        <label for="image">Image:</label>
        <input type="file" name="image" id="image" accept="image/*" required>
        <button type="submit">Submit</button>
    </form>
</div>
</body>
</html>
