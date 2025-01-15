<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Badimalika Secondary School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="https://badimalikasecschool.netlify.app/471f74d9-7a7c-4024-82b7-251a5aba58a3.jpg" type="image/x-icon">
</head>
<body class="bg-gray-100 font-sans">

<div class="min-h-screen flex flex-col items-center justify-center">
    <!-- Header -->
    <header class="w-full bg-blue-600 py-4 text-white text-center">
        <h1 class="text-3xl font-bold">Welcome to Badimalika Secondary School</h1>
    </header>

    <!-- Welcome Section -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6 w-11/12 md:w-8/12 lg:w-6/12">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">
            Welcome, <span class="text-blue-600"><?php echo htmlspecialchars($_SESSION['username']); ?></span>!
        </h2>
        <p class="text-gray-600">You have successfully logged into the school portal.</p>
        <div class="mt-4 flex space-x-4">
            <a href="logout.php" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition">
                Logout
            </a>
            <a href="index.php" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                Go to Home Page
            </a>
        </div>
    </div>

    <!-- About School Section -->
    <section class="mt-12 w-11/12 md:w-8/12 lg:w-6/12 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-3">About Badimalika Secondary School</h3>
        <p class="text-gray-600 leading-relaxed">
        Badimalika Secondary School, Kalikot is a public academic institute located in Syuna, Raskot, Kalikot district, Karnali Province of Nepal. It is affiliated to National Examinations Board (NEB) & Council for Technical Education and Vocation Training (CTEVT) and approved by Ministry of Education (MoE), Nepal. This secondary school offers Ten Plus Two programs under Management, Humanities, Education and Agriculture Streams.
        </p>
        <p class="text-gray-600 leading-relaxed mt-5">
        Badimalika Secondary School, Kalikot also offers vocational programs such as JTA in Plant Science for 40 seats each years. This secondary school has been offering agriculture programs such as Ten plus Two in Animal Science from 2015 AD. It has been imparting education with various facilities including scholarship.
        </p>
    </section>

    <!-- Footer -->
    <footer class="mt-16 w-full bg-blue-600 py-4 text-white text-center">
        <p>&copy; <?php echo date("Y"); ?> Badimalika Secondary School. All Rights Reserved.</p>
    </footer>
</div>

</body>
</html>
