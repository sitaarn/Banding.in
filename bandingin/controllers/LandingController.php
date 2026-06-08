<?php
/**
 * ============================================
 * CONTROLLER: LandingController
 * Praktikum Aplikasi Web - Universitas Tidar
 * ============================================
 * 
 * Controller untuk halaman-halaman publik/umum:
 * - Landing page, About Us, List produk
 * - Login, Register, Admin, Favorit
 * - API endpoint: data produk, toggle favorit, ganti bahasa, cek username
 */
namespace Controllers;

use Models\User as UserModel;
use Models\Product as ProductModel; 
use Models\Favorite as FavoriteModel;

class LandingController {

    private $ProductModel;   // Model untuk query produk
    private $favoriteModel;  // Model untuk query favorit

    /** Constructor: inisialisasi model yang dibutuhkan */
    public function __construct() {
        $this->ProductModel = new ProductModel();
        $this->favoriteModel = new FavoriteModel();
    }

    // ─── Render Halaman Statis ──────────────────
    /** Tampilkan halaman landing/beranda */
    public function index () { \view('pages/landing', []); }

    /** Tampilkan halaman tentang kami */
    public function aboutus () { \view('pages/aboutus', []); }

    /** Tampilkan halaman profil */
    public function profile () { \view('pages/profile', []); }

    /** Tampilkan halaman daftar & pencarian produk */
    public function list () { \view('pages/list', []); }

    /** Tampilkan halaman login & register */
    public function login () { \view('pages/login', []); }

    /** Tampilkan halaman register (standalone) */
    public function register () { \view('pages/register', []); }

    /** Tampilkan halaman admin panel */
    public function admin () { \view('pages/admin', []); }

    /** Tampilkan halaman favorit */
    public function favorit () { \view('pages/favorit', []); }

    // ─── API: Toggle Favorit ────────────────────
    /**
     * Tambah/hapus produk dari favorit user (toggle).
     * Menerima JSON body: { product_id, platform }
     * Jika sudah favorit → hapus, jika belum → tambah.
     */
    public function favoritbarang() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $product_id = isset($data['product_id']) ? (int)$data['product_id'] : null;
        $platform   = isset($data['platform']) ? $data['platform'] : null;
        $user_id = $_SESSION['user_id'] ?? null;

        // Validasi: semua field harus ada
        if (!$user_id || !$product_id || !$platform) {
            echo json_encode(['success' => false, 'error' => 'Invalid data (user_id, product_id, platform required)']);
            exit;
        }

        // Cek apakah sudah ada di favorit
        $exists = $this->favoriteModel->exists($user_id, $product_id, $platform);

        if ($exists) {
            $success = $this->favoriteModel->remove($user_id, $product_id, $platform);
            $message = 'Dihapus dari favorit';
        } else {
            $success = $this->favoriteModel->add($user_id, $product_id, $platform);
            $message = 'Ditambahkan ke favorit';
        }

        echo json_encode(['success' => $success, 'message' => $message]);
    }

    // ─── API: Get Favorit User ──────────────────
    /** Ambil semua data favorit user yang sedang login (JSON response) */
    public function getFavorites() {
        header('Content-Type: application/json');
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }
        $favorites = $this->favoriteModel->getByUser($user_id);
        echo json_encode(['success' => true, 'data' => $favorites]);
    }

    // ─── API: Get All Products ──────────────────
    /** API endpoint: return semua data produk dalam JSON (dipakai frontend) */
    public function getAllData() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Content-Type: application/json');
        echo json_encode($this->ProductModel->all());
        exit;
    }

    // ─── Switch Language ────────────────────────
    /**
     * Ganti bahasa aplikasi (ID/EN).
     * Query parameter: ?lang=id atau ?lang=en
     * Setelah ganti, redirect balik ke halaman sebelumnya.
     */
    public function switchLanguage() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
        if (in_array($lang, ['id', 'en'])) {
            $_SESSION['lang'] = $lang;
        }
        // Redirect ke halaman sebelumnya (referer)
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL;
        header("Location: " . $referer);
        exit;
    }

    // ─── API: Check Username Availability ───────
    /**
     * Cek apakah username sudah dipakai (untuk validasi real-time saat register).
     * Query parameter: ?username=<value>
     * Return JSON: { exists: true/false }
     */
    public function checkUsername() {
        header('Content-Type: application/json');
        $username = isset($_GET['username']) ? trim($_GET['username']) : '';
        if (strlen($username) < 3) {
            echo json_encode(['exists' => false]);
            exit;
        }
        $userModel = new \Models\User();
        $exists = $userModel->usernameExists($username);
        echo json_encode(['exists' => $exists]);
        exit;
    }
}