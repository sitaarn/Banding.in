<?php
/**
 * ============================================
 * KONFIGURASI DATABASE - Bandingin
 * Praktikum Aplikasi Web - Universitas Tidar
 * ============================================
 * 
 * Menggunakan Singleton Pattern agar hanya ada 1 koneksi
 * database (PDO) yang di-share ke seluruh aplikasi.
 * Ini mencegah koneksi berlebihan ke MySQL.
 */

class Database {
    private static $instance = null; // Menyimpan satu-satunya instance Database
    private $connection;             // Object PDO untuk query ke MySQL

    // ── Kredensial Database ──
    private $host = 'localhost';
    private $dbname = 'bandingin';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';  // Support emoji & karakter unicode penuh

    /**
     * Constructor - Private agar tidak bisa di-new dari luar.
     * Hanya bisa diakses lewat getInstance() (Singleton Pattern).
     * Membuat koneksi PDO ke MySQL dengan error handling.
     */
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // Throw exception saat error
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   // Return array asosiatif
                PDO::ATTR_EMULATE_PREPARES => false                 // Gunakan prepared statement asli
            ];

            $this->connection = new PDO($dsn, $this->username, $this->password, $options);

        } catch (PDOException $e) {
            // Development: tampilkan error, Production: simpan ke log
            if (ENVIRONMENT === 'development') {
                die("Koneksi database gagal: " . $e->getMessage());
            } else {
                error_log("Database Error: " . $e->getMessage());
                die("Terjadi kesalahan sistem. Silakan hubungi administrator.");
            }
        }
    }

    /**
     * Mendapatkan instance Database (Singleton).
     * Jika belum ada instance, buat baru. Jika sudah, return yang existing.
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Mendapatkan object koneksi PDO untuk dipakai query.
     */
    public function getConnection() {
        return $this->connection;
    }

    /** Mencegah cloning instance (bagian dari Singleton Pattern) */
    private function __clone() {}

    /** Mencegah unserialize instance (bagian dari Singleton Pattern) */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Helper function untuk akses koneksi database secara cepat.
 * Penggunaan: $db = getDB(); lalu $db->prepare(...), dll.
 */
function getDB() {
    return Database::getInstance()->getConnection();
}
