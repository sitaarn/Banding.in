<?php
/**
 * 
 * KONFIGURASI DATABASE
 * Praktikum Aplikasi Web - Universitas Tidar
 * 
 */

class Database {
    private static $instance = null;
    private $connection;

    // Konfigurasi Database
    private $host = 'localhost';
    private $dbname = 'bandingin';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';

    /**
     * Constructor - Private untuk Singleton Pattern
     */
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];

            $this->connection = new PDO($dsn, $this->username, $this->password, $options);

        } catch (PDOException $e) {
            if (ENVIRONMENT === 'development') {
                die("Koneksi database gagal: " . $e->getMessage());
            } else {
                error_log("Database Error: " . $e->getMessage());
                die("Terjadi kesalahan sistem. Silakan hubungi administrator.");
            }
        }
    }

    /**
     * Singleton Pattern - Mendapatkan instance database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Mendapatkan koneksi PDO
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Mencegah cloning
     */
    private function __clone() {}

    /**
     * Mencegah unserialize
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Fungsi helper untuk mendapatkan koneksi database
 */
function getDB() {
    return Database::getInstance()->getConnection();
}
