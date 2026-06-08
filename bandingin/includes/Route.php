<?php
/**
 * ============================================
 * ROUTE - Sistem Routing Sederhana
 * Praktikum Aplikasi Web - Universitas Tidar
 * ============================================
 * 
 * Class ini menangani routing URL ke Controller.
 * Mendukung:
 * - HTTP GET dan POST
 * - Middleware (cek login, role, dll) 
 * - Route grouping (kelompokkan route dengan middleware sama)
 * 
 * Alur: URL masuk → cek middleware → cocokkan URI → panggil Controller method
 */

namespace Includes;

class Route {

    protected $registerMiddleware; // Daftar middleware yang tersedia (key→callback)
    private $middlewarePassed;     // Flag: apakah semua middleware lolos

    /**
     * Constructor: Daftarkan semua middleware yang bisa dipakai.
     * Setiap middleware adalah closure yang return true/false.
     */
    public function __construct () {
        $this->registerMiddleware = [
            'login' => function () { return \isLoggedIn(); },      // Harus sudah login
            'guest' => function () { return \isGuest(); },          // Harus belum login
            'admin' => function () { return \isAdmin(); },          // Harus role admin
            'super_admin' => function () { return \isSuperAdmin(); }, // Harus role super_admin
            'seller' => function () { return \isSeller(); },        // Harus role seller
            'user' => function () { return \isStandardUser(); },    // Harus role user biasa
            'all' => function () { return true; }                   // Izinkan semua (tanpa syarat)
        ];   
    }

    /**
     * Daftarkan route GET. Cocokkan request method & URI,
     * lalu panggil controller method jika cocok.
     * $action[0] = class Controller, $action[1] = method name
     */
    public function get($uri, $action) {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if(!$this->middlewarePassed) return false; // Middleware gagal → skip

        if ( $_SERVER['REQUEST_METHOD'] !== 'GET' || $requestUri !==  \FIRSTSECTION_URI . $uri ) return false;
        // Panggil method di controller: new Controller()->method()
        call_user_func_array([new $action[0], $action[1]], []);
    }

    /**
     * Daftarkan route POST. Sama seperti get() tapi untuk method POST.
     */
    public function post($uri, $action) {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if(!$this->middlewarePassed) return false;

        if ( $_SERVER['REQUEST_METHOD'] !== 'POST' || $requestUri !==  \FIRSTSECTION_URI . $uri ) return false;
        call_user_func_array([new $action[0], $action[1]], []);
    }

    /**
     * Menjalankan sekelompok route di dalam satu callback.
     * Semua route di dalam group akan berbagi middleware yang sama.
     */
    public function group($action) {
        $action($this);
    }

    /**
     * Set middleware untuk route group berikutnya.
     * Jika salah satu middleware gagal, semua route di group akan di-skip.
     * Return $this agar bisa di-chain: $route->middleware([...])->group(...)
     */
    public function middleware($middleware) {
        $this->middlewarePassed = true;
        
        foreach ($middleware as $name) {
            $condition = $this->registerMiddleware[$name]();
            if (!$condition) {
                $this->middlewarePassed = false;
                break; // Satu gagal = langsung stop, tidak perlu cek sisanya
            }
        }
        return $this;
    }
}