<?php
/**
 * Database Fix Script
 * 
 * This script fixes the users table structure by adding the missing 'role' column
 * and ensures that an admin user exists with the correct credentials.
 */

// Include database connection
require_once __DIR__ . '/config/database.php';

// Initialize Database
$db = Database::getInstance();
$conn = $db->getConnection();

echo "Starting database fix...\n";

// Check if role column exists in users table
$checkColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'role'");
$roleColumnExists = ($checkColumn && $checkColumn->rowCount() > 0);

if (!$roleColumnExists) {
    echo "Adding 'role' column to users table...\n";
    try {
        // Add role column to users table
        $conn->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'student'");
        $conn->exec("ALTER TABLE users ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
        echo "'role' column added successfully.\n";
    } catch (PDOException $e) {
        echo "Error adding 'role' column: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Check if admin user exists
$adminUser = $db->getRecord("SELECT id FROM users WHERE username = 'admin'");

if ($adminUser) {
    // Update existing admin user
    echo "Updating existing admin user...\n";
    $hashedPassword = password_hash('admin', PASSWORD_DEFAULT);
    $result = $db->update(
        'users',
        [
            'password' => $hashedPassword,
            'role' => 'admin'
        ],
        'id = ?',
        [$adminUser['id']]
    );
    
    if ($result) {
        echo "Admin user updated successfully with username 'admin' and password 'admin'\n";
    } else {
        echo "Failed to update admin user.\n";
    }
} else {
    // Create new admin user
    echo "Creating new admin user...\n";
    $userData = [
        'username' => 'admin',
        'password' => password_hash('admin', PASSWORD_DEFAULT),
        'role' => 'admin'
    ];
    
    $result = $db->insert('users', $userData);
    
    if ($result) {
        echo "Admin user created successfully with username 'admin' and password 'admin'\n";
    } else {
        echo "Failed to create admin user. Please check database connection.\n";
    }
}

echo "Database fix completed.\n";
echo "You can now log in with username 'admin' and password 'admin'\n";