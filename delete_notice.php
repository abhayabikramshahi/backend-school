<?php
// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=look", "root", "");

// Ensure delete_id is provided
if (!isset($_GET['delete_id'])) {
    header("Location: manage_notices.php");
    exit();
}

$deleteId = (int)$_GET['delete_id'];

// Fetch the notice to check if it exists
$stmt = $pdo->prepare("SELECT * FROM notices WHERE id = ?");
$stmt->execute([$deleteId]);
$notice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$notice) {
    // If the notice doesn't exist, redirect with an error
    header("Location: manage_notices.php?error=Notice not found.");
    exit();
}

// Delete the image file if it exists
if (!empty($notice['image_path']) && file_exists($notice['image_path'])) {
    unlink($notice['image_path']);
}

// Delete the notice from the database
$stmt = $pdo->prepare("DELETE FROM notices WHERE id = ?");
$stmt->execute([$deleteId]);

// Redirect back to the manage notices page with a success message
header("Location: manage_notices.php?success=Notice deleted successfully.");
exit();
