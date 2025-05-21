<?php
/**
 * PHP Information Page
 * 
 * This file displays PHP configuration information for debugging purposes.
 * IMPORTANT: Remove or protect this file in production environments.
 */

// Basic authentication for security
$valid_username = 'admin';
$valid_password = 'render_debug';

// Check if authentication is provided
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] !== $valid_username || $_SERVER['PHP_AUTH_PW'] !== $valid_password) {
    header('WWW-Authenticate: Basic realm="PHP Info Access"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Authentication required to access this page.';
    exit;
}

// Display PHP information
phpinfo();