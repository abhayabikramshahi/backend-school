<?php
if (!class_exists('Database')) {
    class Database {
        private static $instance = null;
        private $pdo;
        
        private function __construct() {
            try {
                $this->pdo = new PDO(
                    "mysql:host=localhost;dbname=look;charset=utf8mb4",
                    "root",
                    "",
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
        
        public function updateTeacher($id, $data) {
            $stmt = $this->pdo->prepare("
                UPDATE teachers 
                SET name = ?, role = ?, email = ?, phonenumber = ?, qualification = ?
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['name'],
                $data['role'],
                $data['email'],
                $data['phonenumber'],
                $data['qualification'],
                $id
            ]);
        }
        
        public function deleteTeacher($id) {
            $stmt = $this->pdo->prepare("DELETE FROM teachers WHERE id = ?");
            return $stmt->execute([$id]);
        }
        
        public function updateStudent($id, $data) {
            $stmt = $this->pdo->prepare("
                UPDATE students 
                SET name = ?, class = ?, roll_number = ?, address = ?, 
                    parent_name = ?, contact_number = ?, email = ?
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['name'],
                $data['class'],
                $data['roll_number'],
                $data['address'],
                $data['parent_name'],
                $data['contact_number'],
                $data['email'],
                $id
            ]);
        }
        
        public function deleteStudent($id) {
            $stmt = $this->pdo->prepare("DELETE FROM students WHERE id = ?");
            return $stmt->execute([$id]);
        }
        
        public function addResult($data) {
            $stmt = $this->pdo->prepare("
                INSERT INTO results (student_id, class_id, subject_id, marks, grade, exam_type, exam_date)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $data['student_id'],
                $data['class_id'],
                $data['subject_id'],
                $data['marks'],
                $data['grade'],
                $data['exam_type'],
                $data['exam_date']
            ]);
        }
        
        public function updateResult($id, $data) {
            $stmt = $this->pdo->prepare("
                UPDATE results 
                SET marks = ?, grade = ?, exam_type = ?, exam_date = ?
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['marks'],
                $data['grade'],
                $data['exam_type'],
                $data['exam_date'],
                $id
            ]);
        }
        
        public function deleteResult($id) {
            $stmt = $this->pdo->prepare("DELETE FROM results WHERE id = ?");
            return $stmt->execute([$id]);
        }
        
        /**
         * Execute a SQL query with parameters
         * 
         * @param string $query SQL query with placeholders
         * @param array $params Parameters to bind to the query
         * @return bool|int Returns true on success or number of affected rows
         */
        public function execute($query, $params = []) {
            try {
                $stmt = $this->pdo->prepare($query);
                $stmt->execute($params);
                return $stmt->rowCount() > 0 ? $stmt->rowCount() : true;
            } catch (PDOException $e) {
                error_log('Query Error: ' . $e->getMessage());
                return false;
            }
        }
        
        /**
         * Execute a SQL query with parameters (alias for execute)
         * 
         * @param string $query SQL query with placeholders
         * @param array $params Parameters to bind to the query
         * @return bool|int Returns true on success or number of affected rows
         */
        public function executeQuery($query, $params = []) {
            return $this->execute($query, $params);
        }
        
        /**
         * Get a single record from the database
         * 
         * @param string $query SQL query with placeholders
         * @param array $params Parameters to bind to the query
         * @return array|false Single record or false on failure
         */
        public function getRecord($query, $params = []) {
            try {
                $stmt = $this->pdo->prepare($query);
                $stmt->execute($params);
                return $stmt->fetch();
            } catch (PDOException $e) {
                error_log('Query Error: ' . $e->getMessage());
                return false;
            }
        }
        
        /**
         * Get multiple records from the database
         * 
         * @param string $query SQL query with placeholders
         * @param array $params Parameters to bind to the query
         * @return array|false Array of records or false on failure
         */
        public function getRecords($query, $params = []) {
            try {
                $stmt = $this->pdo->prepare($query);
                $stmt->execute($params);
                return $stmt->fetchAll();
            } catch (PDOException $e) {
                error_log('Query Error: ' . $e->getMessage());
                return false;
            }
        }
    }
}