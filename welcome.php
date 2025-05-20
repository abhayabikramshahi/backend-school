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
    <link rel="stylesheet" href="assets/css/school-theme.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="https://badimalikasecschool.netlify.app/471f74d9-7a7c-4024-82b7-251a5aba58a3.jpg" type="image/x-icon">
</head>
<body class="bg-white font-sans">

<div class="min-h-screen flex flex-col items-center justify-center">
    <!-- Header -->
    <header class="w-full bg-white border-b-2 border-black py-6 text-center shadow-md">
        <h1 class="text-3xl font-bold text-black">Welcome to Badimalika Secondary School</h1>
    </header>

    <!-- Welcome Section -->
    <div class="mt-10 bg-white rounded-lg border border-gray-200 shadow-md p-8 w-11/12 md:w-8/12 lg:w-6/12">
        <h2 class="text-2xl font-bold text-black mb-4">
            Welcome, <span class="text-black font-bold"><?php echo htmlspecialchars($_SESSION['username']); ?></span>!
        </h2>
        <p class="text-gray-700">You have successfully logged into the school portal.</p>
        <div class="mt-6 flex space-x-4">
            <a href="auth/logout.php" class="px-5 py-2 bg-white text-black border-2 border-black rounded hover:bg-gray-100 transition duration-300 font-medium">
                Logout
            </a>
            <a href="index.php" class="px-5 py-2 bg-black text-white rounded hover:bg-gray-800 transition duration-300 font-medium">
                Go to Home Page
            </a>
        </div>
    </div>

    <!-- About School Section -->
    <section class="mt-12 w-11/12 md:w-8/12 lg:w-6/12 bg-white rounded-lg border border-gray-200 shadow-md p-8">
        <h3 class="text-xl font-bold text-black mb-4 border-b border-gray-200 pb-2">About Badimalika Secondary School</h3>
        <p class="text-gray-700 leading-relaxed">
        Badimalika Secondary School, Kalikot is a public academic institute located in Syuna, Raskot, Kalikot district, Karnali Province of Nepal. It is affiliated to National Examinations Board (NEB) & Council for Technical Education and Vocation Training (CTEVT) and approved by Ministry of Education (MoE), Nepal. This secondary school offers Ten Plus Two programs under Management, Humanities, Education and Agriculture Streams.
        </p>
        <p class="text-gray-700 leading-relaxed mt-5">
        Badimalika Secondary School, Kalikot also offers vocational programs such as JTA in Plant Science for 40 seats each years. This secondary school has been offering agriculture programs such as Ten plus Two in Animal Science from 2015 AD. It has been imparting education with various facilities including scholarship.
        </p>
    </section>

    <!-- Footer -->
    <footer class="mt-16 w-full bg-white border-t-2 border-black py-4 text-black text-center shadow-md">
        <p>&copy; <?php echo date("Y"); ?> Badimalika Secondary School. All Rights Reserved.</p>
    </footer>
</div>

</body>
</html>

</body>
</html>
