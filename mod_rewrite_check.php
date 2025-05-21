<?php
/**
 * mod_rewrite Diagnostic Tool for Render.com
 * 
 * This file helps diagnose if mod_rewrite is enabled on your Render.com deployment
 */

// Display all errors for diagnostic purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Basic header
echo "<html><head><title>mod_rewrite Check for Render.com</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;line-height:1.6} .success{color:green;} .error{color:red;}</style>";
echo "</head><body>";

echo "<h1>Apache mod_rewrite Diagnostic</h1>";

// Check if mod_rewrite is loaded
echo "<h2>mod_rewrite Status</h2>";

$loaded_modules = apache_get_modules();
$mod_rewrite_loaded = in_array('mod_rewrite', $loaded_modules);

if ($mod_rewrite_loaded) {
    echo "<p class='success'><strong>✓ mod_rewrite is loaded!</strong> Your .htaccess RewriteEngine directives should work.</p>";
} else {
    echo "<p class='error'><strong>✗ mod_rewrite is NOT loaded!</strong> Your .htaccess RewriteEngine directives will not work.</p>";
    echo "<p>You need to ensure mod_rewrite is enabled in your Apache configuration.</p>";
}

// Display all loaded Apache modules
echo "<h2>All Loaded Apache Modules</h2>";
echo "<ul>";
sort($loaded_modules);
foreach ($loaded_modules as $module) {
    $highlight = ($module === 'mod_rewrite') ? ' style="font-weight:bold;color:green;"' : '';
    echo "<li{$highlight}>{$module}</li>";
}
echo "</ul>";

// Provide recommendations
echo "<h2>Recommendations</h2>";
echo "<p>If mod_rewrite is not loaded and you're using Render.com:</p>";
echo "<ol>";
echo "<li>Ensure your .htaccess file uses <code>&lt;IfModule mod_rewrite.c&gt;</code> around RewriteEngine directives</li>";
echo "<li>Check your render.yaml configuration to ensure Apache is properly configured</li>";
echo "<li>Contact Render.com support if you continue to have issues</li>";
echo "</ol>";

echo "<p><a href='phpinfo_render.php'>View Full PHP Info</a></p>";

echo "</body></html>";
?>