<?php
// Include the PHPMailer files
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = htmlspecialchars($_POST['name']);
    $age = htmlspecialchars($_POST['age']);
    $address = htmlspecialchars($_POST['address']);
    $email_or_phone = htmlspecialchars($_POST['email_or_phone']);
    $message = htmlspecialchars($_POST['message']);

    // Database connection (PDO)
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=your_database_name", "your_db_username", "your_db_password");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Insert form data into the database
        $stmt = $pdo->prepare("INSERT INTO contact_form (name, age, address, email_or_phone, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $age, $address, $email_or_phone, $message]);

        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your_email@gmail.com'; // Your Gmail address
            $mail->Password = 'your_email_password'; // Your Gmail app password (generate it from Google account settings)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587; // SMTP port for Gmail

            // Recipients
            $mail->setFrom('your_email@gmail.com', 'Your Name or Organization');
            $mail->addAddress('abhayabikramshahiofficial@gmail.com'); // Recipient's email address

            // Content
            $mail->isHTML(false);
            $mail->Subject = 'Contact Us Form Submission';
            $mail->Body    = "
                Name: $name\n
                Age: $age\n
                Address: $address\n
                Email or Phone: $email_or_phone\n
                Message: $message\n
            ";

            // Send email
            if ($mail->send()) {
                echo "Thank you for contacting us. We will get back to you soon.";
            } else {
                echo "There was an error sending your message.";
            }
        } catch (Exception $e) {
            echo "Error: " . $mail->ErrorInfo;
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="https://cdn.tailwindcss.com">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6 bg-white rounded-lg shadow-md mt-6">
        <h1 class="text-2xl font-bold text-center text-blue-600 mb-6">Contact Us</h1>

        <form action="contact.php" method="POST">
            <div class="mb-4">
                <label for="name" class="block text-sm font-semibold text-gray-700">Name (Student/Parent)</label>
                <input type="text" id="name" name="name" class="w-full p-2 mt-1 border rounded-md" required>
            </div>

            <div class="mb-4">
                <label for="age" class="block text-sm font-semibold text-gray-700">Age</label>
                <input type="number" id="age" name="age" class="w-full p-2 mt-1 border rounded-md" required>
            </div>

            <div class="mb-4">
                <label for="address" class="block text-sm font-semibold text-gray-700">Address</label>
                <textarea id="address" name="address" class="w-full p-2 mt-1 border rounded-md" required></textarea>
            </div>

            <div class="mb-4">
                <label for="email_or_phone" class="block text-sm font-semibold text-gray-700">Email or Phone Number</label>
                <input type="text" id="email_or_phone" name="email_or_phone" class="w-full p-2 mt-1 border rounded-md" required>
            </div>

            <div class="mb-4">
                <label for="message" class="block text-sm font-semibold text-gray-700">Message</label>
                <textarea id="message" name="message" class="w-full p-2 mt-1 border rounded-md" required></textarea>
            </div>

            <div class="text-center">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">Submit</button>
            </div>
        </form>
    </div>
</body>
</html>
