<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="https://badimalikasecschool.netlify.app/471f74d9-7a7c-4024-82b7-251a5aba58a3.jpg" type="image/x-icon">
    <title>Admin - Badimalika Secondary School</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-blue-600 text-white py-4 px-6">
        <div class="container mx-auto flex items-center justify-between">
            <div class="logo text-xl font-bold">
                <h1>Badimalika</h1>
            </div>
            <ul class="hidden md:flex space-x-6">
                <li><a href="https://badimalikasecschool.netlify.app/" class="hover:underline">Home</a></li>
                <li><a href="https://badimalikasecschool.netlify.app/about-us/about" class="hover:underline">About Us</a></li>
                <li><a href="https://badimalikasecschool.netlify.app/gallery/gallery" class="hover:underline">Gallery</a></li>
                <li><a href="/notice/nottice.html" class="hover:underline">Notices</a></li>
                <li><a href="/Vacancy/Vacancy.html" class="hover:underline">Vacancy</a></li>
                <li><a href="https://badimalikasecschool.netlify.app/achievements/achievements" class="hover:underline">Achievements</a></li>
            </ul>
            <button class="hidden md:block bg-yellow-400 text-black px-4 py-2 rounded-lg hover:bg-yellow-500">
                Contact Us
            </button>
            <button class="md:hidden flex flex-col space-y-1" id="hamburger">
                <div class="w-6 h-1 bg-white"></div>
                <div class="w-6 h-1 bg-white"></div>
                <div class="w-6 h-1 bg-white"></div>
            </button>
        </div>
        <div class="mobile-menu hidden flex flex-col space-y-4 bg-blue-700 text-white py-4 px-6 md:hidden">
            <a href="https://badimalikasecschool.netlify.app/" class="hover:underline">Home</a>
            <a href="https://badimalikasecschool.netlify.app/about-us/about" class="hover:underline">About Us</a>
            <a href="https://badimalikasecschool.netlify.app/gallery/gallery" class="hover:underline">Gallery</a>
            <a href="/notice/nottice.html" class="hover:underline">Notices</a>
            <a href="/Vacancy/Vacancy.html" class="hover:underline">Vacancy</a>
            <a href="https://badimalikasecschool.netlify.app/achievements/achievements" class="hover:underline">Achievements</a>
        </div>
    </nav>

  <!-- Hero Section -->
<section class="relative h-screen bg-cover bg-center" style="background-image: url('https://badimalikasecschool.netlify.app/uploads/hero-backend.jpg');">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    
    <!-- Content -->
    <div class="relative z-10 flex flex-col items-center justify-center text-center text-white h-full">
        <h1 class="text-5xl font-bold mb-4">Welcome to Badimalika Secondary School</h1>
        <p class="text-lg mb-6">Empowering the next generation through quality education and digital innovation.</p>
        <div class="flex space-x-4">
            <a href="#main-content" class="bg-yellow-400 text-black px-6 py-3 rounded-lg hover:bg-yellow-500 transition duration-300">Learn More</a>
            <a href="/signup.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300">Join Us</a>
        </div>
    </div>
</section>


   <!-- Main Content -->
