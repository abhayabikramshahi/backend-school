<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize user inputs
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if both fields are filled
    if (empty($username) || empty($password)) {
        echo "Please fill out both fields.";
    } else {
        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the data to save to the PHP file
        $data = "<?php\n";
        $data .= "// User data\n";
        $data .= "\$username = '" . addslashes($username) . "';\n";
        $data .= "\$password = '" . addslashes($hashedPassword) . "';\n";
        $data .= "?>\n";

        // Save data to id_password.php
        $filePath = 'id_password.php';

        // Open the file and write the data (append mode)
        if (file_put_contents($filePath, $data, FILE_APPEND)) {
            echo "Signup successful! <a href='login.php'>Login here</a>";
        } else {
            echo "Error: Unable to save data.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="style.css"> <!-- Optional CSS for styling -->
</head>
<body>
    <div class="container">
        <h1>Signup</h1>
        <form method="POST" action="signup.php">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <br><br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <br><br>
            <button type="submit">Signup</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
