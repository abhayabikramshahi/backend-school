<?php
// Database credentials
$host = 'localhost'; // Your database host, e.g., localhost
$dbname = 'look'; // Your database name
$username = 'root'; // Your database username (default for XAMPP is 'root')
$password = ''; // Your database password (default for XAMPP is empty)

// PDO connection setup
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname: " . $e->getMessage());
}

// Fetch all students' results
$sql = "SELECT * FROM student_results";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle update result
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $math_marks = $_POST['math_marks'];
    $science_marks = $_POST['science_marks'];
    $english_marks = $_POST['english_marks'];
    $total_marks = $math_marks + $science_marks + $english_marks;

    // Determine grade based on total marks
    $grade = '';
    if ($total_marks >= 250) {
        $grade = 'A';
    } elseif ($total_marks >= 200) {
        $grade = 'B';
    } else {
        $grade = 'C';
    }

    // Update query
    $sql = "UPDATE student_results SET math_marks = :math_marks, science_marks = :science_marks, english_marks = :english_marks, total_marks = :total_marks, grade = :grade WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':math_marks', $math_marks);
    $stmt->bindParam(':science_marks', $science_marks);
    $stmt->bindParam(':english_marks', $english_marks);
    $stmt->bindParam(':total_marks', $total_marks);
    $stmt->bindParam(':grade', $grade);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    echo "Result updated successfully!";
}

// Handle delete result
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Delete query
    $sql = "DELETE FROM student_results WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    echo "Result deleted successfully!";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Student Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-semibold text-center mb-6">Manage Student Results</h1>

    <!-- Results Table -->
    <table class="min-w-full table-auto bg-white shadow-md rounded-lg">
        <thead>
            <tr class="bg-gray-200 text-gray-700">
                <th class="px-4 py-2 border">SN</th>
                <th class="px-4 py-2 border">Name</th>
                <th class="px-4 py-2 border">Class</th>
                <th class="px-4 py-2 border">Total Marks</th>
                <th class="px-4 py-2 border">Grade</th>
                <th class="px-4 py-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $index => $student): ?>
                <tr class="text-center">
                    <td class="px-4 py-2 border"><?= $index + 1 ?></td>
                    <td class="px-4 py-2 border"><?= htmlspecialchars($student['student_name']) ?></td>
                    <td class="px-4 py-2 border"><?= htmlspecialchars($student['class']) ?></td>
                    <td class="px-4 py-2 border"><?= htmlspecialchars($student['total_marks']) ?></td>
                    <td class="px-4 py-2 border"><?= htmlspecialchars($student['grade']) ?></td>
                    <td class="px-4 py-2 border">
                        <!-- Edit Button -->
                        <button data-id="<?= $student['id'] ?>" data-name="<?= $student['student_name'] ?>" data-class="<?= $student['class'] ?>" data-math="<?= $student['math_marks'] ?>" data-science="<?= $student['science_marks'] ?>" data-english="<?= $student['english_marks'] ?>" class="edit-btn bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">Edit</button>
                        <!-- Delete Button -->
                        <a href="?delete=<?= $student['id'] ?>" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Edit Result Form Modal -->
    <div id="edit-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/2">
            <h2 class="text-2xl mb-4">Edit Student Result</h2>
            <form method="POST" action="manage_results.php">
                <input type="hidden" name="id" id="edit-id">
                <div class="mb-4">
                    <label for="edit-name" class="block text-gray-700">Name</label>
                    <input type="text" id="edit-name" name="name" class="w-full px-4 py-2 border-gray-300 rounded-md" disabled>
                </div>
                <div class="mb-4">
                    <label for="edit-class" class="block text-gray-700">Class</label>
                    <input type="text" id="edit-class" name="class" class="w-full px-4 py-2 border-gray-300 rounded-md" disabled>
                </div>
                <div class="mb-4">
                    <label for="edit-math" class="block text-gray-700">Math Marks</label>
                    <input type="number" id="edit-math" name="math_marks" class="w-full px-4 py-2 border-gray-300 rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="edit-science" class="block text-gray-700">Science Marks</label>
                    <input type="number" id="edit-science" name="science_marks" class="w-full px-4 py-2 border-gray-300 rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="edit-english" class="block text-gray-700">English Marks</label>
                    <input type="number" id="edit-english" name="english_marks" class="w-full px-4 py-2 border-gray-300 rounded-md" required>
                </div>
                <button type="submit" name="update" class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600">Update Result</button>
            </form>
            <button id="close-modal" class="bg-gray-300 text-black px-4 py-2 mt-4 rounded-md hover:bg-gray-400">Close</button>
        </div>
    </div>
</div>

<script>
// Open the edit modal when "Edit" button is clicked
const editButtons = document.querySelectorAll('.edit-btn');
editButtons.forEach(button => {
    button.addEventListener('click', () => {
        const studentData = button.dataset;
        document.getElementById('edit-id').value = studentData.id;
        document.getElementById('edit-name').value = studentData.name;
        document.getElementById('edit-class').value = studentData.class;
        document.getElementById('edit-math').value = studentData.math;
        document.getElementById('edit-science').value = studentData.science;
        document.getElementById('edit-english').value = studentData.english;
        document.getElementById('edit-modal').classList.remove('hidden');
    });
});

// Close the modal
document.getElementById('close-modal').addEventListener('click', () => {
    document.getElementById('edit-modal').classList.add('hidden');
});
</script>

</body>
</html>
