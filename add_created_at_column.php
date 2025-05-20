<?php
/**
 * Add Created_At Column to Users Table
 * 
 * This script adds the missing 'created_at' column to the users table
 * to fix the error when adding users.
 */

// Include database connection
require_once __DIR__ . '/config/database.php';

// Initialize Database
$db = Database::getInstance();
$conn = $db->getConnection();

echo "Starting migration to add created_at column to users table...\n";

// Check if created_at column exists
try {
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'created_at'");
    $created_at_exists = $result && $result->rowCount() > 0;

    // Add created_at column if it doesn't exist
    if (!$created_at_exists) {
        echo "Adding 'created_at' column to users table...\n";
        try {
            $conn->exec("ALTER TABLE users ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
            echo "'created_at' column added successfully.\n";
        } catch (Exception $e) {
            echo "Error adding 'created_at' column: " . $e->getMessage() . "\n";
        }
    } else {
        echo "'created_at' column already exists.\n";
    }

    echo "Migration completed.\n";
} catch (Exception $e) {
    echo "Error checking column existence: " . $e->getMessage() . "\n";
}
?>