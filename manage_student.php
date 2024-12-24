<?php
// Include the database connection file
require 'db.php';

// Fetch all students
try {
    $stmt = $pdo->query("SELECT * FROM students");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Manage Students</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Class</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($students as $student): ?>
            <tr>
                <td><?= htmlspecialchars($student['id']) ?></td>
                <td><?= htmlspecialchars($student['name']) ?></td>
                <td><?= htmlspecialchars($student['class']) ?></td>
                <td><?= htmlspecialchars($student['email']) ?></td>
                <td>
                    <a href="edit_student.php?id=<?= $student['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <form method="POST" action="delete_student.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $student['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="add_student.php" class="btn btn-success mt-3">Add New Student</a>
</div>
</body>
</html>
