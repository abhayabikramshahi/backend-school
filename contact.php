<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = htmlspecialchars($_POST['name']);
    $age = htmlspecialchars($_POST['age']);
    $address = htmlspecialchars($_POST['address']);
    $email_or_phone = htmlspecialchars($_POST['email_or_phone']);
    $message = htmlspecialchars($_POST['message']);
    
    // Email settings
    $to = "abhayabikramshahiofficial@gmail.com";
    $subject = "Contact Us Form Submission";
    $body = "
    Name: $name\n
    Age: $age\n
    Address: $address\n
    Email or Phone: $email_or_phone\n
    Message: $message\n
    ";
    
    // Headers for the email
    $headers = "From: no-reply@yourdomain.com" . "\r\n" . "Reply-To: $email_or_phone" . "\r\n" . "Content-Type: text/plain; charset=UTF-8";

    // Send email
    if (mail($to, $subject, $body, $headers)) {
        $success_message = "Thank you for contacting us. We will get back to you soon.";
    } else {
        $error_message = "There was an error submitting the form. Please try again later.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="https://cdn.tailwindcss.com">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6 bg-white rounded-lg shadow-md mt-6">
        <h1 class="text-2xl font-bold text-center text-blue-600 mb-6">Contact Us</h1>

        <?php if (isset($success_message)): ?>
            <div class="bg-green-500 text-white p-4 mb-4 rounded">
                <?php echo $success_message; ?>
            </div>
        <?php elseif (isset($error_message)): ?>
            <div class="bg-red-500 text-white p-4 mb-4 rounded">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

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
