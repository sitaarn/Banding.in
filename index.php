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

$route = new Route();



// Mulai session
startSession();

// $route untuk mengatur hak akses

// Routing


$route->middleware(['guest'])->group(function ($childClass) {
    $childClass->get('/landing', [LandingController::class, 'index']);
    $childClass->get('/search', [LandingController::class, 
        'searching']);
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
});

$route->middleware(['login'])->group(function ($childClass) {
    $childClass->get('/landing', [LandingController::class, 
        'index']);
    $childClass->get('/search', [LandingController::class, 
        'searching']);
    $childClass->get('/aboutus', [LandingController::class, 
        'aboutus']);
    $childClass->get('/profile', [AuthController::class, 'profile']); 
    $childClass->get('/list', [LandingController::class, 
        'list']);
    $childClass->get('/logout', [AuthController::class, 
        'logout']);
    $childClass->get('/favorit', [LandingController::class, 
        'favorit']);
    $childClass->post('/favorit/toggle', [LandingController::class, 'favoritbarang']);
    $childClass->get('/admin', [LandingController::class, 
        'admin']);
    $childClass->get('/favorites', [LandingController::class, 'getFavorites']);
    $childClass->post('/profile/update', [AuthController::class,'updateProfil']); 
});

$route->middleware(['all'])->group(function ($childClass) {
    $prefix = 'api';

    $childClass->get("/$prefix/get-all-data", [LandingController::class, 'getAllData']);
});


