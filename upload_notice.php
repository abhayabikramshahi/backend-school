<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Please log in first.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image_path = 'uploads/' . basename($_FILES['image']['name']);

    // Move the uploaded image to the 'uploads' directory
    if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
        $stmt = $conn->prepare("INSERT INTO notices (title, description, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $description, $image_path);

        if ($stmt->execute()) {
            echo "Notice uploaded successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error uploading image.";
    }
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
        <form method="POST" action="upload_notice.php" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" required>
            <br><br>
            <label for="description">Description:</label>
            <textarea name="description" id="description" required></textarea>
            <br><br>
            <label for="image">Image:</label>
            <input type="file" name="image" id="image" required>
            <br><br>
            <button type="submit">Upload Notice</button>
        </form>
    </div>
</body>
</html>
