<?php
/**
 * Results Database Setup Script
 * 
 * This script creates the necessary database table for the student results system
 * and inserts sample data for testing purposes.
 */

// Include database connection
require_once __DIR__ . '/../config/database.php';

// Get database connection
$db = Database::getInstance();
$conn = $db->getConnection();

// Create results table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    class VARCHAR(20) NOT NULL,
    exam_name VARCHAR(100) NOT NULL,
    exam_date DATE NOT NULL,
    subject VARCHAR(50) NOT NULL,
    marks DECIMAL(5,2) NOT NULL,
    grade VARCHAR(5) NOT NULL,
    remarks VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (student_id),
    INDEX (class),
    INDEX (exam_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

$table_created = $db->query($create_table_sql);

// Check if table exists and has data
$check_data_sql = "SELECT COUNT(*) as count FROM results";
$result = $db->getRecord($check_data_sql);

// Insert sample data if table is empty
if ($result && $result['count'] == 0) {
    $sample_data = [
        // Sample data for student ID 1
        ['student_id' => 1, 'class' => 'Class 1', 'exam_name' => 'First Term Examination', 'exam_date' => '2023-04-15', 'subject' => 'Mathematics', 'marks' => 85.00, 'grade' => 'A', 'remarks' => 'Excellent performance'],
        ['student_id' => 1, 'class' => 'Class 1', 'exam_name' => 'First Term Examination', 'exam_date' => '2023-04-15', 'subject' => 'English', 'marks' => 78.50, 'grade' => 'B+', 'remarks' => 'Good work'],
        ['student_id' => 1, 'class' => 'Class 1', 'exam_name' => 'First Term Examination', 'exam_date' => '2023-04-15', 'subject' => 'Science', 'marks' => 92.00, 'grade' => 'A+', 'remarks' => 'Outstanding'],
        ['student_id' => 1, 'class' => 'Class 1', 'exam_name' => 'First Term Examination', 'exam_date' => '2023-04-15', 'subject' => 'Social Studies', 'marks' => 81.00, 'grade' => 'A', 'remarks' => 'Very good'],
        ['student_id' => 1, 'class' => 'Class 1', 'exam_name' => 'First Term Examination', 'exam_date' => '2023-04-15', 'subject' => 'Art', 'marks' => 88.00, 'grade' => 'A', 'remarks' => 'Creative work'],
        
        ['student_id' => 1, 'class' => 'Class 1', 'exam_name' => 'Mid Term Examination', 'exam_date' => '2023-07-20', 'subject' => 'Mathematics', 'marks' => 82.00, 'grade' => 'A', 'remarks' => 'Good improvement'],
        ['student_id' => 1, 'class' => 'Class 1', 'exam_name' => 'Mid Term Examination', 'exam_date' => '2023-07-20', 'subject' => 'English', 'marks' => 75.00, 'grade' => 'B', 'remarks' => 'Needs more practice in writing'],
        ['student_id' => 1, 'class' => 'Class 1', 'exam_name' => 'Mid Term Examination', 'exam_date' => '2023-07-20', 'subject' => 'Science', 'marks' => 90.00, 'grade' => 'A+', 'remarks' => 'Excellent understanding of concepts'],
        ['student_id' => 1, 'class' => 'Class 1', 'exam_name' => 'Mid Term Examination', 'exam_date' => '2023-07-20', 'subject' => 'Social Studies', 'marks' => 85.00, 'grade' => 'A', 'remarks' => 'Good knowledge of the subject'],
        ['student_id' => 1, 'class' => 'Class 1', 'exam_name' => 'Mid Term Examination', 'exam_date' => '2023-07-20', 'subject' => 'Art', 'marks' => 92.00, 'grade' => 'A+', 'remarks' => 'Exceptional creativity'],
        
        // Sample data for student ID 2
        ['student_id' => 2, 'class' => 'Class 2', 'exam_name' => 'First Term Examination', 'exam_date' => '2023-04-15', 'subject' => 'Mathematics', 'marks' => 72.00, 'grade' => 'B', 'remarks' => 'Good effort'],
        ['student_id' => 2, 'class' => 'Class 2', 'exam_name' => 'First Term Examination', 'exam_date' => '2023-04-15', 'subject' => 'English', 'marks' => 68.00, 'grade' => 'C+', 'remarks' => 'Needs improvement in grammar'],
        ['student_id' => 2, 'class' => 'Class 2', 'exam_name' => 'First Term Examination', 'exam_date' => '2023-04-15', 'subject' => 'Science', 'marks' => 75.00, 'grade' => 'B', 'remarks' => 'Good understanding of basic concepts'],
        
        // Sample data for student ID 3
        ['student_id' => 3, 'class' => 'Class 3', 'exam_name' => 'Mid Term Examination', 'exam_date' => '2023-07-20', 'subject' => 'Mathematics', 'marks' => 95.00, 'grade' => 'A+', 'remarks' => 'Exceptional performance'],
        ['student_id' => 3, 'class' => 'Class 3', 'exam_name' => 'Mid Term Examination', 'exam_date' => '2023-07-20', 'subject' => 'English', 'marks' => 88.00, 'grade' => 'A', 'remarks' => 'Excellent communication skills'],
        ['student_id' => 3, 'class' => 'Class 3', 'exam_name' => 'Mid Term Examination', 'exam_date' => '2023-07-20', 'subject' => 'Science', 'marks' => 92.00, 'grade' => 'A+', 'remarks' => 'Outstanding scientific knowledge']
    ];
    
    $success_count = 0;
    foreach ($sample_data as $data) {
        $inserted = $db->insert('results', $data);
        if ($inserted) {
            $success_count++;
        }
    }
    
    echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background-color: #f8f9fa; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
            <h1 style='color: #4a5568; margin-bottom: 20px;'>Results Database Setup</h1>";
    
    if ($table_created) {
        echo "<div style='background-color: #c6f6d5; border-left: 4px solid #38a169; padding: 15px; margin-bottom: 20px; border-radius: 4px;'>
                <p style='color: #2f855a; margin: 0;'><strong>Success:</strong> Results table created successfully!</p>
              </div>";
    } else {
        echo "<div style='background-color: #fed7d7; border-left: 4px solid #e53e3e; padding: 15px; margin-bottom: 20px; border-radius: 4px;'>
                <p style='color: #c53030; margin: 0;'><strong>Error:</strong> Failed to create results table.</p>
              </div>";
    }
    
    echo "<div style='background-color: #c6f6d5; border-left: 4px solid #38a169; padding: 15px; margin-bottom: 20px; border-radius: 4px;'>
            <p style='color: #2f855a; margin: 0;'><strong>Success:</strong> {$success_count} sample records inserted into the results table.</p>
          </div>";
    
    echo "<div style='margin-top: 30px;'>
            <a href='../student/my_results.php' style='display: inline-block; background-color: #4c51bf; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Go to Student Results Page</a>
            <a href='../index.php' style='display: inline-block; background-color: #718096; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-left: 10px;'>Back to Home</a>
          </div>";
    
    echo "</div>";
} else {
    echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background-color: #f8f9fa; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
            <h1 style='color: #4a5568; margin-bottom: 20px;'>Results Database Setup</h1>
            <div style='background-color: #bee3f8; border-left: 4px solid #3182ce; padding: 15px; margin-bottom: 20px; border-radius: 4px;'>
                <p style='color: #2c5282; margin: 0;'><strong>Information:</strong> Results table already exists and contains data.</p>
            </div>
            <div style='margin-top: 30px;'>
                <a href='../student/my_results.php' style='display: inline-block; background-color: #4c51bf; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Go to Student Results Page</a>
                <a href='../index.php' style='display: inline-block; background-color: #718096; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-left: 10px;'>Back to Home</a>
            </div>
          </div>";
}
?>