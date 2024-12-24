<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['teachers'])) {
    $teachers = $_POST['teachers']; // Array of teachers

    if (is_array($teachers)) {
        $errorMessages = [];
        foreach ($teachers as $index => $teacher) {
            // Validate if required keys exist
            $name = isset($teacher['name']) ? $teacher['name'] : '';
            $role = isset($teacher['role']) ? $teacher['role'] : '';
            $email = isset($teacher['email']) ? $teacher['email'] : '';
            $phonenumber = isset($teacher['phonenumber']) ? $teacher['phonenumber'] : '';

            // Validate input to avoid empty entries
            if (!empty($name) && !empty($role) && !empty($email) && !empty($phonenumber)) {
                if ($stmt = $conn->prepare("INSERT INTO teachers (name, role, email, phonenumber) VALUES (?, ?, ?, ?)")) {
                    $stmt->bind_param("ssss", $name, $role, $email, $phonenumber);
                    if (!$stmt->execute()) {
                        $errorMessages[] = "Error inserting teacher at index $index: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $errorMessages[] = "Error preparing statement: " . $conn->error;
                }
            } else {
                $errorMessages[] = "Missing data for teacher at index $index. All fields are required.";
            }
        }

        if (empty($errorMessages)) {
            echo "Teachers added successfully!";
        } else {
            echo "Some errors occurred:<br>";
            echo implode("<br>", $errorMessages);
        }
    } else {
        echo "Invalid input format.";
    }
} else {
    echo "No teachers data provided.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Teachers</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-md">
        <h1 class="text-2xl font-bold text-blue-600">Upload Teachers</h1>
        <form method="POST" action="upload_teachers.php">
            <div id="teachers-list">
                <!-- Add Teacher Form Fields -->
                <div class="teacher-entry border-b pb-4 mb-4">
                    <label class="block mb-2">Name: 
                        <input type="text" name="teachers[0][name]" class="border px-2 py-1 rounded w-full" required>
                    </label>
                    <label class="block mb-2">Role: 
                        <input type="text" name="teachers[0][role]" class="border px-2 py-1 rounded w-full" required>
                    </label>
                    <label class="block mb-2">Email: 
                        <input type="email" name="teachers[0][email]" class="border px-2 py-1 rounded w-full" required>
                    </label>
                    <label class="block mb-2">Phone Number: 
                        <input type="text" name="teachers[0][phonenumber]" class="border px-2 py-1 rounded w-full" required>
                    </label>
                </div>
            </div>
            <button type="button" id="add-teacher" class="bg-green-500 text-white px-4 py-2 rounded-md">Add Another Teacher</button>
            <br><br>
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-md">Submit</button>
        </form>
    </div>

    <script>
        const teachersList = document.getElementById('teachers-list');
        const addTeacherButton = document.getElementById('add-teacher');
        let teacherIndex = 1;

        addTeacherButton.addEventListener('click', () => {
            if (teacherIndex < 50) {
                const teacherEntry = document.createElement('div');
                teacherEntry.classList.add('teacher-entry', 'border-b', 'pb-4', 'mb-4');
                teacherEntry.innerHTML = `
                    <label class="block mb-2">Name: 
                        <input type="text" name="teachers[${teacherIndex}][name]" class="border px-2 py-1 rounded w-full" required>
                    </label>
                    <label class="block mb-2">Role: 
                        <input type="text" name="teachers[${teacherIndex}][role]" class="border px-2 py-1 rounded w-full" required>
                    </label>
                    <label class="block mb-2">Email: 
                        <input type="email" name="teachers[${teacherIndex}][email]" class="border px-2 py-1 rounded w-full" required>
                    </label>
                    <label class="block mb-2">Phone Number: 
                        <input type="text" name="teachers[${teacherIndex}][phonenumber]" class="border px-2 py-1 rounded w-full" required>
                    </label>
                `;
                teachersList.appendChild(teacherEntry);
                teacherIndex++;
            } else {
                alert('You can only add up to 50 teachers at a time.');
            }
        });
    </script>
</body>
</html>
