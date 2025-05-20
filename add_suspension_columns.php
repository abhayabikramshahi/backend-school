<?php
/**
 * Add Suspension Columns to Users Table
 * 
 * This script adds the necessary columns to the users table to support user suspension functionality.
 */

// Include database connection
require_once __DIR__ . '/config/database.php';

// Initialize Database
$db = Database::getInstance();
$conn = $db->getConnection();

echo "Starting migration to add suspension columns to users table...\n";

// Check if is_suspended column exists
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'is_suspended'");
$is_suspended_exists = $result->num_rows > 0;

// Check if suspend_until column exists
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'suspend_until'");
$suspend_until_exists = $result->num_rows > 0;

// Add is_suspended column if it doesn't exist
if (!$is_suspended_exists) {
    echo "Adding 'is_suspended' column to users table...\n";
    try {
        $conn->exec("ALTER TABLE users ADD COLUMN is_suspended TINYINT(1) NOT NULL DEFAULT 0");
        echo "'is_suspended' column added successfully.\n";
    } catch (Exception $e) {
        echo "Error adding 'is_suspended' column: " . $e->getMessage() . "\n";
    }
} else {
    echo "'is_suspended' column already exists.\n";
}

// Add suspend_until column if it doesn't exist
if (!$suspend_until_exists) {
    echo "Adding 'suspend_until' column to users table...\n";
    try {
        $conn->exec("ALTER TABLE users ADD COLUMN suspend_until DATETIME NULL");
        echo "'suspend_until' column added successfully.\n";
    } catch (Exception $e) {
        echo "Error adding 'suspend_until' column: " . $e->getMessage() . "\n";
    }
} else {
    echo "'suspend_until' column already exists.\n";
}

echo "Migration completed.\n";
?>