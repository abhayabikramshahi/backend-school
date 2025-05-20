<?php
/**
 * Database Configuration File
 * 
 * This file handles the database connection using PDO with proper error handling
 * and security measures.
 */

class Database {
    private $host = 'localhost';
    private $dbname = 'look';
    private $username = 'root';
    private $password = '';
    private $conn;
    private static $instance = null;
    
    /**
     * Constructor - establishes database connection
     */
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => true
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // Log error instead of exposing details
            error_log('Database Connection Error: ' . $e->getMessage());
            die('Database connection failed. Please try again later.');
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
    
    /**
     * Execute a query with parameters
     * 
     * @param string $query SQL query with placeholders
     * @param array $params Parameters to bind to the query
     * @return PDOStatement|false
     */
    public function query($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Query Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get a single record
     * 
     * @param string $query SQL query with placeholders
     * @param array $params Parameters to bind to the query
     * @return array|false Single record or false on failure
     */
    public function getRecord($query, $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt ? $stmt->fetch() : false;
    }
    
    /**
     * Get multiple records
     * 
     * @param string $query SQL query with placeholders
     * @param array $params Parameters to bind to the query
     * @return array|false Array of records or false on failure
     */
    public function getRecords($query, $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt ? $stmt->fetchAll() : false;
    }
    
    
    /**
     * Insert a record and return the last insert ID
     * 
     * @param string $table Table name
     * @param array $data Associative array of column => value
     * @return int|false Last insert ID or false on failure
     */
    public function insert($table, $data) {
        try {
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            
            $query = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
            $stmt = $this->conn->prepare($query);
            
            // Debug information
            $debugValues = array_values($data);
            error_log("Attempting to insert into {$table} with query: {$query}");
            error_log("Values: " . print_r($debugValues, true));
            
            $result = $stmt->execute(array_values($data));
            
            if ($result) {
                return $this->conn->lastInsertId();
            } else {
                $errorInfo = $stmt->errorInfo();
                error_log("Insert Error: " . print_r($errorInfo, true));
                return false;
            }
        } catch (PDOException $e) {
            error_log('Insert Error: ' . $e->getMessage());
            error_log('SQL Query: ' . $query);
            error_log('Data: ' . print_r($data, true));
            return false;
        }
    }
    
    /**
     * Update records
     * 
     * @param string $table Table name
     * @param array $data Associative array of column => value to update
     * @param string|array $where WHERE clause or conditions array
     * @param array $params Parameters for WHERE clause
     * @return int|false Number of affected rows or false on failure
     */
    public function update($table, $data, $where, $params = []) {
        try {
            $set = [];
            foreach (array_keys($data) as $column) {
                $set[] = "{$column} = ?";
            }
            $set = implode(', ', $set);
            
            // Handle where clause - could be string or array
            $whereClause = $where;
            if (is_array($where)) {
                $whereParts = [];
                foreach (array_keys($where) as $column) {
                    $whereParts[] = "{$column} = ?";
                }
                $whereClause = implode(' AND ', $whereParts);
                // If where is an array, add its values to params
                $params = array_values($where);
            }
            
            $query = "UPDATE {$table} SET {$set} WHERE {$whereClause}";
            $stmt = $this->conn->prepare($query);
            
            $values = array_merge(array_values($data), $params);
            $stmt->execute($values);
            
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log('Update Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete records
     * 
     * @param string $table Table name
     * @param string $where WHERE clause
     * @param array $params Parameters for WHERE clause
     * @return int|false Number of affected rows or false on failure
     */
    public function delete($table, $where, $params = []) {
        try {
            $query = "DELETE FROM {$table} WHERE {$where}";
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log('Delete Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Execute a direct SQL query with parameters
     * 
     * @param string $query SQL query with placeholders
     * @param array $params Parameters to bind to the query
     * @return bool True on success, false on failure
     */
    public function execute($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('Execute Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Execute a query with parameters (alias for execute)
     * 
     * @param string $query SQL query with placeholders
     * @param array $params Parameters to bind to the query
     * @return bool True on success, false on failure
     */
    public function executeQuery($query, $params = []) {
        return $this->execute($query, $params);
    }
    
    /**
     * Get all students
     * 
     * @return array|false Array of student records or false on failure
     */
    public function getStudents() {
        return $this->getRecords("SELECT * FROM students ORDER BY name");
    }
    
    /**
     * Get all teachers
     * 
     * @return array|false Array of teacher records or false on failure
     */
    public function getTeachers() {
        return $this->getRecords("SELECT * FROM teachers ORDER BY name");
    }
    
    /**
     * Get all classes
     * 
     * @return array|false Array of class records or false on failure
     */
    public function getClasses() {
        return $this->getRecords("SELECT * FROM classes ORDER BY name");
    }
    
    /**
     * Get all results
     * 
     * @param int|null $student_id Optional student ID filter
     * @param int|null $class_id Optional class ID filter
     * @return array|false Array of result records or false on failure
     */
    public function getResults($student_id = null, $class_id = null) {
        $sql = "SELECT r.*, s.name as student_name, c.name as class_name, sub.name as subject_name 
                FROM results r 
                JOIN students s ON r.student_id = s.id 
                JOIN classes c ON r.class_id = c.id 
                JOIN subjects sub ON r.subject_id = sub.id";
        
        $params = [];
        if ($student_id) {
            $sql .= " WHERE r.student_id = ?";
            $params[] = $student_id;
        } elseif ($class_id) {
            $sql .= " WHERE r.class_id = ?";
            $params[] = $class_id;
        }
        
        $sql .= " ORDER BY s.name, sub.name";
        return $this->getRecords($sql, $params);
    }
}