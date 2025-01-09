<?php
function uploadResult($pdo, $class_name, $student_name, $roll_number, $subjects) {
    foreach ($subjects as $subject_name => $marks) {
        $optional = isset($marks['optional']) ? $marks['optional'] : null;
        $stmt = $pdo->prepare("INSERT INTO results (class_name, student_name, roll_number, subject_name, marks_obtained, total_marks, optional_subject)
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$class_name, $student_name, $roll_number, $subject_name, $marks['obtained'], $marks['total'], $optional]);
    }
    return "Results uploaded successfully for $class_name!";
}
?>
