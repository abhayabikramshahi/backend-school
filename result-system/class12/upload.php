<?php
// Include database connection
include '../manage/db.php';
include '../manage/common.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

// Initialize variables
$success_message = "";
$error_message = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $student_id = isset($_POST['student_id']) ? mysqli_real_escape_string($conn, $_POST['student_id']) : '';
    $student_name = isset($_POST['student_name']) ? mysqli_real_escape_string($conn, $_POST['student_name']) : '';
    $roll_number = isset($_POST['roll_number']) ? mysqli_real_escape_string($conn, $_POST['roll_number']) : '';
    $exam_type = isset($_POST['exam_type']) ? mysqli_real_escape_string($conn, $_POST['exam_type']) : '';
    $year = isset($_POST['year']) ? mysqli_real_escape_string($conn, $_POST['year']) : '';
    
    // Subject marks - adjusted for class 12
    $bangla = isset($_POST['bangla']) ? (int)$_POST['bangla'] : 0;
    $english = isset($_POST['english']) ? (int)$_POST['english'] : 0;
    $physics = isset($_POST['physics']) ? (int)$_POST['physics'] : 0;
    $chemistry = isset($_POST['chemistry']) ? (int)$_POST['chemistry'] : 0;
    $biology = isset($_POST['biology']) ? (int)$_POST['biology'] : 0;
    $math = isset($_POST['math']) ? (int)$_POST['math'] : 0;
    $ict = isset($_POST['ict']) ? (int)$_POST['ict'] : 0;
    
    // Calculate total and average
    $total_marks = $bangla + $english + $physics + $chemistry + $biology + $math + $ict;
    $total_subjects = 7; // Total number of subjects
    $average = $total_marks / $total_subjects;
    
    // Determine grade based on average
    $grade = calculateGrade($average);
    
    // Validate required fields
    if (empty($student_id) || empty($student_name) || empty($roll_number) || empty($exam_type) || empty($year)) {
        $error_message = "All fields are required!";
    } else {
        // Check if result already exists
        $check_query = "SELECT * FROM results WHERE student_id = '$student_id' AND exam_type = '$exam_type' AND year = '$year' AND class = 'class12'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            // Update existing result
            $update_query = "UPDATE results SET 
                student_name = '$student_name',
                roll_number = '$roll_number',
                bangla = $bangla,
                english = $english,
                physics = $physics,
                chemistry = $chemistry,
                biology = $biology,
                math = $math,
                ict = $ict,
                total_marks = $total_marks,
                average = $average,
                grade = '$grade'
                WHERE student_id = '$student_id' AND exam_type = '$exam_type' AND year = '$year' AND class = 'class12'";
            
            if (mysqli_query($conn, $update_query)) {
                $success_message = "Result updated successfully!";
            } else {
                $error_message = "Error updating result: " . mysqli_error($conn);
            }
        } else {
            // Insert new result
            $insert_query = "INSERT INTO results (student_id, student_name, roll_number, class, exam_type, year, bangla, english, physics, chemistry, biology, math, ict, total_marks, average, grade) 
                VALUES ('$student_id', '$student_name', '$roll_number', 'class12', '$exam_type', '$year', $bangla, $english, $physics, $chemistry, $biology, $math, $ict, $total_marks, $average, '$grade')";
            
            if (mysqli_query($conn, $insert_query)) {
                $success_message = "Result uploaded successfully!";
            } else {
                $error_message = "Error uploading result: " . mysqli_error($conn);
            }
        }
    }
}

// Function to calculate grade
function calculateGrade($average) {
    if ($average >= 80) {
        return 'A+';
    } elseif ($average >= 70) {
        return 'A';
    } elseif ($average >= 60) {
        return 'A-';
    } elseif ($average >= 50) {
        return 'B';
    } elseif ($average >= 40) {
        return 'C';
    } elseif ($average >= 33) {
        return 'D';
    } else {
        return 'F';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Result - Class 12</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }
        .container {
            max-width: 800px;
        }
        .form-group {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">Upload Result - Class 12</h2>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="student_id">Student ID</label>
                        <input type="text" class="form-control" id="student_id" name="student_id" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="student_name">Student Name</label>
                        <input type="text" class="form-control" id="student_name" name="student_name" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="roll_number">Roll Number</label>
                        <input type="text" class="form-control" id="roll_number" name="roll_number" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="exam_type">Exam Type</label>
                        <select class="form-control" id="exam_type" name="exam_type" required>
                            <option value="">Select Exam Type</option>
                            <option value="First Term">First Term</option>
                            <option value="Mid Term">Mid Term</option>
                            <option value="Final">Final</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="year">Year</label>
                        <select class="form-control" id="year" name="year" required>
                            <option value="">Select Year</option>
                            <?php
                            $current_year = date('Y');
                            for ($i = $current_year; $i >= $current_year - 5; $i--) {
                                echo "<option value=\"$i\">$i</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <h4 class="mt-4 mb-3">Subject Marks</h4>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="bangla">Bangla</label>
                        <input type="number" class="form-control" id="bangla" name="bangla" min="0" max="100" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="english">English</label>
                        <input type="number" class="form-control" id="english" name="english" min="0" max="100" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="physics">Physics</label>
                        <input type="number" class="form-control" id="physics" name="physics" min="0" max="100" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="chemistry">Chemistry</label>
                        <input type="number" class="form-control" id="chemistry" name="chemistry" min="0" max="100" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="biology">Biology</label>
                        <input type="number" class="form-control" id="biology" name="biology" min="0" max="100" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="math">Mathematics</label>
                        <input type="number" class="form-control" id="math" name="math" min="0" max="100" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="ict">ICT</label>
                        <input type="number" class="form-control" id="ict" name="ict" min="0" max="100" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">Upload Result</button>
                <a href="result.php" class="btn btn-secondary ml-2">View Results</a>
            </div>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>