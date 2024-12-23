<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT * FROM vacancies ORDER BY created_at DESC");
$vacancies = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacancies</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Vacancies</h1>
        <div class="vacancy-list">
            <?php foreach ($vacancies as $vacancy): ?>
                <div class="vacancy-card">
                    <img src="<?php echo htmlspecialchars($vacancy['image_path']); ?>" alt="Vacancy Image">
                    <h2><?php echo htmlspecialchars($vacancy['title']); ?></h2>
                    <p><?php echo htmlspecialchars($vacancy['description']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
