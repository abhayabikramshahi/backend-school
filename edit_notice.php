<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM notices WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notice = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $description = $_POST['description'];

        if (isset($_FILES['notice_image']) && $_FILES['notice_image']['name']) {
            $imagePath = 'uploads/' . basename($_FILES['notice_image']['name']);
            move_uploaded_file($_FILES['notice_image']['tmp_name'], $imagePath);
        } else {
            $imagePath = $notice['image_path']; // Retain old image if no new image is uploaded
        }

        $stmt = $conn->prepare("UPDATE notices SET title = ?, description = ?, image_path = ? WHERE id = ?");
        $stmt->bind_param("sssi", $title, $description, $imagePath, $id);
        $stmt->execute();
        echo "Notice updated successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Notice</title>
    <link rel="shortcut icon" href="https://badimalikasecschool.netlify.app/471f74d9-7a7c-4024-82b7-251a5aba58a3.jpg" type="image/x-icon">
</head>
<body>
    <h1>Edit Notice</h1>
    <form action="edit_notice.php?id=<?php echo $notice['id']; ?>" method="POST" enctype="multipart/form-data">
        <label for="title">Title</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($notice['title']); ?>" required><br><br>

        <label for="description">Description</label>
        <textarea name="description" required><?php echo htmlspecialchars($notice['description']); ?></textarea><br><br>

        <label for="notice_image">Upload Image (optional)</label>
        <input type="file" name="notice_image"><br><br>

        <button type="submit">Update Notice</button>
    </form>
    <p><a href="manage_notice.php">Back to Manage Notices</a></p>
</body>
</html>
