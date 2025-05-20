<?php
include 'db.php'; // Include database connection

// Initialize variables
$id = $name = $role = $email = $phonenumber = '';
$errorMessage = $successMessage = '';

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Fetch teacher data
    $stmt = $pdo->prepare("SELECT * FROM teachers WHERE id = ?");
    $stmt->execute([$id]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($teacher) {
        // Populate variables with teacher data
        $name = $teacher['name'];
        $role = $teacher['role'];
        $email = $teacher['email'];
        $phonenumber = $teacher['phonenumber'];
    } else {
        $errorMessage = "Teacher not found.";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $role = $_POST['role'] ?? '';
    $email = $_POST['email'] ?? '';
    $phonenumber = $_POST['phonenumber'] ?? '';
    
    // Validate form data
    if (empty($name) || empty($role) || empty($email) || empty($phonenumber)) {
        $errorMessage = "All fields are required.";
    } else {
        try {
            // Update teacher data
            $stmt = $pdo->prepare("UPDATE teachers SET name = ?, role = ?, email = ?, phonenumber = ? WHERE id = ?");
            $stmt->execute([$name, $role, $email, $phonenumber, $id]);
            
            // Redirect to manage teachers page with success message
            header("Location: manage_teachers.php?success=Teacher updated successfully!");
            exit;
        } catch (PDOException $e) {
            $errorMessage = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Teacher - Badimalika Secondary School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="https://badimalikasecschool.netlify.app/471f74d9-7a7c-4024-82b7-251a5aba58a3.jpg" type="image/x-icon">
</head>
<body class="bg-gray-100 font-sans">
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-blue-600 mb-6">Edit Teacher</h1>
        
        <!-- Error Message -->
        <?php if (!empty($errorMessage)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $errorMessage; ?></span>
            </div>
        <?php endif; ?>
        
        <!-- Edit Form -->
        <form method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                    Name
                </label>
                <input 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                    id="name" 
                    type="text" 
                    name="name" 
                    value="<?php echo htmlspecialchars($name); ?>" 
                    required
                >
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="role">
                    Role
                </label>
                <input 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                    id="role" 
                    type="text" 
                    name="role" 
                    value="<?php echo htmlspecialchars($role); ?>" 
                    required
                >
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="<?php echo htmlspecialchars($email); ?>" 
                    required
                >
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="phonenumber">
                    Phone Number
                </label>
                <input 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                    id="phonenumber" 
                    type="text" 
                    name="phonenumber" 
                    value="<?php echo htmlspecialchars($phonenumber); ?>" 
                    required
                >
            </div>
            
            <div class="flex items-center justify-between">
                <button 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                    type="submit"
                >
                    Update Teacher
                </button>
                <a 
                    href="manage_teachers.php" 
                    class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800"
                >
                    Cancel
                </a>
            </div>
        </form>
        
        <!-- Footer -->
        <footer class="text-center mt-10 text-gray-500">
            &copy; 2024 Badimalika Secondary School. All rights reserved.
        </footer>
    </div>
</body>
</html>