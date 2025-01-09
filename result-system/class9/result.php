<?php
include '../manage/db.php';

$class_name = "Class 9";
$stmt = $pdo->prepare("SELECT * FROM results WHERE class_name = ? ORDER BY roll_number");
$stmt->execute([$class_name]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class 9 Results</title>
</head>
<body>
    <h1>Class 9 Results</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Roll Number</th>
                <th>Student Name</th>
                <th>Subject</th>
                <th>Marks Obtained</th>
                <th>Total Marks</th>
                <th>Optional Subject</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $result): ?>
                <tr>
                    <td><?php echo $result['roll_number']; ?></td>
                    <td><?php echo $result['student_name']; ?></td>
                    <td><?php echo $result['subject_name']; ?></td>
                    <td><?php echo $result['marks_obtained']; ?></td>
                    <td><?php echo $result['total_marks']; ?></td>
                    <td><?php echo $result['optional_subject']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
