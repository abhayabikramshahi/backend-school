<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Navbar -->
        <nav class="bg-blue-600 text-white p-4 rounded-md shadow-md">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Badimalika Secondary School</h1>
                <ul class="flex space-x-4">
                    <li><a href="index.php" class="hover:text-gray-300">Home</a></li>
                    <li><a href="notices.php" class="hover:text-gray-300">Notices</a></li>
                    <li><a href="vacancy.php" class="hover:text-gray-300">Vacancy</a></li>
                    <li><a href="logout.php" class="hover:text-gray-300">Logout</a></li>
                </ul>
            </div>
        </nav>

        <!-- Welcome Section -->
        <div class="bg-white p-6 rounded-md shadow-md mt-6">
            <h1 class="text-3xl font-semibold text-gray-800">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
            <p class="mt-2 text-gray-600">You have successfully logged in.</p>
            
            <!-- Dashboard Links -->
            <div class="mt-6 flex space-x-4">
                <a href="index.php" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md shadow-md hover:bg-gray-400 transition duration-300">Home page</a>
            </div>
           
        </div>

      

        
</body>
</html>
