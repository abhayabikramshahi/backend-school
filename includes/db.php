<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=localhost;dbname=school_management;charset=utf8mb4",
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

    // Teacher methods
    public function getTeachers() {
        $stmt = $this->pdo->query("SELECT * FROM teachers ORDER BY name");
        return $stmt->fetchAll();
    }

    public function getTeacher($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM teachers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateTeacher($id, $data) {
        $sql = "UPDATE teachers SET name = ?, role = ?, email = ?, phonenumber = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$data['name'], $data['role'], $data['email'], $data['phonenumber'], $id]);
    }

    public function deleteTeacher($id) {
        $stmt = $this->pdo->prepare("DELETE FROM teachers WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Student methods
    public function getStudents() {
        $stmt = $this->pdo->query("SELECT s.*, c.name as class_name 
                                  FROM students s 
                                  LEFT JOIN classes c ON s.class_id = c.id 
                                  ORDER BY s.name");
        return $stmt->fetchAll();
    }

    public function getStudent($id) {
        $stmt = $this->pdo->prepare("SELECT s.*, c.name as class_name 
                                    FROM students s 
                                    LEFT JOIN classes c ON s.class_id = c.id 
                                    WHERE s.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateStudent($id, $data) {
        $sql = "UPDATE students SET name = ?, class_id = ?, roll_number = ?, 
                parent_name = ?, contact = ?, email = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['name'], 
            $data['class_id'], 
            $data['roll_number'],
            $data['parent_name'],
            $data['contact'],
            $data['email'],
            $id
        ]);
    }

    public function deleteStudent($id) {
        $stmt = $this->pdo->prepare("DELETE FROM students WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Result methods
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

    public function addResult($data) {
        $sql = "INSERT INTO results (student_id, class_id, subject_id, marks, grade, exam_type, exam_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
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
        $sql = "UPDATE results SET marks = ?, grade = ?, exam_type = ?, exam_date = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
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

    // Count methods
    public function getStudentCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM students");
        return $stmt->fetchColumn();
    }

    public function getTeacherCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM teachers");
        return $stmt->fetchColumn();
    }

    public function getClassCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM classes");
        return $stmt->fetchColumn();
    }

    public function getResultCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM results");
        return $stmt->fetchColumn();
    }

    // Authentication
    public function authenticateUser($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
} 