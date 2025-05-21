<?php
/**
 * Bootstrap File
 * 
 * This file determines which database configuration to use based on the environment
 * It will use the Render-specific configuration when deployed to Render.com
 */

// Load environment variables
require_once __DIR__ . '/../env_loader.php';

// Determine if we're running on Render.com
$isRender = (getenv('RENDER') === 'true');

// Load the appropriate database configuration
if ($isRender || getenv('DB_HOST')) {
    // We're on Render.com or environment variables are set
    require_once __DIR__ . '/database.render.php';
} else {
    // We're in local development
    require_once __DIR__ . '/database.php';
}

// Additional bootstrap logic can be added here