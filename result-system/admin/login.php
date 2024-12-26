<?php
session_start();

// Admin login credentials (you can replace these with database queries in production)
$admin_username = 'admin';
$admin_password = 'password123'; // Use password hashing in production!

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate credentials
    if ($username == $admin_username && $password == $admin_password) {
        $_SESSION['is_admin'] = true; // Set session to indicate admin login
        header('Location: manage.php'); // Redirect to manage page after login
        exit();
    } else {
        echo "Invalid credentials. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<body>
    <h1>Admin Login</h1>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
