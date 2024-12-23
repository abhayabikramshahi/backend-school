<?php
session_start();

// Destroy the session to log out
session_destroy();
header("Location: login.php");
exit();
?>
