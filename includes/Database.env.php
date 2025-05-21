<?php
/**
 * Database Environment Configuration
 * 
 * This file provides database connection settings with environment variable support
 * for deployment on platforms like Render.com
 */

if (!class_exists('Database')) {
    class Database {
        private static $instance = null;
        private $pdo;
        
        private function __construct() {
            // Get database credentials from environment variables or use defaults
            $host = getenv('DB_HOST') ?: 'localhost';
            $dbname = getenv('DB_NAME') ?: 'look';
            $username = getenv('DB_USERNAME') ?: 'root';
            $password = getenv('DB_PASSWORD') ?: '';
            
            try {
                $this->pdo = new PDO(
                    "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        
        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        
        public function getConnection() {
            return $this->pdo;
        }
        
        // Student related methods
        public function getStudents() {
            $stmt = $this->pdo->query("SELECT * FROM students ORDER BY name");
            return $stmt->fetchAll();
        }
        
        public function getStudent($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM students WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        }
        
        // Teacher related methods
        public function getTeachers() {
            $stmt = $this->pdo->query("SELECT * FROM teachers ORDER BY name");
            return $stmt->fetchAll();
        }
        
        public function getTeacher($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM teachers WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        }
        
        // Class related methods
        public function getClasses() {
            $stmt = $this->pdo->query("SELECT * FROM classes ORDER BY name");
            return $stmt->fetchAll();
        }
        
        public function getClass($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM classes WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        }
        
        // Result related methods
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
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        }
        
        public function getResult($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM results WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        }
    }
}