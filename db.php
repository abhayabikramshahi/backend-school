<?php
$host = 'localhost';      // Database host (usually localhost)
$dbname = 'look';       // Database name
$username = 'root';       // Database username
$password = '';           // Database password (default is empty for XAMPP)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // For error handling
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
        // Database password (default is empty for XAMPP)

// Create a MySQLi connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set the character set (optional, but recommended for compatibility)
$conn->set_charset("utf8");

// If successful

?>


