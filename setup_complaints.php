<?php
/**
 * Setup Complaints System
 * 
 * This file creates the necessary database table for the complaints/gunaso system
 */

// Include database connection
require_once __DIR__ . '/config/database.php';

// Get database instance
$db = Database::getInstance();
$conn = $db->getConnection();

try {
    // Create complaints table
    $sql = "CREATE TABLE IF NOT EXISTS complaints (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        status ENUM('pending', 'in_progress', 'resolved') DEFAULT 'pending',
        is_anonymous BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    $conn->exec($sql);
    echo "<p>Complaints table created successfully!</p>";
    
    echo "<p>Setup completed successfully. <a href='index.php'>Go to homepage</a></p>";
} catch (PDOException $e) {
    echo "<p>Error creating complaints table: " . $e->getMessage() . "</p>";
}
?>