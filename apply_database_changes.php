<?php
/**
 * Apply Database Changes Script
 * 
 * This script applies all necessary database changes from the consolidated SQL file
 * and removes redundant SQL files after successful application.
 */

// Include database connection
require_once __DIR__ . '/config/database.php';

// Initialize Database
$db = Database::getInstance();
$conn = $db->getConnection();

echo "<h2>Applying Database Changes</h2>";

try {
    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/database_setup.sql');
    
    // Split SQL file into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    // Execute each statement
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $conn->exec($statement);
            echo "<p>Executed: " . substr(trim($statement), 0, 50) . "...</p>";
        }
    }
    
    echo "<p style='color: green; font-weight: bold;'>Database setup completed successfully.</p>";
    
    // Verify user table structure
    $checkColumns = $conn->query("SHOW COLUMNS FROM users");
    $columns = $checkColumns->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Verifying users table structure:</h3>";
    echo "<ul>";
    $requiredColumns = ['id', 'username', 'password', 'role', 'user_id', 'is_suspended', 'suspend_until', 'suspend_reason', 'is_banned', 'created_at'];
    
    foreach ($requiredColumns as $column) {
        if (in_array($column, $columns)) {
            echo "<li style='color: green;'>✓ Column '$column' exists</li>";
        } else {
            echo "<li style='color: red;'>✗ Column '$column' is missing</li>";
        }
    }
    echo "</ul>";
    
    // List of redundant SQL files to remove
    $redundantFiles = [
        __DIR__ . '/school_management.sql',
        __DIR__ . '/add_created_at_column.sql',
        __DIR__ . '/result-system/sql/create_results_table.sql',
        __DIR__ . '/result-system/sql/update_results_table.sql'
    ];
    
    echo "<h3>Cleaning up redundant SQL files:</h3>";
    echo "<ul>";
    foreach ($redundantFiles as $file) {
        if (file_exists($file)) {
            // Backup the file before removing
            $backupDir = __DIR__ . '/sql_backups';
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            
            $fileName = basename($file);
            $backupFile = $backupDir . '/' . $fileName;
            
            // Copy file to backup
            if (copy($file, $backupFile)) {
                echo "<li>Backed up: $fileName</li>";
                
                // Remove the original file
                if (unlink($file)) {
                    echo "<li style='color: green;'>Removed redundant file: $fileName</li>";
                } else {
                    echo "<li style='color: red;'>Failed to remove: $fileName</li>";
                }
            } else {
                echo "<li style='color: red;'>Failed to backup: $fileName</li>";
            }
        } else {
            echo "<li>File not found: $fileName</li>";
        }
    }
    echo "</ul>";
    
    echo "<p>All database changes have been applied successfully. The database structure has been consolidated into a single file: <strong>database_setup.sql</strong></p>";
    echo "<p>Redundant SQL files have been backed up to the <strong>sql_backups</strong> directory before removal.</p>";
    echo "<p><a href='index.php' style='color: blue; text-decoration: underline;'>Return to Home</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red; font-weight: bold;'>Error setting up database: " . $e->getMessage() . "</p>";
    exit(1);
}
?>