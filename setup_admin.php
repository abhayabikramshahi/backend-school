<?php
/**
 * Admin Setup Script
 * 
 * This script ensures that an admin user exists in the database
 * with username 'admin' and password 'admin'
 */

// Include database connection
require_once __DIR__ . '/config/database.php';

// Initialize Database
$db = Database::getInstance();

// Check if admin user exists
$adminUser = $db->getRecord("SELECT id FROM users WHERE username = 'admin'");

if ($adminUser) {
    // Update existing admin user
    $hashedPassword = password_hash('admin', PASSWORD_DEFAULT);
    $db->update(
        'users',
        [
            'password' => $hashedPassword,
            'role' => 'admin'
        ],
        ['id' => $adminUser['id']]
    );
    echo "Admin user updated successfully with username 'admin' and password 'admin'";
} else {
    // Create new admin user
    $userData = [
        'username' => 'admin',
        'password' => password_hash('admin', PASSWORD_DEFAULT),
        'role' => 'admin'
    ];
    
    $result = $db->insert('users', $userData);
    
    if ($result) {
        echo "Admin user created successfully with username 'admin' and password 'admin'";
    } else {
        echo "Failed to create admin user. Please check database connection.";
    }
}