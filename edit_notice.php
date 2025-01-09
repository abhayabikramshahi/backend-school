<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit();
}

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM notices WHERE id = ?");
$stmt->execute([$id]);
$notice = $stmt->fetch();

if (!$notice) {
    echo "Notice not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $imagePath = $notice['image_path'];

    // Handle image upload
    if (isset($_FILES['notice_image']) && $_FILES['notice_image']['name']) {
        $imagePath = 'uploads/' . time() . '_' . basename($_FILES['notice_image']['name']);
        move_uploaded_file($_FILES['notice_image']['tmp_name'], $imagePath);
    }

    // Update notice
    $stmt = $pdo->prepare("UPDATE notices SET title = ?, description = ?, image_path = ? WHERE id = ?");
    if ($stmt->execute([$title, $description, $imagePath, $id])) {
        echo "Notice updated successfully!";
    } else {
        echo "Error updating notice.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Notice</title>
</head>
<body>
    <h1>Edit Notice</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" value="<?php echo htmlspecialchars($notice['title']); ?>" required>
        <textarea name="description" required><?php echo htmlspecialchars($notice['description']); ?></textarea>
        <input type="file" name="notice_image">
        <button type="submit">Update</button>
    </form>
</body>
</html>
