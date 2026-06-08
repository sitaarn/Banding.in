<?php
/**
 * ============================================
 * FRONT CONTROLLER / ROUTER
 * Praktikum Aplikasi Web - Universitas Tidar
 * ============================================
 * 
 * File ini adalah ENTRY POINT utama aplikasi.
 * Semua request HTTP masuk ke sini (lewat .htaccess rewrite)
 * lalu diarahkan ke Controller dan method yang sesuai
 * berdasarkan URL path dan HTTP method (GET/POST).
 * 
 * Alur: Browser → .htaccess → index.php → Route → Controller → View
 */

// Load semua konfigurasi, helper, dan class yang dibutuhkan
require_once __DIR__ . '/config/config.php';

// Import class Route dan semua Controller
use Includes\Route;
use Controllers\AuthController;
use Controllers\LandingController;
use Controllers\SellerController;
use Controllers\AdminController;

// Buat instance router untuk mendaftarkan semua route
$route = new Route();

// Mulai session PHP (untuk login state, flash messages, dll)
startSession();

/**
 * ─── ROUTE GROUP: GUEST (belum login) ──────────────
 * Halaman yang hanya bisa diakses saat BELUM login.
 * Termasuk landing page, about, list produk, login & register.
 */
$route->middleware(['guest'])->group(function ($childClass) {
    $childClass->get('/', [LandingController::class, 'index']);          // Halaman utama
    $childClass->get('/landing', [LandingController::class, 'index']);   // Alias landing page
    $childClass->get('/aboutus', [LandingController::class, 'aboutus']);  // Halaman tentang kami
    $childClass->get('/list', [LandingController::class, 'list']);        // Halaman daftar & cari produk
    $childClass->get('/login', [LandingController::class, 'login']);      // Form login & register
    $childClass->post('/login', [AuthController::class, 'authenticaton']); // Proses login
    $childClass->post('/register', [AuthController::class, 'storeUser']); // Proses registrasi
});

/**
 * ─── ROUTE GROUP: LOGIN (sudah login, semua role) ──
 * Halaman yang bisa diakses oleh SEMUA user yang sudah login
 * (user biasa, seller, admin, super_admin).
 */
$route->middleware(['login'])->group(function ($childClass) {
    $childClass->get('/', [LandingController::class, 'index']);               // Landing page
    $childClass->get('/landing', [LandingController::class, 'index']);        // Alias landing
    $childClass->get('/aboutus', [LandingController::class, 'aboutus']);      // Tentang kami
    $childClass->get('/profile', [AuthController::class, 'profile']);          // Halaman profil user
    $childClass->get('/list', [LandingController::class, 'list']);            // Daftar & cari produk
    $childClass->get('/logout', [AuthController::class, 'logout']);           // Proses logout
    $childClass->get('/admin', [LandingController::class, 'admin']);          // Halaman admin (legacy)
    $childClass->post('/profile/update', [AuthController::class,'updateProfil']); // Update data profil
    $childClass->post('/product/report', [AdminController::class, 'submitReport']); // Laporkan produk
});

/**
 * ─── ROUTE GROUP: USER BIASA ──────────────────────
 * Halaman khusus untuk user role 'user' (pembeli).
 * Fitur favorit hanya tersedia untuk pembeli, bukan seller/admin.
 */
$route->middleware(['login', 'user'])->group(function ($childClass) {
    $childClass->get('/favorit', [LandingController::class, 'favorit']);          // Halaman favorit
    $childClass->post('/favorit/toggle', [LandingController::class, 'favoritbarang']); // Toggle favorit (tambah/hapus)
    $childClass->get('/favorites', [LandingController::class, 'getFavorites']);   // API: ambil data favorit (JSON)
});

/**
 * ─── ROUTE GROUP: SELLER ──────────────────────────
 * Halaman khusus untuk user role 'seller' (penjual).
 * Seller bisa menambah produk dan mengelola produk mereka.
 */
