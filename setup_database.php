<?php
/**
 * Database Setup Script
 * 
 * This script applies all necessary database changes from the consolidated SQL file
 * and ensures proper database structure for the school management system.
 */

// Include database connection
require_once __DIR__ . '/config/database.php';

// Initialize Database
$db = Database::getInstance();
$conn = $db->getConnection();

echo "Starting database setup...\n";

try {
    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/database_setup.sql');
    
    // Split SQL file into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    // Execute each statement
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $conn->exec($statement);
            echo "Executed: " . substr(trim($statement), 0, 50) . "...\n";
        }
    }
    
    echo "\nDatabase setup completed successfully.\n";
    
    // Verify user table structure
    $checkColumns = $conn->query("SHOW COLUMNS FROM users");
    $columns = $checkColumns->fetchAll(PDO::FETCH_COLUMN);
    
    echo "\nVerifying users table structure:\n";
    $requiredColumns = ['id', 'username', 'password', 'role', 'is_suspended', 'suspend_until', 'suspend_reason', 'created_at'];
    
    foreach ($requiredColumns as $column) {
        if (in_array($column, $columns)) {
            echo "✓ Column '$column' exists\n";
        } else {
            echo "✗ Column '$column' is missing\n";
        }
    }
    
    echo "\nTo run this script, use: php setup_database.php\n";
    echo "After running this script, all database tables will be properly set up with the required columns.\n";
    
} catch (PDOException $e) {
    echo "Error setting up database: " . $e->getMessage() . "\n";
    exit(1);
}
?>