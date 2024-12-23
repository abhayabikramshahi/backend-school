<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT * FROM notices ORDER BY created_at DESC");
$notices = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notices</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Notices</h1>
        <div class="notice-list">
            <?php foreach ($notices as $notice): ?>
                <div class="notice-card">
                    <img src="<?php echo htmlspecialchars($notice['image_path']); ?>" alt="Notice Image">
                    <h2><?php echo htmlspecialchars($notice['title']); ?></h2>
                    <p><?php echo htmlspecialchars($notice['description']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
