<?php
include 'db.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['vacancy_image'])) {
    try {
        // Collect form data
        $title = $_POST['title'];
        $description = $_POST['description'];
        $imageName = basename($_FILES['vacancy_image']['name']);
        $imagePath = 'uploads/' . $imageName;

        // Validate file upload
        if ($_FILES['vacancy_image']['error'] === UPLOAD_ERR_OK) {
            // Move uploaded file to 'uploads' directory
            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true); // Create directory if not exists
            }

            if (move_uploaded_file($_FILES['vacancy_image']['tmp_name'], $imagePath)) {
                // Insert data into the database
                $stmt = $pdo->prepare("INSERT INTO vacancies (title, description, image_path) VALUES (:title, :description, :image_path)");
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':image_path', $imagePath);

                if ($stmt->execute()) {
                    echo "<script>alert('Vacancy uploaded successfully!'); window.location.href = 'view_vacancies.php';</script>";
                } else {
                    echo "Error: Could not save vacancy to the database.";
                }
            } else {
                echo "Error: Failed to move the uploaded file.";
            }
        } else {
            // Handle upload errors
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
                UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
                UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded.",
                UPLOAD_ERR_NO_FILE => "No file was uploaded.",
                UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
                UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
                UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload.",
            ];

            $errorCode = $_FILES['vacancy_image']['error'];
            $errorMessage = $errorMessages[$errorCode] ?? "Unknown error occurred during file upload.";
            echo "Error: " . $errorMessage;
        }
    } catch (Exception $e) {
        echo "An unexpected error occurred: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Vacancy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-lg w-full bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-3xl font-semibold text-blue-600 text-center mb-6">Upload Vacancy</h2>
        <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    placeholder="Enter vacancy title" 
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    required>
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea 
                    name="description" 
                    id="description" 
                    placeholder="Enter vacancy description" 
                    rows="4" 
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    required></textarea>
            </div>
            <div>
                <label for="vacancy_image" class="block text-sm font-medium text-gray-700 mb-2">Upload Image</label>
                <input 
                    type="file" 
                    name="vacancy_image" 
                    id="vacancy_image" 
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    required>
            </div>
            <button 
                type="submit" 
                class="w-full bg-blue-500 text-white py-3 rounded-lg font-medium hover:bg-blue-600 transition-colors duration-300">
                Upload Vacancy
            </button>
        </form>
    </div>
</body>
</html>

