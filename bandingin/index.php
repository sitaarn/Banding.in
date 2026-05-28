<?php
/**
 * 
 * FRONT CONTROLLER / ROUTER
 * Praktikum Aplikasi Web - Universitas Tidar
 * 
 *
 * File ini berfungsi sebagai entry point aplikasi
 * dan mengarahkan request ke controller yang sesuai.
 */

// Load konfigurasi
require_once __DIR__ . '/config/config.php';

// Route
use Includes\Route;

// Controllers
use Controllers\AuthController;
use Controllers\LandingController;
use Controllers\SellerController;
use Controllers\AdminController;

$route = new Route();

// Mulai session
startSession();

$route->middleware(['guest'])->group(function ($childClass) {
    $childClass->get('/', [LandingController::class, 'index']);
    $childClass->get('/landing', [LandingController::class, 'index']);
    $childClass->get('/aboutus', [LandingController::class, 
        'aboutus']);
    $childClass->get('/list', [LandingController::class, 
        'list']);
    $childClass->get('/login', [LandingController::class, 
        'login']);
    $childClass->post('/login', [AuthController::class, 
        'authenticaton']);
    $childClass->post('/register', [AuthController::class, 
        'storeUser']);
    
    // Forgot Password Routes
    $childClass->get('/forgot-password', [AuthController::class, 'forgotPassword']);
    $childClass->post('/forgot-password', [AuthController::class, 'sendResetLink']);
    $childClass->get('/reset-password', [AuthController::class, 'resetPasswordForm']);
    $childClass->post('/reset-password', [AuthController::class, 'updatePassword']);
});

$route->middleware(['login'])->group(function ($childClass) {
    $childClass->get('/', [LandingController::class, 'index']);
    $childClass->get('/landing', [LandingController::class, 
        'index']);
    $childClass->get('/aboutus', [LandingController::class, 
        'aboutus']);
    $childClass->get('/profile', [AuthController::class, 'profile']); 
    $childClass->get('/list', [LandingController::class, 
        'list']);
    $childClass->get('/logout', [AuthController::class, 
        'logout']);
    $childClass->get('/admin', [LandingController::class, 
        'admin']);
    $childClass->post('/profile/update', [AuthController::class,'updateProfil']); 
});

$route->middleware(['login', 'user'])->group(function ($childClass) {
    $childClass->get('/favorit', [LandingController::class, 
        'favorit']);
    $childClass->post('/favorit/toggle', [LandingController::class, 'favoritbarang']);
    $childClass->get('/favorites', [LandingController::class, 'getFavorites']);
});

$route->middleware(['login', 'seller'])->group(function ($childClass) {
    $childClass->get('/seller/add', [SellerController::class, 'addProduct']);
    $childClass->post('/seller/store', [SellerController::class, 'storeProduct']);
});

// ─── Super Admin Routes ────────────────────────
$route->middleware(['login', 'super_admin'])->group(function ($childClass) {
    // Dashboard
    $childClass->get('/admin/dashboard', [AdminController::class, 'dashboard']);
    
    // User Management
    $childClass->get('/admin/users', [AdminController::class, 'users']);
    $childClass->post('/admin/users/update-role', [AdminController::class, 'updateRole']);
    $childClass->post('/admin/users/toggle-active', [AdminController::class, 'toggleActive']);
    $childClass->post('/admin/users/delete', [AdminController::class, 'deleteUser']);
    $childClass->post('/admin/users/reset-password', [AdminController::class, 'resetPassword']);
    
    // Platform Management
    $childClass->get('/admin/platforms', [AdminController::class, 'platforms']);
    $childClass->post('/admin/platforms/toggle', [AdminController::class, 'togglePlatform']);
    $childClass->post('/admin/platforms/create', [AdminController::class, 'createPlatform']);
    
    // Scraper Management
    $childClass->get('/admin/scraper', [AdminController::class, 'scraper']);
    $childClass->post('/admin/scraper/trigger', [AdminController::class, 'triggerScrape']);
    
    // Activity Logs
    $childClass->get('/admin/logs', [AdminController::class, 'logs']);
    
    // Reports
    $childClass->get('/admin/reports', [AdminController::class, 'reports']);
    $childClass->post('/admin/reports/update', [AdminController::class, 'updateReport']);
    
    // Product Verification
    $childClass->get('/admin/products', [AdminController::class, 'products']);
    $childClass->post('/admin/products/verify', [AdminController::class, 'verifyProduct']);
    $childClass->post('/admin/products/delete', [AdminController::class, 'deleteProduct']);
    $childClass->post('/admin/products/bulk-delete', [AdminController::class, 'bulkDelete']);
});

// ─── Product Report (logged-in users) ──────────
$route->middleware(['login'])->group(function ($childClass) {
    $childClass->post('/product/report', [AdminController::class, 'submitReport']);
});

$route->middleware(['all'])->group(function ($childClass) {
    $prefix = 'api';

    $childClass->get("/$prefix/get-all-data", [LandingController::class, 'getAllData']);
    
    // Language Switcher Route
    $childClass->get('/lang/switch', [LandingController::class, 'switchLanguage']);
});
