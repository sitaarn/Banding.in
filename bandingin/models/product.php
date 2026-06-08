<?php
/**
 * ============================================
 * MODEL: Product
 * Banding.in - Perbandingan Harga E-commerce
 * ============================================
 * 
 * Menangani operasi database untuk tabel `products`.
 * Termasuk CRUD, pencarian, filter berdasarkan status/seller,
 * verifikasi produk, dan bulk operations.
 */
namespace Models;

class Product {
    private $db;                  // Object PDO koneksi database
    private $table = 'products';  // Nama tabel

    /** Constructor: ambil koneksi database */
    public function __construct() {
        $this->db = getDB();
    }

    /** Ambil produk berdasarkan ID */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Ambil semua produk milik seller tertentu (dengan harga dan platform) */
    public function getBySellerId($sellerId) {
        try {
            $sql = "SELECT p.*, pp.price, pp.link, pf.name AS platform_name, pf.id AS platform_id
                    FROM {$this->table} p
                    LEFT JOIN product_prices pp ON pp.product_id = p.id
                    LEFT JOIN platforms pf ON pp.platform_id = pf.id
                    WHERE p.seller_id = ?
                    ORDER BY p.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$sellerId]);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }

    /** Cari produk berdasarkan keyword (hanya yang approved/null) */
    public function search($keyword) {
        $sql = "SELECT * FROM {$this->table} WHERE name LIKE ? AND (status = 'approved' OR status IS NULL) ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["%{$keyword}%"]);
        return $stmt->fetchAll();
    }

    /**
     * Ambil semua produk beserta harga dan platform (untuk API/frontend).
     * Hanya produk yang approved atau status NULL.
     * Join: products → product_prices → platforms
     */
    public function all() {
        $sql = "SELECT 
        p.name AS product_name,
        p.id,
        p.category,
        pf.name AS platform_name,
        pp.price,
        pp.link
        FROM product_prices pp
        JOIN products p ON pp.product_id = p.id
        JOIN platforms pf ON pp.platform_id = pf.id
        WHERE (p.status = 'approved' OR p.status IS NULL)";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /** Insert produk baru. Return ID produk atau false. */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, category, seller_id, status) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['name'], 
            $data['category'] ?? null,
            $data['seller_id'] ?? null,
            $data['status'] ?? 'approved'  // Default approved (dari scraper), pending (dari seller)
        ]);
        if ($result) return $this->db->lastInsertId();
        return false;
    }

    /** Update produk (dinamis, hanya field yang dikirim) */
    public function update($id, $data) {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $params[] = $value;
        }
        $params[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /** Hapus produk beserta harga terkait di product_prices */
    public function delete($id) {
        // Hapus harga dulu (foreign key)
        $sql = "DELETE FROM product_prices WHERE product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        // Lalu hapus produk
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /** Ambil produk pending (menunggu verifikasi admin), dengan data seller dan platform */
    public function getPending() {
        try {
            $sql = "SELECT p.*, u.username AS seller_name, pp.price, pp.link, pf.name AS platform_name
                    FROM {$this->table} p
                    LEFT JOIN users u ON p.seller_id = u.id
                    LEFT JOIN product_prices pp ON pp.product_id = p.id
                    LEFT JOIN platforms pf ON pp.platform_id = pf.id
                    WHERE p.status = 'pending'
                    ORDER BY p.created_at DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }

    /** Ambil semua produk dengan semua status (untuk admin panel) */
    public function getAllWithStatus() {
        try {
            $sql = "SELECT p.*, u.username AS seller_name, pp.price, pp.link, pf.name AS platform_name
                    FROM {$this->table} p
                    LEFT JOIN users u ON p.seller_id = u.id
                    LEFT JOIN product_prices pp ON pp.product_id = p.id
                    LEFT JOIN platforms pf ON pp.platform_id = pf.id
                    ORDER BY p.created_at DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            // Fallback: query tanpa seller_id/status jika kolom belum ada
            try {
                $sql = "SELECT p.*, pp.price, pp.link, pf.name AS platform_name
                        FROM {$this->table} p
                        LEFT JOIN product_prices pp ON pp.product_id = p.id
                        LEFT JOIN platforms pf ON pp.platform_id = pf.id
                        ORDER BY p.id DESC";
                $stmt = $this->db->query($sql);
                return $stmt->fetchAll();
            } catch (\Exception $e2) {
                return [];
            }
        }
    }

    /** Update status verifikasi produk (approved/rejected/taken_down/pending) */
    public function updateStatus($id, $status) {
        $sql = "UPDATE {$this->table} SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    /**
     * Hapus semua produk dari satu platform (bulk delete).
     * Pertama hapus harga, lalu hapus produk orphan (yang tidak punya harga lagi).
     */
    public function bulkDeleteByPlatform($platformId) {
        // Hapus semua harga dari platform ini
        $sql = "DELETE pp FROM product_prices pp WHERE pp.platform_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$platformId]);

        // Hapus produk yang tidak punya harga lagi (orphan)
        $sql = "DELETE p FROM products p LEFT JOIN product_prices pp ON p.id = pp.product_id WHERE pp.id IS NULL";
        $stmt = $this->db->query($sql);

        return true;
    }

    /** Hitung jumlah produk berdasarkan status (misal: 'pending', 'approved') */
    public function countByStatus($status) {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE status = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$status]);
            return $stmt->fetchColumn();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /** Hitung total semua produk */
    public function count() {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }
}