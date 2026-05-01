<?php
namespace Models;

class Favorite {
    private $db;
    private $table = 'favorites';

    public function __construct() {
        $this->db = getDB();
    }

    // Cek apakah sudah ada favorit untuk user, produk, platform tertentu
    public function exists($userId, $productId, $platform) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_id = ? AND product_id = ? AND platform = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $productId, $platform]);
        return $stmt->fetchColumn() > 0;
    }

    // Tambah favorit dengan menyimpan platform
    public function add($userId, $productId, $platform) {
        if ($this->exists($userId, $productId, $platform)) {
            return false;
        }
        $sql = "INSERT INTO {$this->table} (user_id, product_id, platform, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $productId, $platform]);
    }

    // Hapus favorit berdasarkan user, produk, dan platform
    public function remove($userId, $productId, $platform) {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ? AND product_id = ? AND platform = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $productId, $platform]);
    }

    // Ambil favorit user, dengan harga dari platform yang disimpan
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
        $result = [];
        foreach ($rows as $row) {
            $platform = $row['platform'] ?: 'tokopedia';
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