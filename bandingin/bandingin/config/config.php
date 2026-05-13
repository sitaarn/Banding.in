<?php
/**
 * 
 * KONFIGURASI APLIKASI
 * Praktikum Aplikasi Web - Universitas Tidar
 * 
 */

// Deteksi environment (development atau production)
define('ENVIRONMENT', 'development'); // Ubah ke 'production' saat hosting

// Konfigurasi Error Reporting
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
}

// Base URL Aplikasi
if (ENVIRONMENT === 'development') {
    define('BASE_URL', 'http://localhost/bandingin/');
    define('FIRSTSECTION_URI', '/bandingin');
} else {
    define('BASE_URL', 'https://yourdomain.com/');
    define('FIRSTSECTION_URI', '/bandingin');
}

// Konfigurasi Aplikasi
define('APP_NAME', 'Bandingin');
define('APP_VERSION', '1.0.0');

// Konfigurasi Path
define('ROOT_PATH', dirname(__DIR__) . '/');
define('CONFIG_PATH', ROOT_PATH . 'config/');
define('MODELS_PATH', ROOT_PATH . 'models/');
define('VIEWS_PATH', ROOT_PATH . 'views/');
define('CONTROLLERS_PATH', ROOT_PATH . 'controllers/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');
define('PUBLIC_PATH', ROOT_PATH . 'public/');

// Konfigurasi Upload
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

// Konfigurasi Session
define('SESSION_LIFETIME', 3600); // 1 jam
define('COOKIE_LIFETIME', 86400 * 30); // 30 hari

// Timezone
date_default_timezone_set('Asia/Jakarta');

// // Include file database
require_once CONFIG_PATH . 'database.php';

// // Include helper functions
require_once INCLUDES_PATH . 'init.php';
require_once INCLUDES_PATH . 'auth.php';
require_once INCLUDES_PATH . 'validation.php';
require_once INCLUDES_PATH . 'FileHandler.php';
require_once INCLUDES_PATH . 'view.php';
require_once INCLUDES_PATH . 'Route.php';

