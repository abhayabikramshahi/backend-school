<?php
// Database credentials
$host = 'localhost'; // Your database host
$dbname = 'look';  // Your database name
$username = 'root';  // Your database username (default for XAMPP is 'root')
$password = '';      // Your database password (default for XAMPP is empty)

// PDO connection setup
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname: " . $e->getMessage());
}

// Handle the search functionality
$search_query = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = $_GET['search'];
    $sql = "SELECT * FROM student_results WHERE student_name LIKE :search_query";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':search_query', '%' . $search_query . '%');
    $stmt->execute();
} else {
    // Fetch all results when no search query is provided
    $sql = "SELECT * FROM student_results";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-semibold text-center mb-6">Student Results</h1>

    <!-- Search Form -->
    <form method="GET" class="mb-6 flex justify-center">
        <input type="text" name="search" value="<?= htmlspecialchars($search_query) ?>" placeholder="Search by name" class="px-4 py-2 border rounded-md mr-2" />
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Search</button>
    </form>

    <!-- Results Table -->
    <table class="min-w-full table-auto bg-white shadow-md rounded-lg">
        <thead>
            <tr class="bg-gray-200 text-gray-700">
                <th class="px-4 py-2 border">SN</th>
                <th class="px-4 py-2 border">Name</th>
                <th class="px-4 py-2 border">Class</th>
                <th class="px-4 py-2 border">Total Marks</th>
                <th class="px-4 py-2 border">Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($students) > 0): ?>
                <?php foreach ($students as $index => $student): ?>
                    <tr class="text-center">
                        <td class="px-4 py-2 border"><?= $index + 1 ?></td>
                        <td class="px-4 py-2 border"><?= htmlspecialchars($student['student_name']) ?></td>
                        <td class="px-4 py-2 border"><?= htmlspecialchars($student['class']) ?></td>
                        <td class="px-4 py-2 border"><?= htmlspecialchars($student['total_marks']) ?></td>
                        <td class="px-4 py-2 border"><?= htmlspecialchars($student['grade']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="px-4 py-2 text-center">No results found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
