<?php
/**
 * DevicesVN - E-commerce Platform
 * Main Entry Point (inside public folder)
 */

// Start session
session_start();

// Define paths
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('PUBLIC_PATH', __DIR__);

// Load environment variables first
require_once ROOT_PATH . '/app/core/Env.php';
Env::load(ROOT_PATH . '/.env');

// Load configuration
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/config/app.php';

// Load application logic
require_once ROOT_PATH . '/app/core/App.php';
require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/core/Database.php';
require_once ROOT_PATH . '/app/helpers/functions.php';

// Initialize application with routes
$app = new App();
