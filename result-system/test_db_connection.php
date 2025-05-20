<?php
// Set error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
include 'manage/db.php';

// Start HTML output
echo "<!DOCTYPE html>\n";
echo "<html lang='en'>\n";
echo "<head>\n";
echo "    <meta charset='UTF-8'>\n";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "    <title>Test Database Connection</title>\n";
echo "    <link rel='stylesheet' href='../css/bootstrap.min.css'>\n";
echo "    <link rel='stylesheet' href='../css/style.css'>\n";
echo "</head>\n";
echo "<body>\n";
echo "    <div class='container mt-4'>\n";
echo "        <h1 class='text-center mb-4'>Database Connection Test</h1>\n";

// Check if database connection is successful
if (!isset($pdo)) {
    echo "<div class='alert alert-danger'>Database connection failed. Please check your configuration.</div>\n";
    exit;
}

echo "<div class='card'>\n";
echo "    <div class='card-header bg-primary text-white'>\n";
echo "        <h5 class='mb-0'>Connection Status</h5>\n";
echo "    </div>\n";
echo "    <div class='card-body'>\n";

// Test the connection
try {
    // Get PDO attributes
    $serverVersion = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    $connectionStatus = $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
    $driverName = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    
    echo "<div class='alert alert-success'>Database connection successful!</div>\n";
    echo "<ul class='list-group'>\n";
    echo "    <li class='list-group-item'><strong>Server Version:</strong> $serverVersion</li>\n";
    echo "    <li class='list-group-item'><strong>Connection Status:</strong> $connectionStatus</li>\n";
    echo "    <li class='list-group-item'><strong>Driver Name:</strong> $driverName</li>\n";
    echo "</ul>\n";
    
    // Check if results table exists
    $tableCheckSQL = "SHOW TABLES LIKE 'results'";
    $stmt = $pdo->query($tableCheckSQL);
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "<div class='alert alert-success mt-3'>Results table exists in the database.</div>\n";
        
        // Check table structure
        $tableStructureSQL = "DESCRIBE results";
        $stmt = $pdo->query($tableStructureSQL);
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h5 class='mt-4'>Results Table Structure:</h5>\n";
        echo "<div class='table-responsive'>\n";
        echo "<table class='table table-striped'>\n";
        echo "<thead>\n";
        echo "<tr>\n";
        echo "<th>Field</th>\n";
        echo "<th>Type</th>\n";
        echo "<th>Null</th>\n";
        echo "<th>Key</th>\n";
        echo "<th>Default</th>\n";
        echo "<th>Extra</th>\n";
        echo "</tr>\n";
        echo "</thead>\n";
        echo "<tbody>\n";
        
        foreach ($columns as $column) {
            echo "<tr>\n";
            echo "<td>{$column['Field']}</td>\n";
            echo "<td>{$column['Type']}</td>\n";
            echo "<td>{$column['Null']}</td>\n";
            echo "<td>{$column['Key']}</td>\n";
            echo "<td>{$column['Default']}</td>\n";
            echo "<td>{$column['Extra']}</td>\n";
            echo "</tr>\n";
        }
        
        echo "</tbody>\n";
        echo "</table>\n";
        echo "</div>\n";
        
        // Check if the table has the required columns for class12
        $requiredColumns = ['student_id', 'student_name', 'roll_number', 'class', 'exam_type', 'year', 
                           'bangla', 'english', 'physics', 'chemistry', 'biology', 'math', 'ict', 
                           'total_marks', 'average', 'grade'];
        
        $missingColumns = [];
        $existingColumns = array_column($columns, 'Field');
        
        foreach ($requiredColumns as $column) {
            if (!in_array($column, $existingColumns)) {
                $missingColumns[] = $column;
            }
        }
        
        if (empty($missingColumns)) {
            echo "<div class='alert alert-success mt-3'>All required columns for class12 results exist in the table.</div>\n";
        } else {
            echo "<div class='alert alert-warning mt-3'>Missing columns for class12 results: " . implode(', ', $missingColumns) . "</div>\n";
            echo "<div class='alert alert-info'>Please run the update_results_schema.php script to add these columns.</div>\n";
        }
    } else {
        echo "<div class='alert alert-danger mt-3'>Results table does not exist in the database.</div>\n";
        echo "<div class='alert alert-info'>Please run the setup_results_db.php script to create the table.</div>\n";
    }
    
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error testing database: " . $e->getMessage() . "</div>\n";
}

echo "    </div>\n";
echo "</div>\n";

echo "<div class='text-center mt-4'>\n";
echo "    <a href='update_results_schema.php' class='btn btn-primary'>Update Results Schema</a>\n";
echo "    <a href='../index.php' class='btn btn-secondary ml-2'>Back to Dashboard</a>\n";
echo "</div>\n";

echo "    </div>\n";
echo "    <script src='../js/jquery.min.js'></script>\n";
echo "    <script src='../js/bootstrap.bundle.min.js'></script>\n";
echo "</body>\n";
echo "</html>\n";
?>