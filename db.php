<?php
$servername = "localhost";
$username = "root"; // Database username
$password = ""; // Database password
$database = "look"; // Database name

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
