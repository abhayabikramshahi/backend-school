<?php
/**
 * Fix Created_At Column Script
 * 
 * This script fixes the users table structure by adding the missing 'created_at' column
 * which is causing errors when adding new users.
 */

// Include database connection
require_once __DIR__ . '/config/database.php';

// Initialize Database
$db = Database::getInstance();
$conn = $db->getConnection();

echo "Starting database fix for created_at column...\n";

// Check if created_at column exists in users table
$checkColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'created_at'");
$createdAtColumnExists = ($checkColumn && $checkColumn->rowCount() > 0);

if (!$createdAtColumnExists) {
    echo "Adding 'created_at' column to users table...\n";
    try {
        // Add created_at column to users table
        $conn->exec("ALTER TABLE users ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
        echo "'created_at' column added successfully.\n";
    } catch (PDOException $e) {
        echo "Error adding 'created_at' column: " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    echo "'created_at' column already exists.\n";
}

echo "Database fix completed.\n";

// Verify the fix by checking if the column exists now
$verifyColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'created_at'");
$columnExists = ($verifyColumn && $verifyColumn->rowCount() > 0);

if ($columnExists) {
    echo "Verification successful: 'created_at' column exists in the users table.\n";
} else {
    echo "Verification failed: 'created_at' column still does not exist in the users table.\n";
}

echo "\nTo run this script, use: php fix_created_at_column.php\n";
echo "After running this script, you should be able to add users without the 'Unknown column' error.\n";
?>