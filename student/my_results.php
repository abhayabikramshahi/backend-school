<?php
/**
 * Student Results Page
 * 
 * This file allows students to view their examination results
 * with a professional UI and secure authentication.
 */

// Include authentication functions
require_once __DIR__ . '/../auth/auth_functions.php';

// Initialize Auth class
$auth = new Auth();

// Check if user is logged in and has student role
if (!$auth->isLoggedIn() || !$auth->hasRole('student')) {
    header('Location: ../auth/login.php');
    exit;
}

// Check session timeout
if (!$auth->checkSessionTimeout()) {
    header('Location: ../auth/login.php');
    exit;
}

// Get current user
$user = $auth->getCurrentUser();
$student_id = $user['id'];

// Include database connection
$db = Database::getInstance();
$conn = $db->getConnection();

// Set page variables for header
$page_title = 'My Results';
$base_path = '..';

// Get student class information
$student_query = "SELECT class FROM users WHERE id = ?";
$student_data = $db->getRecord($student_query, [$student_id]);
$student_class = $student_data ? $student_data['class'] : null;

// Get available exams for the student's class
$exams_query = "SELECT DISTINCT exam_name, exam_date FROM results WHERE class = ? ORDER BY exam_date DESC";
$exams = $db->getRecords($exams_query, [$student_class]);

// Process form submission to view specific exam results
$selected_exam = null;
$results = null;
$error_message = null;
$success_message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['exam_name'])) {
    $selected_exam = $_POST['exam_name'];
    
    // Get results for the selected exam and student
    $results_query = "SELECT subject, marks, grade, remarks FROM results 
                     WHERE student_id = ? AND class = ? AND exam_name = ?";
    $results = $db->getRecords($results_query, [$student_id, $student_class, $selected_exam]);
    
    if (!$results) {
        $error_message = "No results found for the selected examination.";
    } else {
        $success_message = "Showing results for {$selected_exam}.";
    }
}

// Include header
include_once '../includes/header.php';
?>

