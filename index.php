<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - User System</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the external CSS -->
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome to the User System</h1>
        </header>
        <nav>
            <ul>
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="welcome.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <main>
            <p>
                <?php if (isset($_SESSION['username'])): ?>
                    Hello, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! You are logged in. Access your dashboard or log out.
                <?php else: ?>
                    Welcome! Please log in or sign up to access the system.
                <?php endif; ?>
            </p>
        </main>
        <footer>
            <p>&copy; 2024 User System. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
