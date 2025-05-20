<?php
/**
 * Contact Administration Form
 * 
 * This file provides a contact form for suspended or banned users
 * to reach out to the administration for assistance.
 */

// Include database connection
require_once __DIR__ . '/config/database.php';

// Initialize variables
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $user_id = trim($_POST['user_id'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validate input
    if (empty($name) || empty($email) || empty($user_id) || empty($subject) || empty($message)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        // Initialize Database
        $db = Database::getInstance();
        
        // Insert complaint/contact message
        $data = [
            'name' => $name,
            'email' => $email,
            'user_id' => $user_id,
            'subject' => $subject,
            'message' => $message,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $result = $db->insert('contact_messages', $data);
        
        if ($result) {
            $success_message = 'Your message has been sent successfully. The administration will review your request and contact you soon.';
        } else {
            $error_message = 'Failed to send your message. Please try again later.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Administration - School Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/modern-theme.css">
    <style>
        body {
            background-color: white;
            color: black;
        }
        .card {
            background-color: white;
            color: black;
            border: 1px solid #e5e7eb;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-primary text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">School Management System</h1>
            <a href="index.php" class="text-white hover:underline">Back to Home</a>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="flex-grow container mx-auto p-4">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-center mb-6">Contact Administration</h2>
            
            <?php if (!empty($success_message)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p><?php echo htmlspecialchars($success_message); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4">
                <p>If your account has been suspended or banned, please use this form to contact the administration for assistance.</p>
                <p class="mt-2">Please provide your account details and explain the situation clearly.</p>
            </div>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="user_id" class="form-label">User ID</label>
                    <input type="text" id="user_id" name="user_id" class="form-control" required>
                    <p class="text-sm text-gray-600 mt-1">Please enter your User ID as provided during registration</p>
                </div>
                
                <div class="form-group">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" id="subject" name="subject" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="message" class="form-label">Message</label>
                    <textarea id="message" name="message" rows="5" class="form-control" required></textarea>
                    <p class="text-sm text-gray-600 mt-1">Please explain why you believe your account should be reinstated</p>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-full">Submit Request</button>
                </div>
            </form>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-4">
        <div class="container mx-auto text-center">
            <p>&copy; <?php echo date('Y'); ?> Badimalika Secondary School. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>