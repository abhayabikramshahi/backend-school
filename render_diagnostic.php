<?php
/**
 * Render.com Diagnostic Script
 * 
 * This script helps diagnose common issues when deploying to Render.com
 * It checks environment variables, database connection, and PHP configuration
 */

// Display all errors for diagnostic purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Render.com Deployment Diagnostic</h1>";

// Check PHP version
echo "<h2>PHP Environment</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Required: PHP 7.4 or PHP 8.0+</p>";

// Check loaded extensions
echo "<h3>Loaded Extensions</h3>";
echo "<ul>";
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring'];
foreach ($required_extensions as $ext) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? 'Loaded ✓' : 'Not Loaded ✗';
    $color = $loaded ? 'green' : 'red';
    echo "<li style='color:{$color}'>{$ext}: {$status}</li>";
}
echo "</ul>";

// Check environment variables
echo "<h2>Environment Variables</h2>";
echo "<ul>";
$env_vars = ['DB_HOST', 'DB_NAME', 'DB_USERNAME', 'DB_PASSWORD', 'APP_ENV', 'APP_DEBUG'];
foreach ($env_vars as $var) {
    $value = getenv($var);
    $status = $value !== false ? 'Set ✓' : 'Not Set ✗';
    $color = $value !== false ? 'green' : 'red';
    // Don't display actual values for security
    echo "<li style='color:{$color}'>{$var}: {$status}</li>";
}
echo "</ul>";

// Test database connection
echo "<h2>Database Connection Test</h2>";
try {
    $host = getenv('DB_HOST') ?: 'localhost';
    $dbname = getenv('DB_NAME') ?: 'look';
    $username = getenv('DB_USERNAME') ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';
    
    // First try connecting to the server without specifying a database
    $pdo = new PDO(
        "mysql:host={$host}",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5, // 5 second timeout
        ]
    );
    
    echo "<p style='color:green'>Successfully connected to MySQL server ✓</p>";
    
    // Now try connecting to the specific database
    try {
        $pdo = new PDO(
            "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5, // 5 second timeout
            ]
        );
        echo "<p style='color:green'>Successfully connected to database '{$dbname}' ✓</p>";
        
        // Check if required tables exist
        $tables = ['users', 'students', 'teachers'];
        echo "<h3>Database Tables Check</h3>";
        echo "<ul>";
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'")->fetchAll();
            $exists = !empty($stmt);
            $status = $exists ? 'Exists ✓' : 'Missing ✗';
            $color = $exists ? 'green' : 'red';
            echo "<li style='color:{$color}'>{$table}: {$status}</li>";
        }
        echo "</ul>";
        
    } catch (PDOException $e) {
        echo "<p style='color:red'>Database connection error: " . $e->getMessage() . " ✗</p>";
        echo "<p>The MySQL server is accessible, but the specific database '{$dbname}' could not be connected to.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red'>MySQL server connection error: " . $e->getMessage() . " ✗</p>";
    echo "<p>Possible issues:</p>";
    echo "<ul>";
    echo "<li>MySQL server is not running or not accessible from Render.com</li>";
    echo "<li>Database credentials are incorrect</li>";
    echo "<li>Network/firewall restrictions preventing connection</li>";
    echo "</ul>";
}

// Check file permissions
echo "<h2>File System Check</h2>";
$writable_dirs = ['.', 'config'];
echo "<ul>";
foreach ($writable_dirs as $dir) {
    $is_writable = is_writable(__DIR__ . '/' . $dir);
    $status = $is_writable ? 'Writable ✓' : 'Not Writable ✗';
    $color = $is_writable ? 'green' : 'red';
    echo "<li style='color:{$color}'>{$dir}: {$status}</li>";
}
echo "</ul>";

// Provide troubleshooting tips
echo "<h2>Troubleshooting Tips</h2>";
echo "<ol>";
echo "<li>Ensure all environment variables are correctly set in the Render.com dashboard</li>";
echo "<li>Verify that your database is accessible from Render.com (check IP allowlists)</li>";
echo "<li>Check that the database user has proper permissions</li>";
echo "<li>Ensure your PHP version matches what's specified in composer.json</li>";
echo "<li>If using an external MySQL database, ensure it allows remote connections</li>";
echo "</ol>";

echo "<p><strong>Note:</strong> After fixing issues, you may need to redeploy your application on Render.com</p>";
?>