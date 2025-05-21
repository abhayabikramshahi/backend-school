<?php
/**
 * PHP Info for Render.com
 * 
 * This file displays PHP configuration information to help diagnose issues
 * on Render.com deployment
 */

// Display all errors for diagnostic purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Basic header
echo "<html><head><title>PHP Info for Render.com</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;line-height:1.6}</style>";
echo "</head><body>";

echo "<h1>PHP Configuration on Render.com</h1>";

// Display PHP version
echo "<h2>PHP Version</h2>";
echo "<p>" . phpversion() . "</p>";

// Display loaded extensions
echo "<h2>Loaded Extensions</h2>";
echo "<ul>";
$extensions = get_loaded_extensions();
sort($extensions);
foreach ($extensions as $ext) {
    echo "<li>{$ext}</li>";
}
echo "</ul>";

// Display environment variables (excluding sensitive ones)
echo "<h2>Environment Variables</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>Name</th><th>Value</th></tr>";
$env_vars = getenv();
ksort($env_vars);
foreach ($env_vars as $name => $value) {
    // Skip sensitive variables
    if (in_array(strtolower($name), ['db_password', 'password', 'secret', 'key', 'token'])) {
        $value = '******** (hidden for security)'; 
    }
    echo "<tr><td>{$name}</td><td>{$value}</td></tr>";
}
echo "</table>";

// Display PHP configuration
echo "<h2>PHP Configuration</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>Directive</th><th>Value</th></tr>";

$important_settings = [
    'display_errors',
    'error_reporting',
    'max_execution_time',
    'memory_limit',
    'post_max_size',
    'upload_max_filesize',
    'default_socket_timeout',
    'allow_url_fopen',
    'date.timezone',
    'session.save_path',
    'session.gc_maxlifetime',
    'pdo.dsn.*'
];

foreach ($important_settings as $directive) {
    $value = ini_get($directive);
    echo "<tr><td>{$directive}</td><td>{$value}</td></tr>";
}
echo "</table>";

// Link to full phpinfo
echo "<h2>Full PHP Info</h2>";
echo "<p><a href='#' onclick='document.getElementById(\"fullphpinfo\").style.display=\"block\";return false;'>Show Full PHP Info</a></p>";
echo "<div id='fullphpinfo' style='display:none'>";
ob_start();
phpinfo();
$phpinfo = ob_get_clean();

// Strip the head, title and meta tags
$phpinfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $phpinfo);

echo $phpinfo;
echo "</div>";

echo "</body></html>";
?>