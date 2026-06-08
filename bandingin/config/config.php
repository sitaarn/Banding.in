<?php
/**
 * ============================================
 * KONFIGURASI APLIKASI - Bandingin
 * Praktikum Aplikasi Web - Universitas Tidar
 * ============================================
 * 
 * File ini berisi semua konfigurasi global aplikasi:
 * - Environment (dev/prod)
 * - Base URL & path
 * - Upload settings
 * - Session & cookie
 * - Autoload file helper
 */

// ── Environment ──
// 'development' = tampilkan error, 'production' = sembunyikan error
define('ENVIRONMENT', 'development');

// ── Error Reporting ──
// Development: tampilkan semua error di browser (bantu debugging)
// Production: sembunyikan error dari user, simpan ke log file
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
}

// ── Base URL ──
// URL dasar aplikasi, digunakan untuk redirect, link asset, dll.
// FIRSTSECTION_URI = path prefix di server (misal /bandingin)
if (ENVIRONMENT === 'development') {
    define('BASE_URL', 'https://retrial-blaspheme-juice.ngrok-free.dev/bandingin/');
    define('FIRSTSECTION_URI', '/bandingin');
} else {
    define('BASE_URL', 'https://yourdomain.com/');
    define('FIRSTSECTION_URI', '/bandingin');
}

// ── Identitas Aplikasi ──
define('APP_NAME', 'Bandingin');
define('APP_VERSION', '1.0.0');

// ── Path Direktori ──
// Konstanta path ke setiap folder utama agar mudah diakses dari mana saja
define('ROOT_PATH', dirname(__DIR__) . '/');
define('CONFIG_PATH', ROOT_PATH . 'config/');
define('MODELS_PATH', ROOT_PATH . 'models/');
define('VIEWS_PATH', ROOT_PATH . 'views/');
define('CONTROLLERS_PATH', ROOT_PATH . 'controllers/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');
define('PUBLIC_PATH', ROOT_PATH . 'public/');

// ── Konfigurasi Upload File ──
define('MAX_FILE_SIZE', 2 * 1024 * 1024);  // Maksimal 2MB per file
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']); // Ekstensi gambar yang diizinkan
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'image/gif']); // MIME type yang diizinkan

// ── Session & Cookie ──
define('SESSION_LIFETIME', 3600);       // Session expired setelah 1 jam
define('COOKIE_LIFETIME', 86400 * 30);  // Cookie "remember me" bertahan 30 hari

// ── Timezone ──
date_default_timezone_set('Asia/Jakarta');

// ── Load Dependencies ──
// Muat file-file yang dibutuhkan secara global di seluruh aplikasi
require_once CONFIG_PATH . 'database.php';   // Koneksi database (PDO Singleton)
require_once INCLUDES_PATH . 'init.php';      // Autoloader class & helper umum
require_once INCLUDES_PATH . 'auth.php';      // Fungsi autentikasi & session
require_once INCLUDES_PATH . 'validation.php'; // Class Validator untuk validasi input
require_once INCLUDES_PATH . 'FileHandler.php'; // Class upload & kelola file
require_once INCLUDES_PATH . 'view.php';      // Fungsi render view/template
require_once INCLUDES_PATH . 'Route.php';     // Class routing (URL → Controller)
require_once INCLUDES_PATH . 'lang.php';      // Sistem multi-bahasa (ID/EN)
