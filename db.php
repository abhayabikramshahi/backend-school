<?php
$servername = "localhost";
$username = "root";  // Update with your database username
$password = "";  // Update with your database password
$dbname = "maksm";  // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
