<?php
// Include database connection
include 'manage/db.php';

// Set error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to execute SQL queries
function executeQuery($pdo, $sql) {
    try {
        $result = $pdo->exec($sql);
        return [true, $result];
    } catch (PDOException $e) {
        return [false, $e->getMessage()];
    }
}

// Start HTML output
echo "<!DOCTYPE html>\n";
echo "<html lang='en'>\n";
echo "<head>\n";
echo "    <meta charset='UTF-8'>\n";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "    <title>Update Results Schema</title>\n";
echo "    <link rel='stylesheet' href='../css/bootstrap.min.css'>\n";
echo "    <link rel='stylesheet' href='../css/style.css'>\n";
echo "</head>\n";
echo "<body>\n";
echo "    <div class='container mt-4'>\n";
echo "        <h1 class='text-center mb-4'>Results Database Schema Update</h1>\n";

// Check if database connection is successful
if (!isset($pdo)) {
    echo "<div class='alert alert-danger'>Database connection failed. Please check your configuration.</div>\n";
    exit;
}

echo "<div class='card'>\n";
echo "    <div class='card-header bg-primary text-white'>\n";
echo "        <h5 class='mb-0'>Schema Update Progress</h5>\n";
echo "    </div>\n";
echo "    <div class='card-body'>\n";

// SQL statements to update the schema
$alterTableSQL = "ALTER TABLE results\n"
    . "MODIFY COLUMN class VARCHAR(20) NOT NULL,\n"
    . "ADD COLUMN IF NOT EXISTS student_name VARCHAR(100) NOT NULL AFTER student_id,\n"
    . "ADD COLUMN IF NOT EXISTS roll_number VARCHAR(20) NOT NULL AFTER student_name,\n"
    . "ADD COLUMN IF NOT EXISTS exam_type VARCHAR(50) NOT NULL AFTER class,\n"
    . "ADD COLUMN IF NOT EXISTS year VARCHAR(4) NOT NULL AFTER exam_type,\n"
    . "ADD COLUMN IF NOT EXISTS bangla INT DEFAULT 0 AFTER year,\n"
    . "ADD COLUMN IF NOT EXISTS english INT DEFAULT 0 AFTER bangla,\n"
    . "ADD COLUMN IF NOT EXISTS physics INT DEFAULT 0 AFTER english,\n"
    . "ADD COLUMN IF NOT EXISTS chemistry INT DEFAULT 0 AFTER physics,\n"
    . "ADD COLUMN IF NOT EXISTS biology INT DEFAULT 0 AFTER chemistry,\n"
    . "ADD COLUMN IF NOT EXISTS math INT DEFAULT 0 AFTER biology,\n"
    . "ADD COLUMN IF NOT EXISTS science INT DEFAULT 0 AFTER math,\n"
    . "ADD COLUMN IF NOT EXISTS social_science INT DEFAULT 0 AFTER science,\n"
    . "ADD COLUMN IF NOT EXISTS religion INT DEFAULT 0 AFTER social_science,\n"
    . "ADD COLUMN IF NOT EXISTS ict INT DEFAULT 0 AFTER religion,\n"
    . "ADD COLUMN IF NOT EXISTS total_marks INT DEFAULT 0 AFTER ict,\n"
    . "ADD COLUMN IF NOT EXISTS average DECIMAL(5,2) DEFAULT 0 AFTER total_marks";

// Execute the ALTER TABLE statement
list($success, $result) = executeQuery($pdo, $alterTableSQL);
if ($success) {
    echo "<div class='alert alert-success'>Successfully updated the results table schema.</div>\n";
} else {
    echo "<div class='alert alert-danger'>Error updating results table schema: $result</div>\n";
}

// Create index for better performance
$createIndexSQL = "CREATE INDEX IF NOT EXISTS idx_student_exam ON results(student_id, exam_type, year, class)";
list($success, $result) = executeQuery($pdo, $createIndexSQL);
if ($success) {
    echo "<div class='alert alert-success'>Successfully created index on results table.</div>\n";
} else {
    echo "<div class='alert alert-danger'>Error creating index: $result</div>\n";
}

// Check if the table has the correct structure
$checkTableSQL = "DESCRIBE results";
try {
    $stmt = $pdo->query($checkTableSQL);
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h5 class='mt-4'>Current Table Structure:</h5>\n";
    echo "<ul class='list-group'>\n";
    foreach ($columns as $column) {
        echo "<li class='list-group-item'>$column</li>\n";
    }
    echo "</ul>\n";
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error checking table structure: " . $e->getMessage() . "</div>\n";
}

echo "    </div>\n";
echo "</div>\n";

echo "<div class='text-center mt-4'>\n";
echo "    <a href='../index.php' class='btn btn-primary'>Back to Dashboard</a>\n";
echo "</div>\n";

echo "    </div>\n";
echo "    <script src='../js/jquery.min.js'></script>\n";
echo "    <script src='../js/bootstrap.bundle.min.js'></script>\n";
echo "</body>\n";
echo "</html>\n";
?>