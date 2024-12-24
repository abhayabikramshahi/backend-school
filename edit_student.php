<?php
// Include the database connection file
require 'db.php';

// Fetch student details based on the provided ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    die("Invalid student ID.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $class = $_POST['class'];
    $email = $_POST['email'];

    if (!empty($name) && !empty($class) && !empty($email)) {
        try {
            // Update student details
            $stmt = $pdo->prepare("UPDATE students SET name = ?, class = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $class, $email, $id]);

            // Redirect back to manage students page with success message
            header("Location: manage_student.php?success=Student updated successfully!");
            exit;
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    } else {
        $error_message = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Edit Student</h1>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($student['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="class" class="form-label">Class:</label>
            <input type="text" id="class" name="class" class="form-control" value="<?= htmlspecialchars($student['class']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($student['email']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Student</button>
    </form>
</div>
</body>
</html>
