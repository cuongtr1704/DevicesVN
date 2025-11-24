<?php
/**
 * Database Configuration
 * Using .env variables for security
 */

// Database Credentials
define('DB_HOST', Env::get('DB_HOST', 'localhost'));
define('DB_NAME', Env::get('DB_DATABASE', 'devicesvn'));
define('DB_USER', Env::get('DB_USERNAME', 'root'));
define('DB_PASS', Env::get('DB_PASSWORD', ''));
define('DB_PORT', Env::get('DB_PORT', 3306));

/**
 * Test database connection
 */
function testDatabaseConnection() {
    try {
        $db = Database::getInstance();
        return $db->getConnection() !== null;
    } catch (Exception $e) {
        return false;
    }
}
