<?php
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Handle result upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload'])) {
    $class = $_POST['class'];
    $student_name = $_POST['student_name'];
    $roll_number = $_POST['roll_number'];
    $math_marks = $_POST['math_marks'];
    $science_marks = $_POST['science_marks'];
    $english_marks = $_POST['english_marks'];

    $total_marks = $math_marks + $science_marks + $english_marks;
    $grade = calculateGrade($total_marks);

    $student_data = [
        'roll_number' => $roll_number,
        'name' => $student_name,
        'marks' => [
            'math' => $math_marks,
            'science' => $science_marks,
            'english' => $english_marks
        ],
        'total_marks' => $total_marks,
        'grade' => $grade
    ];

    // Write to the class-specific JSON file
    $file_path = "$class/students.json";
    if (file_exists('/class$/students.json')) {
        $json_data = file_get_contents($file_path);
        $students = json_decode($json_data, true);
    } else {
        $students = [];
    }

    $students[] = $student_data;
    file_put_contents($file_path, json_encode($students, JSON_PRETTY_PRINT));

    echo "Result uploaded successfully!";
}

// Function to calculate grade
function calculateGrade($total_marks) {
    if ($total_marks >= 240) return "A";
    if ($total_marks >= 180) return "B";
    if ($total_marks >= 120) return "C";
    return "D";
}

// Handle result deletion
if (isset($_GET['delete']) && isset($_GET['class']) && isset($_GET['roll_number'])) {
    $class = $_GET['class'];
    $roll_number = $_GET['roll_number'];

    $file_path = "$class/students.json";
    if (file_exists($file_path)) {
        $json_data = file_get_contents($file_path);
        $students = json_decode($json_data, true);

        // Find and remove the student
        foreach ($students as $key => $student) {
            if ($student['roll_number'] == $roll_number) {
                unset($students[$key]);
                break;
            }
        }

        // Save the updated data
        file_put_contents($file_path, json_encode(array_values($students), JSON_PRETTY_PRINT));
        echo "Result deleted successfully!";
    } else {
        echo "Class data not found.";
    }
}

// Handle result editing
if (isset($_GET['edit']) && isset($_GET['class']) && isset($_GET['roll_number'])) {
    $class = $_GET['class'];
    $roll_number = $_GET['roll_number'];
    $file_path = "$class/students.json";
    if (file_exists($file_path)) {
        $json_data = file_get_contents($file_path);
        $students = json_decode($json_data, true);

        foreach ($students as $key => $student) {
            if ($student['roll_number'] == $roll_number) {
                $edit_student = $student;
                break;
            }
        }
    }
}

// Save edited result
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    $class = $_POST['class'];
    $student_name = $_POST['student_name'];
    $roll_number = $_POST['roll_number'];
    $math_marks = $_POST['math_marks'];
    $science_marks = $_POST['science_marks'];
    $english_marks = $_POST['english_marks'];

    $total_marks = $math_marks + $science_marks + $english_marks;
    $grade = calculateGrade($total_marks);

    $student_data = [
        'roll_number' => $roll_number,
        'name' => $student_name,
        'marks' => [
            'math' => $math_marks,
            'science' => $science_marks,
            'english' => $english_marks
        ],
        'total_marks' => $total_marks,
        'grade' => $grade
    ];

    // Read existing students data
    $file_path = "$class/students.json";
    $json_data = file_get_contents($file_path);
    $students = json_decode($json_data, true);

    // Replace the student data with the new one
    foreach ($students as $key => $student) {
        if ($student['roll_number'] == $roll_number) {
            $students[$key] = $student_data;
            break;
        }
    }

    file_put_contents($file_path, json_encode($students, JSON_PRETTY_PRINT));
    echo "Result updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Results</title>
    <link rel="stylesheet" href="result.css">
</head>
<body>
    <h1>Admin Dashboard - Manage Student Results</h1>
    <a href="login.php?logout=true">Logout</a><br><br>

    <h2>Upload Student Results</h2>
    <form method="POST">
        <input type="text" name="student_name" placeholder="Student Name" required><br>
        <input type="number" name="roll_number" placeholder="Roll Number" required><br>
        <input type="number" name="math_marks" placeholder="Math Marks" required><br>
        <input type="number" name="science_marks" placeholder="Science Marks" required><br>
        <input type="number" name="english_marks" placeholder="English Marks" required><br>

        <label for="class">Select Class:</label>
        <select name="class" required>
            <option value="">Select Class</option>
            <option value="class1">Class 1</option>
            <option value="class2">Class 2</option>
            <option value="class3">Class 3</option>
            <!-- Add more classes as needed -->
        </select><br><br>

        <button type="submit" name="upload">Upload Result</button>
    </form>

    <h2>Edit or Delete Student Results</h2>
    <form method="GET" action="manage.php">
        <label for="edit">Edit / Delete Result</label><br>
        <input type="number" name="roll_number" placeholder="Enter Roll Number" required><br>
        <select name="class" required>
            <option value="">Select Class</option>
            <option value="class1">Class 1</option>
            <option value="class2">Class 2</option>
            <option value="class3">Class 3</option>
            <!-- Add more classes as needed -->
        </select><br><br>

        <button type="submit" name="edit">Edit Result</button>
        <button type="submit" name="delete">Delete Result</button>
    </form>
</body>
</html>
