<?php
/**
 * Application Configuration
 * Using .env variables for flexibility
 */

// Application Settings
define('APP_NAME', Env::get('APP_NAME', 'DevicesVN'));
define('APP_VERSION', '1.0.0');
define('APP_ENV', Env::get('APP_ENV', 'development'));
define('APP_DEBUG', Env::get('APP_DEBUG', true));
define('APP_KEY', Env::get('APP_KEY', 'default-insecure-key'));

// URL Settings
define('BASE_URL', rtrim(Env::get('APP_URL', 'http://localhost/devicesvn/'), '/') . '/');
define('ASSETS_URL', BASE_URL);

// Path Settings
define('UPLOAD_PATH', PUBLIC_PATH . '/storage/uploads/');
define('UPLOAD_URL', BASE_URL . 'storage/uploads/');

// Pagination Settings
define('PRODUCTS_PER_PAGE', Env::get('PRODUCTS_PER_PAGE', 12));
define('SEARCH_RESULTS_LIMIT', Env::get('SEARCH_RESULTS_PER_PAGE', 20));

// Security Settings
define('PASSWORD_MIN_LENGTH', 8);
define('SESSION_LIFETIME', Env::get('SESSION_LIFETIME', 7200));
define('SESSION_NAME', Env::get('SESSION_NAME', 'devicesvn_session'));

// Upload Settings
define('MAX_UPLOAD_SIZE', Env::get('MAX_UPLOAD_SIZE', 5242880)); // 5MB
define('ALLOWED_IMAGE_TYPES', Env::get('ALLOWED_IMAGE_TYPES', 'jpg,jpeg,png,gif,webp'));

// Google Maps API
define('GOOGLE_MAPS_API_KEY', Env::get('GOOGLE_MAPS_API_KEY', ''));

// Social Login Configuration
define('GOOGLE_CLIENT_ID', Env::get('GOOGLE_CLIENT_ID', ''));
define('GOOGLE_CLIENT_SECRET', Env::get('GOOGLE_CLIENT_SECRET', ''));
define('GOOGLE_REDIRECT_URI', BASE_URL . 'auth/google-callback');

define('FACEBOOK_APP_ID', Env::get('FACEBOOK_APP_ID', ''));
define('FACEBOOK_APP_SECRET', Env::get('FACEBOOK_APP_SECRET', ''));
define('FACEBOOK_REDIRECT_URI', BASE_URL . 'auth/facebook-callback');

// Email Configuration
define('SMTP_HOST', Env::get('MAIL_HOST', 'smtp.gmail.com'));
define('SMTP_PORT', Env::get('MAIL_PORT', 587));
define('SMTP_USER', Env::get('MAIL_USERNAME', ''));
define('SMTP_PASS', Env::get('MAIL_PASSWORD', ''));
define('SMTP_FROM', Env::get('MAIL_FROM_ADDRESS', 'noreply@devicesvn.com'));
define('SMTP_FROM_NAME', Env::get('MAIL_FROM_NAME', 'DevicesVN'));

// SEO Settings
define('META_DESCRIPTION', 'DevicesVN - Your trusted online store for laptops, phones, gaming devices, and computer accessories');
define('META_KEYWORDS', 'laptops, phones, gaming laptops, computer accessories, electronics, Vietnam');
define('META_AUTHOR', 'DevicesVN Team');

// Error Reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');
