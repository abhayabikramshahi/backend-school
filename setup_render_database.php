<?php
/**
 * Render.com Database Setup Script
 * 
 * This script helps initialize the database when deploying to Render.com
 * It checks for environment variables and creates necessary tables
 */

// Load environment variables
require_once __DIR__ . '/env_loader.php';

// Check if environment variables are set
echo "Checking environment variables...\n";
$required_vars = ['DB_HOST', 'DB_NAME', 'DB_USERNAME', 'DB_PASSWORD'];
$missing_vars = [];

foreach ($required_vars as $var) {
    if (!getenv($var)) {
        $missing_vars[] = $var;
    }
}

if (!empty($missing_vars)) {
    echo "Error: The following environment variables are missing: " . implode(', ', $missing_vars) . "\n";
    echo "Please set these variables in your Render.com dashboard.\n";
    exit(1);
}

// Try to connect to the database
echo "Connecting to database...\n";
try {
    $host = getenv('DB_HOST');
    $dbname = getenv('DB_NAME');
    $username = getenv('DB_USERNAME');
    $password = getenv('DB_PASSWORD');
    
    $pdo = new PDO(
        "mysql:host={$host};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    echo "Connected successfully!\n";
    
    // Check if database exists, if not create it
    echo "Checking if database exists...\n";
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$dbname}'");
    $database_exists = $stmt->fetchColumn();
    
    if (!$database_exists) {
        echo "Database does not exist. Creating database {$dbname}...\n";
        $pdo->exec("CREATE DATABASE `{$dbname}`");
        echo "Database created successfully!\n";
    } else {
        echo "Database already exists.\n";
    }
    
    // Select the database
    $pdo->exec("USE `{$dbname}`");
    
    // Check if tables exist by checking for a common table
    echo "Checking if tables exist...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'students'");
    $tables_exist = $stmt->fetchColumn();
    
    if (!$tables_exist) {
        echo "Tables do not exist. Importing database schema...\n";
        
        // Import database schema from SQL file if it exists
        if (file_exists(__DIR__ . '/database_setup.sql')) {
            $sql = file_get_contents(__DIR__ . '/database_setup.sql');
            $pdo->exec($sql);
            echo "Database schema imported successfully!\n";
        } else {
            echo "Warning: database_setup.sql file not found. Please import the database schema manually.\n";
        }
    } else {
        echo "Tables already exist.\n";
    }
    
    echo "Database setup completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
    exit(1);
}