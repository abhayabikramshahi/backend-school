<?php
/**
 * Render.com Database Configuration
 * 
 * This file provides database connection settings specifically for Render.com deployment
 * It prioritizes environment variables and includes better error handling
 */

class Database {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $conn;
    private static $instance = null;
    
    private function __construct() {
        // Get database credentials from environment variables
        $this->host = getenv('DB_HOST');
        $this->dbname = getenv('DB_NAME');
        $this->username = getenv('DB_USERNAME');
        $this->password = getenv('DB_PASSWORD');
        
        // Validate environment variables
        if (!$this->host || !$this->dbname || !$this->username) {
            error_log('Database Configuration Error: Missing required environment variables');
            die('Database configuration error. Please check environment variables.');
        }
        
        try {
            // First try connecting without database name to check server connection
            $dsn = "mysql:host={$this->host};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5 // 5 second timeout
            ];
            
            $serverConn = new PDO($dsn, $this->username, $this->password, $options);
            
            // Now try connecting to the specific database
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch (PDOException $e) {
            // Log detailed error for debugging
            error_log('Render Database Connection Error: ' . $e->getMessage());
            
            // Provide more helpful error message
            if (strpos($e->getMessage(), "Unknown database") !== false) {
                die('Database does not exist. Please ensure the database is created on your MySQL server.');
            } else if (strpos($e->getMessage(), "Access denied") !== false) {
                die('Database access denied. Please check your credentials.');
            } else if (strpos($e->getMessage(), "Connection refused") !== false) {
                die('Database connection refused. Please check if the database server is running and accessible from Render.com.');
            } else {
                die('Database connection failed: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Get database instance (Singleton pattern)
     * 
     * @return Database instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get database connection
     * 
     * @return PDO connection
     */
    public function getConnection() {
        return $this->conn;
    }
    
    // Include all the methods from the original Database class
    // These methods are kept identical to maintain compatibility
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Database Query Error: ' . $e->getMessage());
            throw $e; // Re-throw for handling by caller
        }
    }
    
    public function getRecord($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    public function getRecords($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($data);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log('Database Insert Error: ' . $e->getMessage());
            throw $e; // Re-throw for handling by caller
        }
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $setClauses = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $setClauses[] = "{$column} = :set_{$column}";
            $params["set_{$column}"] = $value;
        }
        
        $sql = "UPDATE {$table} SET " . implode(', ', $setClauses) . " WHERE {$where}";
        
        foreach ($whereParams as $key => $value) {
            $params[$key] = $value;
        }
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log('Database Update Error: ' . $e->getMessage());
            throw $e; // Re-throw for handling by caller
        }
    }
    
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log('Database Delete Error: ' . $e->getMessage());
            throw $e; // Re-throw for handling by caller
        }
    }
}