<?php
include '../manage/db.php';
include '../manage/common.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = "Class 9";
    $student_name = $_POST['student_name'];
    $roll_number = $_POST['roll_number'];

    $subjects = [
        "Math" => ["obtained" => $_POST['math_marks'], "total" => $_POST['math_total']],
        "Science" => ["obtained" => $_POST['science_marks'], "total" => $_POST['science_total']],
        "English" => ["obtained" => $_POST['english_marks'], "total" => $_POST['english_total']],
        "Optional Subject" => ["obtained" => $_POST['optional_marks'], "total" => $_POST['optional_total'], "optional" => $_POST['optional_subject']],
    ];

    $message = uploadResult($pdo, $class_name, $student_name, $roll_number, $subjects);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Result for Class 9</title>
</head>
<body>
    <h1>Upload Result for Class 9</h1>
    <?php if (isset($message)) echo "<p>$message</p>"; ?>
    <form method="POST">
        <label>Student Name: <input type="text" name="student_name" required></label><br>
        <label>Roll Number: <input type="number" name="roll_number" required></label><br>
        <label>Math Marks: <input type="number" name="math_marks" required></label><br>
        <label>Math Total: <input type="number" name="math_total" required></label><br>
        <label>Science Marks: <input type="number" name="science_marks" required></label><br>
        <label>Science Total: <input type="number" name="science_total" required></label><br>
        <label>English Marks: <input type="number" name="english_marks" required></label><br>
        <label>English Total: <input type="number" name="english_total" required></label><br>
        <label>Optional Subject: <input type="text" name="optional_subject"></label><br>
        <label>Optional Marks: <input type="number" name="optional_marks"></label><br>
        <label>Optional Total: <input type="number" name="optional_total"></label><br>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
