<?php
$servername = "localhost";
$username = "root";  // or your database username
$password = "";      // or your database password
$dbname = "hlosir";  // replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
