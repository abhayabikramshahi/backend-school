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
            <!-- Logo -->
            <div class="logo text-xl font-bold">
                <h1>Badimalika</h1>
            </div>
            <!-- Navigation Items -->
            <ul class="hidden md:flex space-x-6">
                <li><a href="https://badimalikasecschool.netlify.app/" class="hover:underline">Home</a></li>
                <li><a href="https://badimalikasecschool.netlify.app/about-us/about" class="hover:underline">About Us</a></li>
                <li><a href="https://badimalikasecschool.netlify.app/gallery/gallery" class="hover:underline">Gallery</a></li>
                <li><a href="/notice/nottice.html" class="hover:underline">Notices</a></li>
                <li><a href="/Vacancy/Vacancy.html" class="hover:underline">Vacancy</a></li>
                <li><a href="https://badimalikasecschool.netlify.app/achievements/achievements" class="hover:underline">Achievements</a></li>
            </ul>
            <!-- Contact Us Button -->
            <button id="main" class="hidden md:block bg-yellow-400 text-black px-4 py-2 rounded-lg hover:bg-yellow-500">
                Contact Us
            </button>
            <!-- Hamburger Menu -->
            <button class="md:hidden flex flex-col space-y-1" id="hamburger">
                <div class="w-6 h-1 bg-white"></div>
                <div class="w-6 h-1 bg-white"></div>
                <div class="w-6 h-1 bg-white"></div>
            </button>
        </div>
        <!-- Mobile Menu -->
        <div class="mobile-menu hidden flex flex-col space-y-4 bg-blue-700 text-white py-4 px-6 md:hidden">
            <a href="https://badimalikasecschool.netlify.app/" class="hover:underline">Home</a>
            <a href="https://badimalikasecschool.netlify.app/about-us/about" class="hover:underline">About Us</a>
            <a href="https://badimalikasecschool.netlify.app/gallery/gallery" class="hover:underline">Gallery</a>
            <a href="/notice/nottice.html" class="hover:underline">Notices</a>
            <a href="/Vacancy/Vacancy.html" class="hover:underline">Vacancy</a>
            <a href="https://badimalikasecschool.netlify.app/achievements/achievements" class="hover:underline">Achievements</a>
            <button id="mobile-main" class="bg-yellow-400 text-black px-4 py-2 rounded-lg hover:bg-yellow-500">
                Contact Us
            </button>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container mx-auto mt-8 p-6 bg-white shadow-lg rounded-lg">
        <header class="text-center">
            <h1 class="text-2xl font-bold text-blue-600">Digital Portal of Badimalika Secondary School</h1>
            <p class="mt-5 text-red-500">The Digital Portal of Badimalika Secondary School is an innovative platform designed to streamline access to school information and foster communication between students, teachers, parents, and the administration. This portal serves as a one-stop solution for the digital transformation of the school's traditional processes.</p>
        </header>

        <!-- Navigation Links -->
        <nav class="mt-6">
            <ul class="space-y-4">
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="welcome.php" class="text-blue-500 hover:underline">Dashboard</a></li>
                    <li><a href="notice.php" class="text-blue-500 hover:underline">Notices</a></li>
                    <li><a href="vacancy.php" class="text-blue-500 hover:underline">Vacancies</a></li>
                    <li><a href="upload_notice.php" class="text-blue-500 hover:underline">Upload Notice</a></li>
                    <li><a href="upload_vacancy.php" class="text-blue-500 hover:underline">Upload Vacancy</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="text-blue-500 hover:underline">Login</a></li>
                    <li><a href="signup.php" class="text-blue-500 hover:underline">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="mt-6">
            <p class="text-gray-700">
                <?php if (isset($_SESSION['username'])): ?>
                    Hello, <strong class="text-blue-600"><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! 
                    <br><br>
                    <div class="flex space-x-4">
                        <button onclick="window.location.href='welcome.php'" 
                            class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                            Go to Dashboard
                        </button>
                        <button onclick="window.location.href='logout.php'" 
                            class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                            Logout
                        </button>
                    </div>
                <?php else: ?>
                    Welcome! Please log in or sign up to access the system.
                <?php endif; ?>
            </p>
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-blue-600 text-white py-4 mt-6">
        <p class="text-center">&copy; 2024 User System. All rights reserved.</p>
    </footer>

    <!-- JavaScript -->
    <script>
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.querySelector('.mobile-menu');

        // Toggle visibility of mobile menu on click
        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