<!-- Print Stylesheet -->
<style media="print">
    @page { size: portrait; margin: 1cm; }
    body { font-family: Arial, sans-serif; }
    .no-print { display: none !important; }
    .print-only { display: block !important; }
    main { padding: 0 !important; }
    .print-break-after { page-break-after: always; }
    .print-container { padding: 20px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Main Content -->
<main class="flex-grow container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-graduation-cap text-purple-600 mr-3"></i>
                My Examination Results
            </h1>
            <p class="text-gray-600 mt-2">View and analyze your academic performance</p>
        </div>
        <a href="index.php" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition flex items-center no-print">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>
    
    <!-- Print Header (Only visible when printing) -->
    <div class="print-only hidden">
        <div class="text-center mb-4">
            <h1 class="text-xl font-bold">School Management System</h1>
            <p>Student Examination Results</p>
            <hr class="my-2">
        </div>
    </div>
    
    <!-- Results Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Results Header -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white p-6">
            <h2 class="text-2xl font-bold">Academic Performance</h2>
            <p class="mt-2">View your examination results and academic progress</p>
        </div>
        
        <!-- Select Exam Form -->
        <div class="p-6 border-b">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Select Examination</h3>
            
            <?php if ($error_message): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p><?php echo htmlspecialchars($success_message); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (empty($exams)): ?>
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                    <p>No examination results are available for your class at this time.</p>
                </div>
            <?php else: ?>
                <form method="POST" action="" class="max-w-md" id="examForm">
                    <div class="mb-4">
                        <label for="exam_name" class="block text-gray-700 font-medium mb-2">Examination:</label>
                        <select id="exam_name" name="exam_name" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300" required>
                            <option value="">-- Select Examination --</option>
                            <?php foreach ($exams as $exam): ?>
                                <option value="<?php echo htmlspecialchars($exam['exam_name']); ?>" <?php echo ($selected_exam === $exam['exam_name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($exam['exam_name']); ?> (<?php echo date('d M Y', strtotime($exam['exam_date'])); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex items-center">
                        <button type="submit" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-2 rounded-md hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 flex items-center">
                            <span>View Results</span>
                            <svg id="loadingIcon" class="ml-2 w-5 h-5 animate-spin hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                        <?php if ($selected_exam): ?>
                            <a href="my_results.php" class="ml-3 text-gray-600 hover:text-gray-800 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
                
                <script>
                    // Show loading indicator when form is submitted
                    document.getElementById('examForm').addEventListener('submit', function() {
                        document.getElementById('loadingIcon').classList.remove('hidden');
                    });
                </script>
            <?php endif; ?>
        </div>
        
        <!-- Results Display -->
        <?php if ($results): ?>
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Result Details</h3>
                
                <!-- Student Info -->
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 p-6 rounded-md mb-6 border border-purple-100 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-4 rounded-md shadow-sm border border-gray-100">
                            <p class="text-gray-500 text-sm uppercase tracking-wider mb-1">Student Name</p>
                            <p class="font-semibold text-lg text-gray-800"><?php echo htmlspecialchars($user['username']); ?></p>
                        </div>
                        <div class="bg-white p-4 rounded-md shadow-sm border border-gray-100">
                            <p class="text-gray-500 text-sm uppercase tracking-wider mb-1">Class</p>
                            <p class="font-semibold text-lg text-gray-800"><?php echo htmlspecialchars($student_class); ?></p>
                        </div>
                        <div class="bg-white p-4 rounded-md shadow-sm border border-gray-100">
                            <p class="text-gray-500 text-sm uppercase tracking-wider mb-1">Examination</p>
                            <p class="font-semibold text-lg text-gray-800"><?php echo htmlspecialchars($selected_exam); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Results Table -->
                <div class="overflow-x-auto mb-8">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                        <thead>
                            <tr class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white">
                                <th class="py-3 px-4 text-left font-semibold border-b">Subject</th>
                                <th class="py-3 px-4 text-left font-semibold border-b">Marks</th>
                                <th class="py-3 px-4 text-left font-semibold border-b">Grade</th>
                                <th class="py-3 px-4 text-left font-semibold border-b">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_marks = 0;
                            $subject_count = is_array($results) ? count($results) : 0;
                            $grade_points = 0;
                            $highest_mark = 0;
                            $lowest_mark = 100;
                            $grade_distribution = array('A+' => 0, 'A' => 0, 'B+' => 0, 'B' => 0, 'C+' => 0, 'C' => 0, 'D' => 0, 'F' => 0);
                            
                            foreach ($results as $result): 
                                $total_marks += $result['marks'];
                                
                                // Calculate grade points
                                switch ($result['grade']) {
                                    case 'A+': $grade_points += 4.0; $grade_distribution['A+']++; break;
                                    case 'A': $grade_points += 3.7; $grade_distribution['A']++; break;
                                    case 'B+': $grade_points += 3.3; $grade_distribution['B+']++; break;
                                    case 'B': $grade_points += 3.0; $grade_distribution['B']++; break;
                                    case 'C+': $grade_points += 2.7; $grade_distribution['C+']++; break;
                                    case 'C': $grade_points += 2.3; $grade_distribution['C']++; break;
                                    case 'D': $grade_points += 1.0; $grade_distribution['D']++; break;
                                    case 'F': $grade_points += 0.0; $grade_distribution['F']++; break;
                                }
                                
                                // Track highest and lowest marks
                                if ($result['marks'] > $highest_mark) $highest_mark = $result['marks'];
                                if ($result['marks'] < $lowest_mark) $lowest_mark = $result['marks'];
                            ?>
                                <tr class="hover:bg-purple-50 border-b transition-colors">
                                    <td class="py-3 px-4 font-medium"><?php echo htmlspecialchars($result['subject']); ?></td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center">
                                            <span class="mr-2"><?php echo htmlspecialchars($result['marks']); ?></span>
                                            <div class="w-24 bg-gray-200 rounded-full h-2.5">
                                                <div class="bg-purple-600 h-2.5 rounded-full" style="width: <?php echo $result['marks']; ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                            <?php 
                                            switch ($result['grade']) {
                                                case 'A+': echo 'bg-green-100 text-green-800'; break;
                                                case 'A': echo 'bg-green-100 text-green-800'; break;
                                                case 'B+': echo 'bg-blue-100 text-blue-800'; break;
                                                case 'B': echo 'bg-blue-100 text-blue-800'; break;
                                                case 'C+': echo 'bg-yellow-100 text-yellow-800'; break;
                                                case 'C': echo 'bg-yellow-100 text-yellow-800'; break;
                                                case 'D': echo 'bg-orange-100 text-orange-800'; break;
                                                case 'F': echo 'bg-red-100 text-red-800'; break;
                                                default: echo 'bg-gray-100 text-gray-800';
                                            }
                                            ?>"
                                        >
                                            <?php echo htmlspecialchars($result['grade']); ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($result['remarks']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <!-- Summary Row -->
                            <tr class="bg-purple-50 font-semibold">
                                <td class="py-3 px-4">Total</td>
                                <td class="py-3 px-4"><?php echo $total_marks; ?></td>
                                <td class="py-3 px-4"><?php echo number_format(($total_marks / $subject_count), 2); ?> Avg</td>
                                <td class="py-3 px-4">GPA: <?php echo number_format(($grade_points / $subject_count), 2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Performance Summary -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 mb-8">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Performance Summary</h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-purple-50 p-4 rounded-md border border-purple-100">
                            <p class="text-sm text-gray-500 mb-1">Average Score</p>
                            <p class="text-2xl font-bold text-purple-700"><?php echo number_format(($total_marks / $subject_count), 1); ?>%</p>
                        </div>
                        <div class="bg-indigo-50 p-4 rounded-md border border-indigo-100">
                            <p class="text-sm text-gray-500 mb-1">GPA</p>
                            <p class="text-2xl font-bold text-indigo-700"><?php echo number_format(($grade_points / $subject_count), 2); ?></p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-md border border-blue-100">
                            <p class="text-sm text-gray-500 mb-1">Highest Mark</p>
                            <p class="text-2xl font-bold text-blue-700"><?php echo $highest_mark; ?>%</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-md border border-green-100">
                            <p class="text-sm text-gray-500 mb-1">Lowest Mark</p>
                            <p class="text-2xl font-bold text-green-700"><?php echo $lowest_mark; ?>%</p>
                        </div>
                    </div>
                </div>
                
                <!-- Performance Chart -->
                <div class="mt-8">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Performance Visualization</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Bar Chart -->
                        <div class="bg-white p-4 rounded-md shadow-sm border border-gray-100">
                            <h5 class="text-md font-medium text-gray-700 mb-3">Subject Marks</h5>
                            <div class="h-64">
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                        
                        <!-- Radar Chart -->
                        <div class="bg-white p-4 rounded-md shadow-sm border border-gray-100">
                            <h5 class="text-md font-medium text-gray-700 mb-3">Performance Overview</h5>
                            <div class="h-64">
                                <canvas id="radarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    // Prepare data for charts
                    const subjects = [<?php echo implode(', ', array_map(function($result) { return '"' . addslashes($result['subject']) . '"'; }, $results)); ?>];
                    const marks = [<?php echo implode(', ', array_map(function($result) { return $result['marks']; }, $results)); ?>];
                    
                    // Create bar chart
                    const barCtx = document.getElementById('barChart').getContext('2d');
                    const barChart = new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: subjects,
                            datasets: [{
                                label: 'Marks',
                                data: marks,
                                backgroundColor: 'rgba(102, 126, 234, 0.6)',
                                borderColor: 'rgba(102, 126, 234, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            }
                        }
                    });
                    
                    // Create radar chart
                    const radarCtx = document.getElementById('radarChart').getContext('2d');
                    const radarChart = new Chart(radarCtx, {
                        type: 'radar',
                        data: {
                            labels: subjects,
                            datasets: [{
                                label: 'Your Performance',
                                data: marks,
                                backgroundColor: 'rgba(124, 58, 237, 0.2)',
                                borderColor: 'rgba(124, 58, 237, 1)',
                                pointBackgroundColor: 'rgba(124, 58, 237, 1)',
                                pointBorderColor: '#fff',
                                pointHoverBackgroundColor: '#fff',
                                pointHoverBorderColor: 'rgba(124, 58, 237, 1)',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                r: {
                                    angleLines: {
                                        display: true
                                    },
                                    suggestedMin: 0,
                                    suggestedMax: 100
                                }
                            }
                        }
                    });
                </script>
                
                <!-- Download/Print Options -->
                <div class="mt-6 flex justify-end no-print">
                    <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition mr-3 flex items-center">
                        <i class="fas fa-print mr-2"></i> Print Results
                    </button>
                    <button id="downloadPdf" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-md hover:from-indigo-700 hover:to-purple-700 transition flex items-center">
                        <i class="fas fa-download mr-2"></i> Download PDF
                    </button>
                </div>
                
                <!-- Footer Note -->
                <div class="mt-8 text-center text-gray-500 text-sm border-t pt-6">
                    <p>This result card is generated by the School Management System.</p>
                    <p class="mt-1">For any discrepancies, please contact the school administration.</p>
                </div>
                
                <!-- PDF Generation Script -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
                <script>
                    document.getElementById('downloadPdf').addEventListener('click', function() {
                        const { jsPDF } = window.jspdf;
                        const doc = new jsPDF();
                        
                        // Add school header
                        doc.setFontSize(18);
                        doc.setTextColor(75, 85, 99);
                        doc.text('School Management System', 105, 15, { align: 'center' });
                        
                        doc.setFontSize(14);
                        doc.setTextColor(107, 114, 128);
                        doc.text('Examination Results', 105, 25, { align: 'center' });
                        
                        // Add student info
                        doc.setFontSize(11);
                        doc.setTextColor(0, 0, 0);
                        doc.text('Student: <?php echo addslashes($user["username"]); ?>', 14, 40);
                        doc.text('Class: <?php echo addslashes($student_class); ?>', 14, 48);
                        doc.text('Examination: <?php echo addslashes($selected_exam); ?>', 14, 56);
                        
                        // Create results table
                        const tableColumn = ["Subject", "Marks", "Grade", "Remarks"];
                        const tableRows = [];
                        
                        <?php foreach ($results as $result): ?>
                            tableRows.push([
                                "<?php echo addslashes($result['subject']); ?>", 
                                "<?php echo $result['marks']; ?>", 
                                "<?php echo addslashes($result['grade']); ?>", 
                                "<?php echo addslashes($result['remarks']); ?>"
                            ]);
                        <?php endforeach; ?>
                        
                        // Add summary row
                        tableRows.push([
                            "Total", 
                            "<?php echo $total_marks; ?>", 
                            "<?php echo number_format(($total_marks / $subject_count), 2); ?> Avg", 
                            ""
                        ]);
                        
                        // Generate the table
                        doc.autoTable({
                            head: [tableColumn],
                            body: tableRows,
                            startY: 65,
                            theme: 'grid',
                            styles: { fontSize: 10, cellPadding: 3 },
                            headStyles: { fillColor: [102, 126, 234], textColor: 255 },
                            alternateRowStyles: { fillColor: [240, 240, 240] },
                            foot: [['', '', '', '']],
                            didDrawPage: function(data) {
                                // Footer
                                doc.setFontSize(10);
                                doc.setTextColor(150);
                                doc.text('This is an official document from the School Management System.', 105, doc.internal.pageSize.height - 10, { align: 'center' });
                            }
                        });
                        
                        // Save the PDF
                        doc.save('<?php echo addslashes($user["username"]); ?>_<?php echo addslashes($selected_exam); ?>_results.pdf');
                    });
                </script>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include_once '../includes/footer.php'; ?>