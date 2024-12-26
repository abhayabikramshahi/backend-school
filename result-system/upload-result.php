<?php
// Database credentials
$host = 'localhost';
$dbname = 'look'; 
$username = 'root'; 
$password = ''; 

// PDO connection setup
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data for all students
    $students_data = $_POST['students'];

    try {
        $stmt = $pdo->prepare("INSERT INTO student_results (class, roll_number, student_name, math_marks, science_marks, english_marks, total_marks, grade) 
                               VALUES (:class, :roll_number, :student_name, :math_marks, :science_marks, :english_marks, :total_marks, :grade)");

        foreach ($students_data as $student) {
            // Extract student data
            $class = $student['class'];
            $roll_number = $student['roll_number'];
            $student_name = $student['student_name'];
            $math_marks = $student['math_marks'];
            $science_marks = $student['science_marks'];
            $english_marks = $student['english_marks'];

            // Check if the student already exists
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM student_results WHERE class = :class AND roll_number = :roll_number");
            $check_stmt->bindParam(':class', $class);
            $check_stmt->bindParam(':roll_number', $roll_number);
            $check_stmt->execute();
            $count = $check_stmt->fetchColumn();

            if ($count > 0) {
                echo "Duplicate entry found for Class $class and Roll Number $roll_number. Skipping this student.<br>";
                continue;
            }

            // Calculate total marks and grade
            $total_marks = $math_marks + $science_marks + $english_marks;
            $grade = calculateGrade($total_marks);

            // Bind parameters and insert into the database
            $stmt->bindParam(':class', $class);
            $stmt->bindParam(':roll_number', $roll_number);
            $stmt->bindParam(':student_name', $student_name);
            $stmt->bindParam(':math_marks', $math_marks);
            $stmt->bindParam(':science_marks', $science_marks);
            $stmt->bindParam(':english_marks', $english_marks);
            $stmt->bindParam(':total_marks', $total_marks);
            $stmt->bindParam(':grade', $grade);

            $stmt->execute();
            echo "Result for $student_name uploaded successfully!<br>";
        }

    } catch (PDOException $e) {
        echo "Error inserting data: " . $e->getMessage();
    }
}

// Grade calculation function
function calculateGrade($total_marks) {
    if ($total_marks >= 270) {
        return "A+";
    } elseif ($total_marks >= 240) {
        return "A";
    } elseif ($total_marks >= 210) {
        return "B+";
    } elseif ($total_marks >= 180) {
        return "B";
    } elseif ($total_marks >= 150) {
        return "C+";
    } elseif ($total_marks >= 120) {
        return "C";
    } else {
        return "F";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Student Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-semibold text-center mb-6">Upload Student Results</h1>

    <!-- Upload Result Form -->
    <form method="POST" action="upload-result.php">
        <div id="students-container">
            <!-- Student Form Template -->
            <div class="student-form bg-white p-6 mb-4 shadow-md rounded-lg">
                <h2 class="text-xl font-semibold mb-2">Student 1</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="class" class="block font-medium">Class:</label>
                        <select name="students[0][class]" class="w-full border-gray-300 rounded-md" required>
                            <option value="class1">Class 1</option>
                            <option value="class2">Class 2</option>
                            <option value="class3">Class 3</option>
                            <!-- Add options for class 4 to class 12 -->
                            <option value="class12">Class 12</option>
                        </select>
                    </div>
                    <div>
                        <label for="roll_number" class="block font-medium">Roll Number:</label>
                        <input type="number" name="students[0][roll_number]" class="w-full border-gray-300 rounded-md" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="student_name" class="block font-medium">Student Name:</label>
                        <input type="text" name="students[0][student_name]" class="w-full border-gray-300 rounded-md" required>
                    </div>
                    <div>
                        <label for="math_marks" class="block font-medium">Math Marks:</label>
                        <input type="number" name="students[0][math_marks]" class="w-full border-gray-300 rounded-md" required>
                    </div>
                    <div>
                        <label for="science_marks" class="block font-medium">Science Marks:</label>
                        <input type="number" name="students[0][science_marks]" class="w-full border-gray-300 rounded-md" required>
                    </div>
                    <div>
                        <label for="english_marks" class="block font-medium">English Marks:</label>
                        <input type="number" name="students[0][english_marks]" class="w-full border-gray-300 rounded-md" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-between mb-4">
            <button type="button" id="add-student-btn" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Add Another Student</button>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Upload Results</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('add-student-btn').addEventListener('click', function() {
        let studentsContainer = document.getElementById('students-container');
        let studentForms = document.querySelectorAll('.student-form');
        let newStudentIndex = studentForms.length;

        // Clone the first student form and update the name attributes
        let newForm = studentForms[0].cloneNode(true);
        newForm.querySelector('h2').innerText = 'Student ' + (newStudentIndex + 1);

        // Update the name attributes of the input fields dynamically
        newForm.querySelector('select[name="students[0][class]"]').name = 'students[' + newStudentIndex + '][class]';
        newForm.querySelector('input[name="students[0][roll_number]"]').name = 'students[' + newStudentIndex + '][roll_number]';
        newForm.querySelector('input[name="students[0][student_name]"]').name = 'students[' + newStudentIndex + '][student_name]';
        newForm.querySelector('input[name="students[0][math_marks]"]').name = 'students[' + newStudentIndex + '][math_marks]';
        newForm.querySelector('input[name="students[0][science_marks]"]').name = 'students[' + newStudentIndex + '][science_marks]';
        newForm.querySelector('input[name="students[0][english_marks]"]').name = 'students[' + newStudentIndex + '][english_marks]';

        // Append the new form to the students container
        studentsContainer.appendChild(newForm);
    });
</script>

</body>
</html>