<div id="main-content" class="container mx-auto mt-8 p-8 bg-white shadow-2xl rounded-xl">
    <!-- Header -->
    <header class="text-center">
        <h1 class="text-4xl font-bold text-blue-600 mb-4">Digital Portal of Badimalika Secondary School</h1>
        <p class="text-lg text-gray-600">A one-stop solution for streamlined communication and access to school resources for students, parents, and staff.</p>
    </header>

    <!-- Navigation Links -->
    <nav class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <?php if (isset($_SESSION['username'])): ?>
            <a href="welcome.php" class="block bg-blue-100 p-4 rounded-lg shadow hover:shadow-md hover:bg-blue-200 transition duration-300">
                <h2 class="text-blue-600 font-bold text-xl">Dashboard</h2>
                <p class="text-gray-600 mt-2">Access your personal dashboard and manage your tasks.</p>
            </a>
            <a href="view_notices.php" class="block bg-blue-100 p-4 rounded-lg shadow hover:shadow-md hover:bg-blue-200 transition duration-300">
                <h2 class="text-blue-600 font-bold text-xl">Notices</h2>
                <p class="text-gray-600 mt-2">View the latest notices and announcements.</p>
            </a>
            <a href="view_vacancies.php" class="block bg-blue-100 p-4 rounded-lg shadow hover:shadow-md hover:bg-blue-200 transition duration-300">
                <h2 class="text-blue-600 font-bold text-xl">Vacancies</h2>
                <p class="text-gray-600 mt-2">Check available job opportunities at the school.</p>
            </a>
            <a href="upload_notice.php" class="block bg-blue-100 p-4 rounded-lg shadow hover:shadow-md hover:bg-blue-200 transition duration-300">
                <h2 class="text-blue-600 font-bold text-xl">Upload Notice</h2>
                <p class="text-gray-600 mt-2">Publish new notices for students and staff.</p>
            </a>
            <a href="upload_vacancy.php" class="block bg-blue-100 p-4 rounded-lg shadow hover:shadow-md hover:bg-blue-200 transition duration-300">
                <h2 class="text-blue-600 font-bold text-xl">Upload Vacancy</h2>
                <p class="text-gray-600 mt-2">Post new job openings and requirements.</p>
            </a>
            <a href="teachers.php" class="block bg-blue-100 p-4 rounded-lg shadow hover:shadow-md hover:bg-blue-200 transition duration-300">
                <h2 class="text-blue-600 font-bold text-xl">List Of Teachers</h2>
                <p class="text-gray-600 mt-2">View the list of teachers.</p>
            </a>
            <a href="students.php" class="block bg-blue-100 p-4 rounded-lg shadow hover:shadow-md hover:bg-blue-200 transition duration-300">
                <h2 class="text-blue-600 font-bold text-xl">List of students</h2>
                <p class="text-gray-600 mt-2">View the list of students.</p>
            </a>
            </a>
            <a href="upload_students.php" class="block bg-blue-100 p-4 rounded-lg shadow hover:shadow-md hover:bg-blue-200 transition duration-300">
                <h2 class="text-blue-600 font-bold text-xl">Add Students</h2>
                <p class="text-gray-600 mt-2">Publish the list of students.</p>
            </a>
            <a href="upload_teachers.php" class="block bg-blue-100 p-4 rounded-lg shadow hover:shadow-md hover:bg-blue-200 transition duration-300">
                <h2 class="text-blue-600 font-bold text-xl">Add Teachers</h2>
                <p class="text-gray-600 mt-2">Publish the list of teachers.</p>
            </a>
        <?php else: ?>
            <a href="login.php" class="block bg-blue-100 p-4 rounded-lg shadow hover:shadow-md hover:bg-blue-200 transition duration-300">
                <h2 class="text-blue-600 font-bold text-xl">Login</h2>
                <p class="text-gray-600 mt-2">Log in to access your dashboard and features.</p>
            </a>
            <a href="signup.php" class="block bg-blue-100 p-4 rounded-lg shadow hover:shadow-md hover:bg-blue-200 transition duration-300">
                <h2 class="text-blue-600 font-bold text-xl">Sign Up</h2>
                <p class="text-gray-600 mt-2">Create an account to join the digital portal.</p>
            </a>
        <?php endif; ?>
    </nav>

    <!-- Main Content -->
    <main class="mt-10 text-center">
        
                <div class="flex justify-center space-x-4 mt-6">
                    <button onclick="window.location.href='welcome.php'" 
                            class="bg-green-500 text-white px-6 py-3 rounded-lg shadow hover:bg-green-600 transition duration-300">
                        Go to Dashboard
                    </button>
                    <button onclick="window.location.href='logout.php'" 
                            class="bg-red-500 text-white px-6 py-3 rounded-lg shadow hover:bg-red-600 transition duration-300">
                        Logout
                    </button>
                </div>
           
        </p>
    </main>
</div>

   

    <!-- JavaScript -->
    <script>
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.querySelector('.mobile-menu');

        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