$route->middleware(['login', 'seller'])->group(function ($childClass) {
    $childClass->get('/seller/add', [SellerController::class, 'addProduct']);         // Form tambah produk
    $childClass->post('/seller/store', [SellerController::class, 'storeProduct']);    // Simpan produk baru
    $childClass->get('/seller/products', [SellerController::class, 'myProducts']);    // Daftar produk seller
    $childClass->post('/seller/delete-product', [SellerController::class, 'deleteProduct']); // Hapus produk
});

/**
 * ─── ROUTE GROUP: SUPER ADMIN ─────────────────────
 * Halaman khusus untuk Super Admin.
 * Mengelola seluruh sistem: users, platforms, scraper, logs, reports, produk.
 */
$route->middleware(['login', 'super_admin'])->group(function ($childClass) {
    // Dashboard - Ringkasan statistik sistem
    $childClass->get('/admin/dashboard', [AdminController::class, 'dashboard']);
    
    // User Management - CRUD dan kelola user
    $childClass->get('/admin/users', [AdminController::class, 'users']);              // Daftar user
    $childClass->post('/admin/users/update-role', [AdminController::class, 'updateRole']); // Ubah role user
    $childClass->post('/admin/users/toggle-active', [AdminController::class, 'toggleActive']); // Aktif/nonaktif user
    $childClass->post('/admin/users/delete', [AdminController::class, 'deleteUser']); // Hapus user
    $childClass->post('/admin/users/reset-password', [AdminController::class, 'resetPassword']); // Reset password user
    
    // Platform Management - Kelola e-commerce platform
    $childClass->get('/admin/platforms', [AdminController::class, 'platforms']);       // Daftar platform
    $childClass->post('/admin/platforms/toggle', [AdminController::class, 'togglePlatform']); // Toggle aktif platform
    $childClass->post('/admin/platforms/create', [AdminController::class, 'createPlatform']); // Tambah platform baru
    
    // Scraper Management - Trigger dan pantau web scraper
    $childClass->get('/admin/scraper', [AdminController::class, 'scraper']);          // Halaman scraper
    $childClass->post('/admin/scraper/trigger', [AdminController::class, 'triggerScrape']); // Jalankan scraper
    
    // Activity Logs - Riwayat aktivitas system
    $childClass->get('/admin/logs', [AdminController::class, 'logs']);
    
    // Report Logs - Kelola laporan produk dari user
    $childClass->get('/admin/reports', [AdminController::class, 'reports']);           // Daftar report
    $childClass->post('/admin/reports/update', [AdminController::class, 'updateReport']); // Update status report

    // Product Verification - Verifikasi produk dari seller
    $childClass->get('/admin/products', [AdminController::class, 'products']);        // Daftar produk
    $childClass->post('/admin/products/verify', [AdminController::class, 'verifyProduct']); // Approve/reject produk
    $childClass->post('/admin/products/delete', [AdminController::class, 'deleteProduct']); // Hapus produk
    $childClass->post('/admin/products/delete-multiple', [AdminController::class, 'deleteMultipleProducts']); // Hapus beberapa produk terpilih
});

/**
 * ─── ROUTE GROUP: PUBLIC API (semua user) ─────────
 * Endpoint yang bisa diakses siapa saja (login maupun tidak).
 * Digunakan untuk API data produk, ganti bahasa, dan cek username.
 */
$route->middleware(['all'])->group(function ($childClass) {
    $prefix = 'api';

    // API: Ambil semua data produk (JSON) untuk frontend
    $childClass->get("/$prefix/get-all-data", [LandingController::class, 'getAllData']);
    
    // Ganti bahasa (ID/EN) via query parameter ?lang=id|en
    $childClass->get('/lang/switch', [LandingController::class, 'switchLanguage']);

    // API: Cek apakah username sudah dipakai (untuk validasi real-time saat register)
    $childClass->get('/api/check-username', [LandingController::class, 'checkUsername']);
});
