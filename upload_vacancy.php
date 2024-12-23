<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE vacancies SET title=?, content=? WHERE id=?");
        $stmt->bind_param("ssi", $title, $content, $id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO vacancies (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);
        $stmt->execute();
    }

    header("Location: vacancy.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM vacancies WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: vacancy.php");
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
    <title>Upload Vacancy</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Manage Vacancies</h1>
    <form method="POST">
        <input type="hidden" name="id" id="vacancy-id">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>
        <label for="content">Content:</label>
        <textarea name="content" id="content" rows="5" required></textarea>
        <button type="submit">Submit</button>
    </form>

    <h2>Uploaded Vacancies</h2>
    <ul>
        <?php foreach ($vacancies as $index => $vacancy): ?>
            <li>
                <?php echo ($index + 1) . ". " . htmlspecialchars($vacancy['title']); ?>
                <a href="javascript:void(0);" onclick="editVacancy(<?php echo htmlspecialchars(json_encode($vacancy)); ?>)">Edit</a>
                <a href="?delete=<?php echo $vacancy['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script>
function editVacancy(vacancy) {
    document.getElementById('vacancy-id').value = vacancy.id;
    document.getElementById('title').value = vacancy.title;
    document.getElementById('content').value = vacancy.content;
}
</script>
</body>
</html>
