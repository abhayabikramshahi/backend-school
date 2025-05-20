<?php
// Include the database connection file
require 'db.php';

// Check if the teacher ID is provided in the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';  // Get the teacher ID

    // Ensure the teacher ID is not empty
    if (!empty($id)) {
        try {
            // Prepare and execute the SQL query to delete the teacher
            $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
            $stmt->execute([$id]);

            // Redirect back to the manage teachers page with success message
            header("Location: manage_teachers.php?success=Teacher deleted successfully!");
            exit;
        } catch (PDOException $e) {
            // Handle any errors during the database operation
            die("Database error: " . $e->getMessage());
        }
    } else {
        // If the teacher ID is missing, show an error
        die("Invalid teacher ID.");
    }
}
?>