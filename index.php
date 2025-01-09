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
<body class="bg-gray-100 font-sans">
    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-4 px-6 shadow-md sticky top-0 z-50">
        <div class="container mx-auto flex items-center justify-between">
            <div class="text-2xl font-extrabold">
                <h1>Badimalika School</h1>
            </div>
            <ul class="hidden md:flex space-x-6 text-sm font-medium">
                <li><a href="https://badimalikasecschool.netlify.app/" class="hover:underline">Home</a></li>
                <li><a href="https://badimalikasecschool.netlify.app/about-us/about" class="hover:underline">About Us</a></li>
                <li><a href="https://badimalikasecschool.netlify.app/gallery/gallery" class="hover:underline">Gallery</a></li>
                <li><a href="/notice/nottice.html" class="hover:underline">Notices</a></li>
                <li><a href="/Vacancy/Vacancy.html" class="hover:underline">Vacancy</a></li>
                <li><a href="https://badimalikasecschool.netlify.app/achievements/achievements" class="hover:underline">Achievements</a></li>
            </ul>
            <a href="contact.php" class="hidden md:block bg-yellow-400 text-black px-4 py-2 rounded-lg shadow hover:bg-yellow-500">Contact Us</a>
            <button class="md:hidden flex flex-col space-y-1" id="hamburger">
                <span class="w-6 h-1 bg-white"></span>
                <span class="w-6 h-1 bg-white"></span>
                <span class="w-6 h-1 bg-white"></span>
            </button>
        </div>
        <div class="mobile-menu hidden flex flex-col bg-blue-700 text-white py-4 px-6 md:hidden">
            <a href="https://badimalikasecschool.netlify.app/" class="hover:underline">Home</a>
            <a href="https://badimalikasecschool.netlify.app/about-us/about" class="hover:underline">About Us</a>
            <a href="https://badimalikasecschool.netlify.app/gallery/gallery" class="hover:underline">Gallery</a>
            <a href="/notice/nottice.html" class="hover:underline">Notices</a>
            <a href="/Vacancy/Vacancy.html" class="hover:underline">Vacancy</a>
            <a href="https://badimalikasecschool.netlify.app/achievements/achievements" class="hover:underline">Achievements</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative h-[80vh] bg-cover bg-center" style="background-image: url('https://badimalikasecschool.netlify.app/uploads/hero-backend.jpg');">
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="relative z-10 text-center flex flex-col items-center justify-center h-full text-white">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-4">Welcome to Badimalika Secondary School</h1>
            <p class="text-lg md:text-xl mb-6">Empowering the next generation through quality education and innovation.</p>
            <div class="space-x-4">
                <a href="#main-content" class="bg-yellow-400 text-black px-6 py-3 rounded-lg shadow hover:bg-yellow-500 transition">Learn More</a>
                <a href="/signup.php" class="bg-blue-600 px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition">Join Us</a>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div id="main-content" class="container mx-auto mt-8 p-8 bg-white rounded-xl shadow-lg">
        <header class="text-center">
            <h2 class="text-3xl font-bold text-blue-600">Digital Portal</h2>
            <p class="text-gray-600 text-lg mt-2">Streamlining communication and access to resources for students, parents, and staff.</p>
        </header>

        <nav class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php if (isset($_SESSION['username'])): ?>
                <a href="welcome.php" class="block bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-lg shadow-md hover:shadow-lg hover:from-blue-100 transition">
                    <h3 class="text-blue-600 font-bold text-xl">Dashboard</h3>
                    <p class="text-gray-600">Manage your tasks and view personalized updates.</p>
                </a>
                <a href="view_notices.php" class="block bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <h3 class="text-blue-600 font-bold text-xl">Notices</h3>
                    <p class="text-gray-600">Check the latest announcements.</p>
                </a>
                <!-- Add more links similarly -->
            <?php else: ?>
                <a href="login.php" class="block bg-blue-50 p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <h3 class="text-blue-600 font-bold text-xl">Login</h3>
                    <p class="text-gray-600">Access your account.</p>
                </a>
                <a href="register.php" class="block bg-blue-50 p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <h3 class="text-blue-600 font-bold text-xl">Sign Up</h3>
                    <p class="text-gray-600">Create a new account.</p>
                </a>
            <?php endif; ?>
        </nav>
    </div>

    <!-- JavaScript -->
    <script>
        document.getElementById('hamburger').addEventListener('click', () => {
            document.querySelector('.mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>
