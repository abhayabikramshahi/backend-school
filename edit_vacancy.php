<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM vacancies WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vacancy = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $description = $_POST['description'];

        if (isset($_FILES['vacancy_image']) && $_FILES['vacancy_image']['name']) {
            $imagePath = 'uploads/' . basename($_FILES['vacancy_image']['name']);
            move_uploaded_file($_FILES['vacancy_image']['tmp_name'], $imagePath);
        } else {
            $imagePath = $vacancy['image_path']; // Retain old image if no new image is uploaded
        }

        $stmt = $conn->prepare("UPDATE vacancies SET title = ?, description = ?, image_path = ? WHERE id = ?");
        $stmt->bind_param("sssi", $title, $description, $imagePath, $id);
        $stmt->execute();
        echo "Vacancy updated successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Vacancy</title>
    <link rel="shortcut icon" href="https://badimalikasecschool.netlify.app/471f74d9-7a7c-4024-82b7-251a5aba58a3.jpg" type="image/x-icon">
</head>
<body>
    <h1>Edit Vacancy</h1>
    <form action="edit_vacancy.php?id=<?php echo $vacancy['id']; ?>" method="POST" enctype="multipart/form-data">
        <label for="title">Title</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($vacancy['title']); ?>" required><br><br>

        <label for="description">Description</label>
        <textarea name="description" required><?php echo htmlspecialchars($vacancy['description']); ?></textarea><br><br>

        <label for="vacancy_image">Upload Image (optional)</label>
        <input type="file" name="vacancy_image"><br><br>

        <button type="submit">Update Vacancy</button>
    </form>
    <p><a href="manage_vacancy.php">Back to Manage Vacancies</a></p>
</body>
</html>
