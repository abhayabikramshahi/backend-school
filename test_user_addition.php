<?php
/**
 * Test User Addition Script
 * 
 * This script tests the user addition functionality to identify any issues.
 */

// Include database connection
require_once __DIR__ . '/config/database.php';

// Initialize Database
$db = Database::getInstance();

echo "Starting user addition test...\n";

// Test user data
$testUser = [
    'username' => 'testuser_' . time(), // Ensure unique username
    'password' => password_hash('testpassword', PASSWORD_DEFAULT),
    'role' => 'student',
    'is_suspended' => 0,
    'suspend_until' => null
];

echo "Attempting to add user: {$testUser['username']}\n";

// Try to insert the user
try {
    $result = $db->insert('users', $testUser);
    
    if ($result) {
        echo "SUCCESS: User added successfully with ID: {$result}\n";
        
        // Verify user was added
        $user = $db->getRecord("SELECT * FROM users WHERE id = ?", [$result]);
        if ($user) {
            echo "User verification successful. User exists in database.\n";
            echo "Username: {$user['username']}, Role: {$user['role']}\n";
        } else {
            echo "ERROR: User verification failed. User not found in database despite successful insert.\n";
        }
    } else {
        echo "ERROR: Failed to add user. Database insert returned false.\n";
    }
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
?>