<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Handle form submission to upload the notice
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['notice_image'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    
    // Validate inputs
    if (empty($title) || empty($description) || empty($_FILES['notice_image']['name'])) {
        echo "All fields are required!";
        exit();
    }

    $imagePath = 'uploads/' . basename($_FILES['notice_image']['name']);
    
    // Check if image is uploaded successfully
    if (move_uploaded_file($_FILES['notice_image']['tmp_name'], $imagePath)) {
        try {
            // Insert the notice into the database using PDO
            $stmt = $pdo->prepare("INSERT INTO notices (title, description, image_path) VALUES (?, ?, ?)");
            if ($stmt->execute([$title, $description, $imagePath])) {
                echo "Notice uploaded successfully!";
            } else {
                echo "Error uploading notice.";
            }
        } catch (PDOException $e) {
            // Handle database errors
            echo "Database error: " . $e->getMessage();
        }
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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            color: red;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Upload a New Notice</h1>
        
        <form action="upload_notice.php" method="POST" enctype="multipart/form-data">
            <label for="title">Title</label>
            <input type="text" name="title" required><br><br>
            
            <label for="description">Description</label>
            <textarea name="description" required></textarea><br><br>

            <label for="notice_image">Upload Image</label>
            <input type="file" name="notice_image" accept="image/*" required><br><br>

            <button type="submit">Upload Notice</button>
        </form>

        <!-- Display error messages -->
        <?php if (isset($error_message)): ?>
            <p class="message"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </div>

</body>
</html>
