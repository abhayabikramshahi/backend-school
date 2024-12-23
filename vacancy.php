<?php
include 'db.php';
$result = $conn->query("SELECT * FROM notices ORDER BY created_at DESC");
$notices = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacancy</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Vacancy</h1>
    <ul>
        <?php foreach ($notices as $index => $notice): ?>
            <li>
                <?php echo ($index + 1) . ". " . htmlspecialchars($notice['title']); ?>
                <p><?php echo htmlspecialchars($notice['content']); ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
