<?php
/**
 * ============================================
 * MODEL: Favorite
 * Banding.in - Perbandingan Harga E-commerce
 * ============================================
 * 
 * Menangani operasi database untuk tabel `favorites`.
 * User bisa menyimpan produk favorit beserta platform-nya.
 * Contoh: User simpan "iPhone 15" dari platform "tokopedia".
 */
namespace Models;

class Favorite {
    private $db;                   // Object PDO koneksi database
    private $table = 'favorites';  // Nama tabel

    /** Constructor: ambil koneksi database */
    public function __construct() {
        $this->db = getDB();
    }

    /** Cek apakah user sudah punya favorit untuk produk + platform tertentu */
    public function exists($userId, $productId, $platform) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_id = ? AND product_id = ? AND platform = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $productId, $platform]);
        return $stmt->fetchColumn() > 0;
    }

    /** Tambah produk ke favorit (cegah duplikat dengan cek exists dulu) */
    public function add($userId, $productId, $platform) {
        if ($this->exists($userId, $productId, $platform)) {
            return false; // Sudah ada, tidak ditambah lagi
        }
        $sql = "INSERT INTO {$this->table} (user_id, product_id, platform, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $productId, $platform]);
    }

    /** Hapus produk dari favorit (berdasarkan user, produk, dan platform) */
    public function remove($userId, $productId, $platform) {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ? AND product_id = ? AND platform = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $productId, $platform]);
    }

    /**
     * Ambil semua favorit milik user, lengkap dengan harga dari platform yang disimpan.
     * Menggunakan subquery untuk ambil harga sesuai platform favorit.
     * Return array dengan format yang siap pakai di frontend.
     */
    public function getByUser($userId) {
        $sql = "
            SELECT 
                f.product_id,
                p.name AS product_name,
                p.category,
                COALESCE(
                    (SELECT price FROM product_prices 
                     WHERE product_id = p.id 
                     AND platform_id = (SELECT id FROM platforms WHERE LOWER(name) = f.platform LIMIT 1)
                     LIMIT 1),
                    0
                ) AS price,
                f.platform,
                f.created_at
            FROM {$this->table} f
            JOIN products p ON f.product_id = p.id
            WHERE f.user_id = ?
            ORDER BY f.created_at DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll();

        // Format hasil agar konsisten untuk frontend
        $result = [];
        foreach ($rows as $row) {
            $platform = $row['platform'] ?: 'tokopedia'; // Default platform jika kosong
            $result[] = [
                'product_id'   => $row['product_id'],
                'product_name' => $row['product_name'],
                'category'     => $row['category'],
                'price'        => (int)$row['price'],
                'platform'     => $platform,
                'created_at'   => $row['created_at']
            ];
        }
        return $result;
    }
}