<?php
// Include the database connection file
require 'db.php';

// Check if the student ID is provided in the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';  // Get the student ID

    // Ensure the student ID is not empty
    if (!empty($id)) {
        try {
            // Prepare and execute the SQL query to delete the student
            $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            $stmt->execute([$id]);

            // Redirect back to the manage student page with success message
            header("Location: manage_student.php?success=Student deleted successfully!");
            exit;
        } catch (PDOException $e) {
            // Handle any errors during the database operation
            die("Database error: " . $e->getMessage());
        }
    } else {
        // If the student ID is missing, show an error
        die("Invalid student ID.");
    }
}
?>
